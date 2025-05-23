<script setup lang="ts">	
	import { ref } from 'vue'
	import Modal from '@/components/common/Modal.vue'
	import flatPickr from 'vue-flatpickr-component'
	import 'flatpickr/dist/themes/airbnb.css'

	import { 
	  X,
	  ChevronDown,
	  UserRoundPlus
	} from "lucide-vue-next"

	const singleSelect = ref('')

	const flatpickrConfig = {
		mode: "range",
	  dateFormat: 'Y-m-d',
	  altInput: true,
	  altFormat: 'F j, Y',
	  wrap: true,
	}

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
	                <select
	                  v-model="singleSelect"
	                  class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
	                  :class="{ 'text-gray-800 dark:text-white/90': singleSelect }">
	                  <option value="" disabled>Select Option</option>
	                  <option value="marketing" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
	                    Marketing
	                  </option>
	                  <option value="template" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
	                    Template
	                  </option>
	                  <option value="development" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
	                    Development
	                  </option>
	                </select>
	                <span class="absolute z-30 text-gray-700 -translate-y-1/2 pointer-events-none right-4 top-1/2 dark:text-gray-400">
	                  <ChevronDown class="w-5 h-5" />
	                </span>
	              </div>
	            </div>

	            <div>
	              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
	                Task List
	              </label>
	              <div class="relative z-20 bg-transparent">
	                <select
	                  v-model="singleSelect"
	                  class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
	                  :class="{ 'text-gray-800 dark:text-white/90': singleSelect }">
	                  <option value="" disabled>Select Option</option>
	                  <option value="marketing" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
	                    Marketing
	                  </option>
	                  <option value="template" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
	                    Template
	                  </option>
	                  <option value="development" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
	                    Development
	                  </option>
	                </select>
	                <span class="absolute z-30 text-gray-700 -translate-y-1/2 pointer-events-none right-4 top-1/2 dark:text-gray-400">
	                  <ChevronDown class="w-5 h-5" />
	                </span>
	              </div>
	            </div>

	            <div class="col-span-full">
	            	<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
	            	  Task Name
	            	</label>
	            	<input
	            	  type="text"
	            	  value=""
	            	  class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
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

	            		<div class="col-span-5">
	            			<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
	            			  Dates
	            			</label>
	            			<flat-pickr
	            			  v-model="date"
	            			  :config="flatpickrConfig"
	            			  class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pl-4 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
	            			  placeholder="Select date"/>
	            		</div>

	            		<div class="col-span-4">
	            			<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
	            			  Estimated Time
	            			</label>
	            			<input
	            			  type="text"
	            			  value=""
	            			  class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
	            			  required />
	            		</div> 
	            	</div>          	
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
	            Save
	          </button>
	        </div>
	      </form>
	    </div>
	  </template>
	</Modal>
</template>
