<script setup lang="ts">
	import { ref, onMounted, watch } from 'vue'
	import axios from 'axios'
	import { priorities, privacies } from '@/stores/data.js'
	import Modal from '@/components/common/Modal.vue'
	import Input from '@/components/common/Input.vue'
	import SingleSelect from '@/components/common/SingleSelect.vue'
	import MultiSelect from '@/components/common/MultiSelect.vue'
	import AssigneeSelector from '@/components/common/AssigneeSelector.vue'
	import flatPickr from 'vue-flatpickr-component'
	import 'flatpickr/dist/themes/airbnb.css'

	import { X, CalendarRange } from "lucide-vue-next"

	interface Task {
	  id?: number | string
	  name?: string
	  description?: string
	  estimated_time?: string
	  priority?: string | null
	  privacy?: string | null
	  task_list_id?: number | string | null
	  tasklist?: { project_id?: number | string }
	  start_date?: string | null
	  end_date?: string | null
	  users?: { id: string; name: string }[]
	  tags?: { id?: string; label: string; color: string }[]
	}

	const props = defineProps<{
	  isOpen: boolean
	  task: Task | null
	}>()

	const emit = defineEmits<{
	  (e: 'close'): void,
	  (e: 'task-created', task: any): void,
	  (e: 'task-updated', task: any): void,
	}>()

	// Reactive form fields
	const taskName = ref('')
	const estimated_time = ref('')
	const taskDesc = ref('')
	const selectedPriority = ref<string | null>(null)
	const selectedPrivacy = ref<string | null>(null)
	const projectOptions = ref<{ label: string; value: string }[]>([])
	const selectedProject = ref<string | null>(null)
	const tasklistOptions = ref<{ label: string; value: string }[]>([])
	const selectedTasklist = ref<string | null>(null)

	const tags = ref<{ id?: string; label: string; color: string }[]>([])
	const users = ref<{ id: string; name: string }[]>([])
	const selectedUsers = ref<{ id: string; first_name: string, last_name: string }[]>([])
	const selectedTags = ref<{ id?: string; label: string; color: string }[]>([])

	const selectedDateRange = ref<string | null>(null)

	const flatpickrConfig = {
	  mode: "range",
	  dateFormat: 'Y-m-d',
	  altInput: true,
	  altFormat: 'F j, Y',
	  wrap: true,
	}

	function formatDate(val: string | null): [string | null, string | null] {
	  if (!val || !val.includes(' to ')) return [null, null]
	  const [startStr, endStr] = val.split(' to ')
	  return [startStr, endStr]
	}

	async function loadInitialData() {
	  try {
	    const [projectRes, userRes, tagRes] = await Promise.all([
	      axios.get('/api/projects'),
	      axios.get('/api/users'),
	      axios.get('/api/tags'),
	    ])

	    projectOptions.value = projectRes.data.map((p: any) => ({
	      label: p.name,
	      value: p.id.toString(),
	    }))
	    users.value = userRes.data
	    tags.value = tagRes.data
	  } catch (error) {
	    console.error('Failed to load initial data:', error)
	  }
	}

	// Load projects, users, tags once
	onMounted(() => {
	  loadInitialData()
	})

	// Load tasklists when selectedProject changes
	watch(selectedProject, async (newProjectId) => {
	  if (!newProjectId) {
	    tasklistOptions.value = []
	    selectedTasklist.value = null
	    return
	  }
	  try {
	    const res = await axios.get(`/api/projects/${newProjectId}/tasklists`)
	    tasklistOptions.value = res.data.map((tl: any) => ({
	      label: tl.name,
	      value: tl.id.toString(),
	    }))
	    // Don't reset selectedTasklist here to preserve user selection or editing value
	  } catch (error) {
	    console.error('Failed to fetch tasklists:', error)
	    tasklistOptions.value = []
	    selectedTasklist.value = null
	  }
	}, { immediate: true })

	function resetForm() {
	  taskName.value = ''
	  taskDesc.value = ''
	  estimated_time.value = ''
	  selectedPriority.value = null
	  selectedPrivacy.value = null
	  selectedProject.value = null
	  selectedTasklist.value = null
	  selectedDateRange.value = null
	  selectedUsers.value = []
	  selectedTags.value = []
	  tasklistOptions.value = []
	}

	function populateForm(task: Task) {
	  taskName.value = task.name || ''
	  taskDesc.value = task.description || ''
	  estimated_time.value = task.estimated_time || ''
	  selectedPriority.value = task.priority || null
	  selectedPrivacy.value = task.privacy || null
	  selectedProject.value = task.tasklist?.project_id?.toString() || null

	  if (selectedProject.value) {
	    axios.get(`/api/projects/${selectedProject.value}/tasklists`)
	      .then(res => {
	        tasklistOptions.value = res.data.map((tl: any) => ({
	          label: tl.name,
	          value: tl.id.toString(),
	        }))
	        selectedTasklist.value = task.task_list_id?.toString() || null
	      })
	      .catch(() => {
	        tasklistOptions.value = []
	        selectedTasklist.value = null
	      })
	  } else {
	    tasklistOptions.value = []
	    selectedTasklist.value = null
	  }

	  selectedDateRange.value = (task.start_date && task.end_date)
	    ? `${task.start_date.slice(0, 10)} to ${task.end_date.slice(0, 10)}`
	    : null

	  selectedUsers.value = (task.users || []).map(user => ({
	    id: user.id,
	    first_name: user.first_name,
	    last_name: user.last_name
	  }))

	  selectedTags.value = (task.tags || []).map(tag => ({
	    id: tag.id ?? undefined,
	    label: tag.label,
	    color: tag.color,
	  }))
	}

	// Populate form when editing a task
	watch(() => props.task, (task) => {
	  if (!task) {
	    resetForm()
	  } else {
	    populateForm(task)
	  }
	}, { immediate: true })

	// Reset form when modal closes
	watch(() => props.isOpen, (newVal) => {
	  if (!newVal) {
	    resetForm()
	  } else {
	    // Modal opened: repopulate from props.task if exists
	    if (props.task) {
	      // Trigger the same code you do in the task watcher
	      populateForm(props.task)
	    }
	  }
	})

	const handleSubmit = async () => {
	  if (!taskName.value || !selectedProject.value || !selectedTasklist.value) {
	    alert('Please fill in required fields: Task Name, Project, and Task List.')
	    return
	  }

	  const [start, end] = formatDate(selectedDateRange.value)

	  const payload = {
	    name: taskName.value,
	    description: taskDesc.value,
	    project_id: selectedProject.value,
	    task_list_id: selectedTasklist.value,
	    priority: selectedPriority.value,
	    privacy: selectedPrivacy.value,
	    tags: selectedTags.value.map(tag => ({
	      id: tag.id || null,
	      label: tag.label,
	      color: tag.color,
	    })),
	    estimated_time: estimated_time.value,
	    start_date: start,
	    end_date: end,
	    user_ids: selectedUsers.value.map(user => user.id),
	  }

	  try {
	    if (props.task && props.task.id) {
	      const response = await axios.put(`/api/tasks/${props.task.id}`, payload)
	      emit("task-updated", response.data)
	    } else {
	      const response = await axios.post('/api/tasks', payload)
	      emit("task-created", response.data)
	    }
	    emit("close")
	  } catch (error) {
	    console.error('Task submission failed:', error)
	    alert('Failed to submit the task. Please try again.')
	  }
	}
