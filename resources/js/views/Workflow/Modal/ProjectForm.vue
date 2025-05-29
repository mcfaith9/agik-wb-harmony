<script setup lang="ts">	
	import { ref, onMounted } from 'vue'
	import axios from 'axios'
	import { priorities, privacies } from '@/stores/data.js'
	import Modal from '@/components/common/Modal.vue'
	import Input from '@/components/common/Input.vue'
	import SingleSelect from '@/components/common/SingleSelect.vue'
	import MultiSelect from '@/components/common/MultiSelect.vue'
	import { 
	  X,
	  ChevronDown,
	  UserRoundPlus,
	} from "lucide-vue-next"

	const props = defineProps<{
	  isOpen: boolean
	}>()

	const emit = defineEmits<{
	  (e: 'close'): void,
	  (e: 'created'): void,
	}>()
	
	const tags = ref<{ id?: string; label: string; color: string }[]>([])
	const selectedPriority = ref<string | null>(null)
	const selectedPrivacy = ref<string | null>(null)
	const selectedTags = ref<{ id?: string; label: string; color: string }[]>([])
	const projectName = ref<string>('')
	const projectDesc = ref<string>('')

	const submitForm = async () => {
	  try {
	    const payload = {
	      name: projectName.value,
	      description: projectDesc.value,
	      priority: selectedPriority.value,
	      privacy: selectedPrivacy.value,
	      tags: selectedTags.value.map(tag => tag.value),
	    }

	    await axios.post('/api/projects', payload)

	    emit('close')
	    emit('created')
	  } catch (error) {
	    console.error('Failed to create project:', error)
	  }
	}

	async function loadInitialData() {
	  try {
	    const [tagRes] = await Promise.all([
	      axios.get('/api/tags'),
	    ])

	    tags.value = tagRes.data
	  } catch (error) {
	    console.error('Failed to load initial data:', error)
	  }
	}

	onMounted(() => {
	  loadInitialData()
	})
</script>

<template>
	<Modal v-if="isOpen" @close="isOpen = false">
	  <template #body>
	    <div class="no-scrollbar relative w-full max-w-[700px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11">
	      <button
	        @click="emit('close')"
	        class="transition-color absolute right-5 top-5 z-999 flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-600 dark:bg-gray-700 dark:bg-white/[0.05] dark:text-gray-400 dark:hover:bg-white/[0.07] dark:hover:text-gray-300">
	        <X class="w-5 h-5" />
	      </button>
	      <div class="px-2 pr-14">
	        <h4 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90">
	          Add Project
	        </h4>
	        <p class="mb-4 text-sm text-gray-500 dark:text-gray-400 lg:mb-4">
	          Create a new project by providing essential details.
	        </p>
	      </div>
	      <form class="flex flex-col" @submit.prevent="submitForm">
	        <div class="px-2 overflow-y-auto custom-scrollbar">
	          <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
	            <div class="col-span-full">
	            	<Input 
	            		v-model="projectName"
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
            				    :options="tags"/>	            			  
	            			</div>
	            		</div>
	            	</div>
	            </div>

	            <div class="col-span-full">
	            	<textarea
	            		v-model="projectDesc"
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
	            Create Project
	          </button>
	        </div>
	      </form>
	    </div>
	  </template>
	</Modal>
</template>
