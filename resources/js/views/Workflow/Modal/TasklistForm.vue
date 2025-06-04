<script setup lang="ts">	
	import { ref, onMounted, watch } from 'vue'
	import axios from 'axios'
	import { priorities, privacies } from '@/stores/data.js'
	import { tags as allTags } from '@/stores/allTags'
	import Modal from '@/components/common/Modal.vue'
	import Input from '@/components/common/Input.vue'
	import SingleSelect from '@/components/common/SingleSelect.vue'
	import MultiSelect from '@/components/common/MultiSelect.vue'
	import { 
	  X
	} from "lucide-vue-next"

	const props = defineProps<{
	  isOpen: boolean,
	  project?: { id: number; name: string } | null
	}>()

	const emit = defineEmits<{
	  (e: 'close'): void,
	  (e: 'created'): void,
	}>()
	
	const selectedProject = ref<{ id: number; name: string } | null>(null)
	const projectOptions = ref<{ label: string; value: number }[]>([])
	const tags = ref<{ id?: string; label: string; color: string }[]>([])
	const selectedPriority = ref<string | null>(null)
	const selectedPrivacy = ref<string | null>(null)
	const selectedTags = ref<{ id?: string; label: string; color: string }[]>([])
	const tasklistName = ref<string>('')
	const tasklistDesc = ref<string>('')

	const submitForm = async () => {
	  try {
	    const projectId = props.project?.id ?? selectedProject.value;
	    const payload = {
	      name: tasklistName.value,
	      description: tasklistDesc.value,
	      priority: selectedPriority.value,
	      privacy: selectedPrivacy.value,
	      tags: selectedTags.value.map(tag => ({
	        id: tag.id || null,
	        label: tag.label,
	        color: tag.color,
	      })),
	    };

	    await axios.post(`/api/projects/${projectId}/tasklists`, payload);
	    resetForm();
	    onTasklistCreated();
	  } catch (error) {
	    console.error('Failed to create tasklist:', error);
	  }
	}

	const onTasklistCreated = () => {
	  emit('created')
	  emit('close')
	}

	async function loadInitialData() {
	  try {
	    const [projectRes] = await Promise.all([
	      axios.get('/api/projects')
	    ])

	    projectOptions.value = projectRes.data.map(p => ({ label: p.name, value: p.id }))
	  } catch (error) {
	    console.error('Failed to load initial data:', error)
	  }
	}

	function resetForm() {
	  selectedProject.value = null
	  tasklistName.value = ''
	  tasklistDesc.value = ''
	  selectedPriority.value = null
	  selectedPrivacy.value = null
	  selectedTags.value = []
	}

	onMounted(() => {
	  loadInitialData()
	  tags.value = allTags
	  
	  if (props.project) {
		 	selectedProject.value = {
		   	label: props.project.name,
		   	value: props.project.id
		 	}
		}
	})
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
	          Create Tasklist
	        </h4>
	        <p class="mb-4 text-sm text-gray-500 dark:text-gray-400 lg:mb-4">
	          Create a new tasklist by providing essential details.
	        </p>
	      </div>
	      <form class="flex flex-col" @submit.prevent="submitForm">
	        <div class="px-2 overflow-y-auto custom-scrollbar">
	          <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
	            <div class="col-span-full">
	            	<div v-if="project">
	            		<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
	            		  Assign to Project
	            		</label>
	            		<span class="text-md font-medium text-gray-800 dark:text-white/90">• {{ props.project?.name }}</span>
	            	</div>
            		<div v-else>
            			<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            			  Project
            			</label>
            			<SingleSelect 
            				v-model="selectedProject"
          				  :options="projectOptions"
          				  placeholder="Select project" />
            		</div>
	            </div>

	            <div class="col-span-full">
	            	<Input 
	            		v-model="tasklistName"
            		  label="Task Name"
            		  placeholder="Enter Task Name"
            		  required />
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
            				    :options="priorities" />	            			  
	            			</div>
	            		</div>

	            		<div class="col-span-3">
	            			<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
	            			  Privacy
	            			</label>
	            			<div class="relative z-20 bg-transparent">
	            				<SingleSelect
            				    v-model="selectedPrivacy"
            				    :options="privacies" />	            			  
	            			</div>
	            		</div>

	            		<div class="col-span-6">
	            			<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
	            			  Tags
	            			</label>
	            			<div class="relative z-20 bg-transparent">
	            				<MultiSelect
            				    v-model="selectedTags"
            				    :options="tags"/>	            			  
	            			</div>
	            		</div>
	            	</div>
	            </div>

	            <div class="col-span-full">
	            	<textarea
	            		v-model="tasklistDesc"
	            	  placeholder="Add description..."
	            	  rows="6"
	            	  class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
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
	            Create Tasklist
	          </button>
	        </div>
	      </form>
	    </div>
	  </template>
	</Modal>
</template>
