<script setup lang="ts">	
	import { ref } from 'vue'
	import Modal from '@/components/common/Modal.vue'
	import Input from '@/components/common/Input.vue'
	import SingleSelect from '@/components/common/SingleSelect.vue'
	import MultiSelect from '@/components/common/MultiSelect.vue'
	import flatPickr from 'vue-flatpickr-component'
	import 'flatpickr/dist/themes/airbnb.css'

	import { 
	  X,
	  ChevronDown,
	  UserRoundPlus
	} from "lucide-vue-next"

	const priorities = [
		{ value: 'none', label: 'None' },
	  { value: 'low', label: 'Low' },
	  { value: 'medium', label: 'Medium' },
	  { value: 'high', label: 'High' },
	]
	const selectedPriority = ref<string | null>(null)

	const privacies = [		
	  { value: 'public', label: 'Public' },
	  { value: 'private', label: 'Private' },
	]
	const selectedPrivacy = ref<string | null>(null)

	const projectOptions = [
	  { label: 'Website Redesign', value: 'website-redesign' },
	  { label: 'Mobile App Development', value: 'mobile-app' },
	  { label: 'Marketing Campaign', value: 'marketing-campaign' },
	  { label: 'Customer Support Setup', value: 'customer-support' },
	  { label: 'Data Analysis', value: 'data-analysis' },
	  { label: 'New Product Launch', value: 'product-launch' },
	  { label: 'SEO Optimization', value: 'seo-optimization' }
	]
	const selectedProject = ref<string | null>(null)

	const taskList = [
	  { value: '1', label: 'Design homepage layout' },
	  { value: '2', label: 'Implement login feature' },
	  { value: '3', label: 'Set up database schema' },
	  { value: '4', label: 'Write unit tests' }
	]
	const selectedTasklist = ref<string | null>(null)


	const tags = [
	  { value: 'apple', label: 'Apple' },
	  { value: 'banana', label: 'Banana' },
	  { value: 'cherry', label: 'Cherry' },
	  { value: 'date', label: 'Date' },
	  { value: 'elderberry', label: 'Elderberry' },
	  { value: 'graphs', label: 'Graphs' },
	  { value: 'orange', label: 'Orange' },
	]
	const selectedTags = ref<Array<{ value: string; label: string }>>([])

	const flatpickrConfig = {
		mode: "range",
	  dateFormat: 'Y-m-d',
	  altInput: true,
	  altFormat: 'F j, Y',
	  wrap: true,
	}
	const selectedDateRange = ref<Date[] | null>(null)

	const taskName = ref<string>('')
	const estimate = ref<string>('')
	const taskDesc = ref<string>('')

	const props = defineProps<{
	  isOpen: boolean
	}>()

	const emit = defineEmits<{
	  (e: 'close'): void
	}>()
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
	          Add Task
	        </h4>
	        <p class="mb-6 text-sm text-gray-500 dark:text-gray-400 lg:mb-7">
	          Update your details to keep your profile up-to-date.
	        </p>
	      </div>
	      <form class="flex flex-col">
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
	                	placeholder="Select Project"/>
	              </div>
	            </div>

	            <div>
	              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
	                Task List
	              </label>
	              <div class="relative z-20 bg-transparent">
	                <SingleSelect 
	                  v-model="selectedTasklist" 
	                  :options="taskList" 
	                  placeholder="Select Task" />
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
	            			
	            			<button type="button">
	            			  <div class="flex -space-x-4 rtl:space-x-reverse">
	            			      <img class="w-10 h-10 border-2 border-white rounded-full dark:border-gray-800" src="@/images/user/owner.jpg" alt="">
	            			      <img class="w-10 h-10 border-2 border-white rounded-full dark:border-gray-800" src="@/images/user/owner.jpg" alt="">
	            			      <a class="flex items-center justify-center w-10 h-10 text-xs font-medium text-white bg-gray-700 border-2 border-white rounded-full hover:bg-gray-600 dark:border-gray-800" href="#">+0</a>
	            			  </div>
	            			</button>	
	            		</div> 

	            		<div class="col-span-6">
	            			<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
	            			  Dates
	            			</label>
	            			<flat-pickr
	            				v-model="selectedDateRange"
	            			  :config="flatpickrConfig"
	            			  class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pl-4 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
	            			  placeholder="Select date"/>
	            		</div>

	            		<div class="col-span-3">
	            			<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
	            			  Estimated Time
	            			</label>
	            			<input
	            				v-model="estimate"
	            			  type="text"
	            			  value=""
	            			  placeholder="1h"
	            			  class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
	            			  required />
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
            				    :options="tags"/>	            			  
	            			</div>
	            		</div>
	            	</div>
	            </div>

	            <div class="col-span-full">
	            	<textarea
	            		v-model="taskDesc"
	            	  placeholder="Add description..."
	            	  rows="6"
	            	  class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
	            	></textarea>
	            </div>

	          </div>	          
	        </div>
	        <div class="flex items-center gap-3 mt-6 lg:justify-end">
	          <button
	            @click="emit('close')"
	            type="button"
	            class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto">
	            Close
	          </button>
	          <button
	            type="submit"
	            class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">
	            Create Task
	          </button>
	        </div>
	      </form>
	    </div>
	  </template>
	</Modal>
</template>