</script>

<template>
	<Modal v-if="isOpen" @close="emit('close')">
	  <template #body>
	    <div class="no-scrollbar relative w-full max-w-[700px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11">
	      <button
	        @click="emit('close')"
	        class="transition-color absolute right-5 top-5 z-999 flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-600 dark:bg-gray-700 dark:bg-white/[0.05] dark:text-gray-400 dark:hover:bg-white/[0.07] dark:hover:text-gray-300">
	        <X class="w-5 h-5" />
	      </button>
	      <div class="px-2 pr-14">
	        <h4 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90">
	          {{ props.task ? 'Edit Task' : 'Add Task' }}
	        </h4>
	        <p class="mb-4 text-sm text-gray-500 dark:text-gray-400 lg:mb-4">
	          {{ props.task ? 'Update the task details below.' : 'Add a new task and assign it to a project or team member.' }}
	        </p>
	      </div>
	      <form class="flex flex-col" @submit.prevent="handleSubmit">
	        <div class="px-2 overflow-y-auto custom-scrollbar">
	          <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
	            <div>
	              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
	                Projects
	              </label>
	              <div class="relative z-20 bg-transparent">
	                <SingleSelect 
	                	v-model="selectedProject" 
	                	:options="projectOptions"
	                	placeholder="Select Project"
	                	required />
	              </div>
	            </div>

	            <div>
	              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
	                Task List
	              </label>
	              <div class="relative z-20 bg-transparent">
	                <SingleSelect 
	                  v-model="selectedTasklist" 
	                  :options="tasklistOptions" 
	                  placeholder="Select Task" 
	                  required />
	              </div>
	            </div>

	            <div class="col-span-full">
	            	<Input 
	            		v-model="taskName"
            		  label="Task Name"
            		  placeholder="Enter Task Name"
            		  required />
	            </div>

	            <div class="col-span-full">
	            	<div class="grid grid-cols-12 gap-x-6">
	            		<div class="col-span-3">
	            			<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            			    Assignees
            			  </label>
            			  <AssigneeSelector v-model="selectedUsers" :users="users" />
	            		</div> 

	            		<div class="col-span-6">
	            			<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
	            			  Dates
	            			</label>
	            			<div class="relative">
		            			<flat-pickr
		            				v-model="selectedDateRange"
		            			  :config="flatpickrConfig"
		            			  class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pl-4 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
		            			  placeholder="Select date"/>
	            			  <span class="absolute text-gray-500 -translate-y-1/2 pointer-events-none right-3 top-1/2 dark:text-gray-400">
	            			  	<CalendarRange class="w-4 h-4" />
	            			  </span>
	            			</div>
	            		</div>

	            		<div class="col-span-3">
	            			<Input 
			            		v-model="estimated_time"
		            		  label="Estimated Time"
		            		  placeholder="e.g 1h" />
	            		</div> 
	            	</div>          	
	            </div>

	            <div class="col-span-full">
	            	<div class="grid grid-cols-12 gap-x-6">
	            		<div class="col-span-3">
	            			<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
	            			  Priority
	            			</label>
	            			<div class="relative z-20 bg-transparent">
	            				<SingleSelect
            				    v-model="selectedPriority"
            				    :options="priorities"
            				    placeholder=""/>	            			  
	            			</div>
	            		</div>

	            		<div class="col-span-3">
	            			<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
	            			  Privacy
	            			</label>
	            			<div class="relative z-20 bg-transparent">
	            				<SingleSelect
            				    v-model="selectedPrivacy"
            				    :options="privacies"
            				    placeholder=""/>	            			  
	            			</div>
	            		</div>

	            		<div class="col-span-6">
	            			<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
	            			  Tags
	            			</label>
	            			<div class="relative z-20 bg-transparent">
	            				<MultiSelect
            				    v-model="selectedTags"
            				    :options="tags" />	            			  
	            			</div>
	            		</div>
	            	</div>
	            </div>

	            <div class="col-span-full">
	            	<textarea
	            		v-model="taskDesc"
	            	  placeholder="Add description..."
	            	  rows="6"
	            	  class="custom-scrollbar dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
	            	></textarea>
	            </div>

	          </div>	          
	        </div>
	        <div class="flex items-center gap-3 mt-4 lg:justify-end">
	          <button
	            @click="emit('close')"
	            type="button"
	            class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto">
	            Close
	          </button>
	          <button
	            type="submit"
	            class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">
	            {{ props.task ? 'Update Task' : 'Create Task' }}
	          </button>
	        </div>
	      </form>
	    </div>
	  </template>
	</Modal>
</template>
