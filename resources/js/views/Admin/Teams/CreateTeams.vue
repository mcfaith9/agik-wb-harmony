<script setup lang="ts">
	import { ref } from 'vue'
	import Modal from '@/components/common/Modal.vue'
	import Input from '@/components/common/Input.vue'
	import { X } from "lucide-vue-next"
	import { useTeams } from '@/composables/useTeams'

	const props = defineProps<{
	  isOpen: boolean
	}>()

	const emit = defineEmits<{
	  (e: 'close'): void,
	  (e: 'created'): void,
	}>()

	const { createTeam } = useTeams()
	const newTeam = ref({ name: '' })
	const errorMessage = ref('')

	const addTeam = async () => {
		errorMessage.value = ''

	  try {
	    await createTeam({
	      name: newTeam.value.name,
	    })

	    newTeam.value.name = ''
	    emit('created')
	    emit('close')
	  } catch (err: any) {
	    if (err.response && err.response.status === 422) {
	      errorMessage.value = 'Team name already exist/taken'
	    } else {
	      errorMessage.value = 'Something went wrong. Please try again.'
	    }
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
            Add Team
          </h4>
          <p class="mb-4 text-sm text-gray-500 dark:text-gray-400 lg:mb-4">
            Name your team and create it to start organizing projects and people.
          </p>
        </div>
        <form class="flex flex-col" @submit.prevent="addTeam">
          <div class="px-2 overflow-y-auto custom-scrollbar">
            <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
              <div class="col-span-full">
                <Input 
                  v-model="newTeam.name"
                  label="Team Name"
                  placeholder="e.g. Engineering, Marketing, QA"
                  :error="errorMessage"
                  required />
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
              Create Team
            </button>
          </div>
        </form>
      </div>
    </template>
  </Modal>
</template>
