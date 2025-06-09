<script setup>
  import { ref, watch, onMounted } from 'vue'
  import axios from 'axios'
  import Modal from '@/components/common/Modal.vue'
  import { 
    X,
    Pencil
  } from "lucide-vue-next"

  const isProfileAddressModal = ref(false)

  const form = ref({
    country: '',
    city_state: '',
    postal_code: '',
    tax_id: ''
  })

  const saveProfile = async () => {
    try {
      await axios.post('/user/address', form.value)
      isProfileAddressModal.value = false
    } catch (error) {
      alert('Failed to update profile')
      console.error(error)
    }
  }

  const loadProfileAddress = async () => {
    try {
      const response = await axios.get('/user/address/show')
      const data = response.data
      form.value.country = data.country;
      form.value.city_state = data.city_state;
      form.value.postal_code = data.postal_code;
      form.value.tax_id = data.tax_id;
    } catch (error) {
      console.error('Failed to load profile address:', error)
    }
  }

  onMounted(() => {
    loadProfileAddress()
  })

  watch(isProfileAddressModal, (newVal) => {
    if (newVal) loadProfileAddress()
  })
</script>

<template>
  <div>
    <div class="p-5 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
      <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
        <div>
          <h4 class="text-md font-semibold text-gray-800 dark:text-white/90 lg:mb-6">Address</h4>

          <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 lg:gap-7 2xl:gap-x-32">
            <div>
              <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Country</p>
              <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ form.country }}</p>
            </div>

            <div>
              <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">City/State</p>
              <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                {{ form.city_state }}
              </p>
            </div>

            <div>
              <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">
                Postal Code
              </p>
              <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ form.postal_code }}</p>
            </div>

            <div>
              <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">TAX ID</p>
              <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ form.tax_id }}</p>
            </div>
          </div>
        </div>

        <button
          @click="isProfileAddressModal = true"
          class="inline-flex items-center gap-2 rounded-full border border-dashed border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.03]">
          <Pencil class="w-3 h-3" />
          Edit
        </button>
      </div>
    </div>
    <Modal v-if="isProfileAddressModal" @close="isProfileAddressModal = false">
      <template #body>
        <div class="no-scrollbar relative w-full max-w-[700px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11">
          <button
            @click="isProfileAddressModal = false"
            class="transition-color absolute right-5 top-5 z-999 flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-600 dark:bg-gray-700 dark:bg-white/[0.05] dark:text-gray-400 dark:hover:bg-white/[0.07] dark:hover:text-gray-300" >
            <X class="w-5 h-5" />
          </button>
          <div class="px-2 pr-14">
            <h4 class="mb-2 text-lg font-semibold text-gray-800 dark:text-white/90">
              Edit Address
            </h4>
            <p class="mb-6 text-xs text-gray-500 dark:text-gray-400 lg:mb-7">
              Update your details to keep your profile up-to-date.
            </p>
          </div>
          <form class="flex flex-col">
            <div class="px-2 overflow-y-auto custom-scrollbar">
              <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
                <div>
                  <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-400">
                    Country
                  </label>
                  <input
                    type="text"
                    value=""
                    v-model="form.country"
                    class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                    required />
                </div>

                <div>
                  <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-400">
                    City/State
                  </label>
                  <input
                    type="text"
                    value=""
                    v-model="form.city_state"
                    class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                    required />
                </div>

                <div>
                  <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-400">
                    Postal Code
                  </label>
                  <input
                    type="text"
                    value=""
                    v-model="form.postal_code"
                    class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                    required />
                </div>

                <div>
                  <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-400">
                    TAX ID
                  </label>
                  <input
                    type="text"
                    value=""
                    v-model="form.tax_id"
                    class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"/>
                </div>
              </div>
            </div>
            <div class="flex items-center gap-3 mt-6 lg:justify-end">
              <button
                @click="isProfileAddressModal = false"
                type="button"
                class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto">
                Close
              </button>
              <button
                @click.prevent="saveProfile"
                type="submit"
                class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">
                Save Changes
              </button>
            </div>
          </form>
        </div>
      </template>
    </Modal>
  </div>
</template>
