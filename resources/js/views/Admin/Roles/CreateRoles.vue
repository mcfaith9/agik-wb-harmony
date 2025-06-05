<script setup lang="ts">	
	import { ref, reactive } from 'vue'
	import Modal from '@/components/common/Modal.vue'
	import Input from '@/components/common/Input.vue'
	import { useRoles } from '@/composables/useRoles'
	import { 
	  X
	} from "lucide-vue-next"

	const props = defineProps<{
	  isOpen: boolean
	}>()

	const emit = defineEmits<{
	  (e: 'close'): void,
	  (e: 'created'): void,
	}>()

	const attributeOptions = [
	  { key: 'can_view', label: 'Can View' },
	  { key: 'can_edit', label: 'Can Edit' },
	  { key: 'can_delete', label: 'Can Delete' },
	  { key: 'can_create', label: 'Can Create' },
	  { key: 'can_approve', label: 'Can Approve' },
	  { key: 'can_export', label: 'Can Export' },
	]
	
	const { getRoles, createRole } = useRoles()
	const roles = ref([])
	const newRole = ref({ name: '', label: '', attributes: {} })
	const attributes = reactive<Record<string, boolean>>({})

	attributeOptions.forEach(opt => {
	  attributes[opt.key] = false
	})

	const addRole = async () => {
	  try {
	    await createRole({
	      name: newRole.value.name,
	      label: newRole.value.label,
	      attributes: { ...attributes }
	    })

	    newRole.value = { name: '', label: '', attributes: {} }
	    
	    attributeOptions.forEach(opt => {
	      attributes[opt.key] = false
	    })

	    const res = await getRoles()
	    roles.value = res.data
	    emit('created')
	    emit('close')
	  } catch (err) {
	    alert('Failed to create role.')
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
	          Add Role
	        </h4>
	        <p class="mb-4 text-sm text-gray-500 dark:text-gray-400 lg:mb-4">
	          Define a unique role, name it, label it, and pick the permissions that power your users.
	        </p>
	      </div>
	      <form class="flex flex-col" @submit.prevent="addRole">
	        <div class="px-2 overflow-y-auto custom-scrollbar">
	          <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
	            <div class="col-span-full">
	            	<Input 
	            		v-model="newRole.name"
            		  label="Role"
            		  placeholder="e.g superadmin"
            		  required />
	            </div>

	            <div class="col-span-full">
	            	<Input 
	            		v-model="newRole.label"
            		  label="Label"
            		  placeholder="e.g Super Admin"
            		  required />
	            </div>

	            <div class="col-span-full">
	            	<label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
	            	  Attributes
	            	</label>	            		              
	              <div class="flex flex-wrap gap-4">
	                <label
	                	v-for="attr in attributeOptions"
	                	:key="attr.key"
	                  :for="`checkbox-${attr.key}`"
	                  class="flex items-center text-sm font-medium text-gray-700 cursor-pointer select-none dark:text-gray-400">
	                  <div class="relative">
	                    <input type="checkbox" :id="`checkbox-${attr.key}`" v-model="attributes[attr.key]" class="sr-only" />
	                    <div 
	                    	:class="attributes[attr.key] 
	                    	  ? 'border-brand-500 bg-brand-500' 
	                    	  : 'bg-transparent border-gray-300 dark:border-gray-700'"
	                    	class="mr-3 flex h-5 w-5 items-center justify-center rounded-md border-[1.25px] hover:border-brand-500 dark:hover:border-brand-500">
	                      <span v-if="attributes[attr.key]">
                          <svg
                            width="14"
                            height="14"
                            viewBox="0 0 14 14"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                              d="M11.6666 3.5L5.24992 9.91667L2.33325 7"
                              stroke="white"
                              stroke-width="1.94437"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                            />
                          </svg>
                        </span>
	                    </div>
	                  </div>
	                  {{ attr.label }}
	                </label>
	              </div>
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
	            Create Role
	          </button>
	        </div>
	      </form>
	    </div>
	  </template>
	</Modal>
</template>
