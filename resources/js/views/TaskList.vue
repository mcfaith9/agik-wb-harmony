<script setup lang="ts">
import { ref } from "vue"
import PageBreadcrumb from "@/components/common/PageBreadcrumb.vue"
import AdminLayout from "@/components/layout/AdminLayout.vue"
import Modal from '@/components/common/Modal.vue'
import { 
	Settings2, 
	CircleFadingPlus,
} from "lucide-vue-next"

const currentPageTitle = ref("Task List")
const addTaskModal = ref(false)
</script>

<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
      <div class="flex flex-col items-center px-4 py-5 xl:px-6 xl:py-6">
        <div class="flex flex-col w-full gap-5 sm:justify-between xl:flex-row xl:items-center">

          <div class="flex flex-wrap items-center gap-x-1 gap-y-2 rounded-lg bg-gray-100 p-0.5 dark:bg-gray-900">
            <button class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-md h group hover:text-gray-900 dark:hover:text-white text-gray-900 dark:text-white bg-white dark:bg-gray-800">
              All Tasks
              <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium bg-brand-50 text-brand-500 dark:bg-brand-500/15 dark:text-brand-400">23</span>
            </button>
            <button class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-md h group hover:text-gray-900 dark:hover:text-white text-gray-500 dark:text-gray-400">
              To do
              <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium bg-white dark:bg-white/[0.03]">3</span>
            </button>
            <button class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-md h group hover:text-gray-900 dark:hover:text-white text-gray-500 dark:text-gray-400">
              Completed
              <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium bg-white dark:bg-white/[0.03]">14</span>
            </button>
          </div>

          <div class="flex flex-wrap items-center gap-3 xl:justify-end">
            <button class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.03]">
              <Settings2 class="w-4 h-4" />
              Filter & Sort
            </button>
            <button @click="addTaskModal = true" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">
              Add New Task
              <CircleFadingPlus class="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 border-t border-gray-200 divide-x divide-gray-200 dark:border-gray-800 mt-7 dark:divide-gray-800 sm:mt-0 sm:grid-cols-2 xl:grid-cols-3">	
        <div class="overflow-hidden">
          <div class="p-4 xl:p-6">
            <div class="flex items-center justify-between mb-1">
              <h3 class="flex items-center gap-3 text-base font-medium text-gray-800 dark:text-white/90">
                To Do
                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-700 dark:bg-white/[0.03] dark:text-white/80">3</span>
              </h3>
            </div>

            <div class="min-h-[200px] space-y-5 mt-5">
              <div draggable="true" class="p-5 bg-white border border-gray-200 task rounded-xl shadow-theme-sm dark:border-gray-800 dark:bg-white/5">
                <div class="flex items-start justify-between gap-6">
                  <div>
                  	<h4 class="mb-5 text-base text-gray-800 dark:text-white/90">Finish user onboarding</h4>
                  	<div class="flex items-center gap-3">
                  	  <span class="flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400">
                  	    📅 Tomorrow
                  	  </span>
                  	  <span class="flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400">
                  	    🕓 2h
                  	  </span>
                  	</div>
                  	<span class="px-2 py-0.5 text-xs font-medium bg-orange-400/10 text-orange-400 mt-3 inline-flex rounded-full px-2 py-0.5 text-xs font-medium">Development</span>
                  </div>
                  <div class="h-6 w-6 shrink-0 overflow-hidden rounded-full border-[0.5px] border-gray-200 dark:border-gray-800">
                  	<img src="@/images/user/user-07.jpg" alt="Finish user onboarding">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="overflow-hidden">
          <div class="p-4 xl:p-6">
            <div class="flex items-center justify-between mb-1">
              <h3 class="flex items-center gap-3 text-base font-medium text-gray-800 dark:text-white/90">
                In Progress
                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-700 dark:bg-white/[0.03] dark:text-white/80">4</span>
              </h3>
            </div>

            <div class="min-h-[200px] space-y-5 mt-5">
              <div draggable="true" class="p-5 bg-white border border-gray-200 task rounded-xl shadow-theme-sm dark:border-gray-800 dark:bg-white/5">
                <div class="flex items-start justify-between gap-6">
                  <div>
                  	<h4 class="mb-5 text-base text-gray-800 dark:text-white/90">Finish user onboarding</h4>
                  	<div class="flex items-center gap-3">
                  	  <span class="flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400">
                  	    📅 Tomorrow
                  	  </span>
                  	  <span class="flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400">
                  	    🕓 2h
                  	  </span>
                  	</div>
                  </div>
                  <div class="h-6 w-6 shrink-0 overflow-hidden rounded-full border-[0.5px] border-gray-200 dark:border-gray-800">
                  	<img src="@/images/user/user-07.jpg" alt="Finish user onboarding">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="overflow-hidden">
          <div class="p-4 xl:p-6">
            <div class="flex items-center justify-between mb-1">
              <h3 class="flex items-center gap-3 text-base font-medium text-gray-800 dark:text-white/90">
                Completed
                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-700 dark:bg-white/[0.03] dark:text-white/80">4</span>
              </h3>
            </div>

            <div class="min-h-[200px] space-y-5 mt-5">
              <div draggable="true" class="p-5 bg-white border border-gray-200 task rounded-xl shadow-theme-sm dark:border-gray-800 dark:bg-white/5">
                <div class="flex items-start justify-between gap-6">
                  <div>
                  	<h4 class="mb-5 text-base text-gray-800 dark:text-white/90">Finish user onboarding</h4>
                  	<div class="flex items-center gap-3">
                  	  <span class="flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400">
                  	    📅 Tomorrow
                  	  </span>
                  	  <span class="flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400">
                  	    🕓 2h
                  	  </span>
                  	</div>
                  </div>
                  <div class="h-6 w-6 shrink-0 overflow-hidden rounded-full border-[0.5px] border-gray-200 dark:border-gray-800">
                  	<img src="@/images/user/user-07.jpg" alt="Finish user onboarding">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <Modal v-if="addTaskModal" @close="addTaskModal = false">
      <template #body>
        <div
          class="no-scrollbar relative w-full max-w-[700px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11">
          <!-- close btn -->
          <button
            @click="addTaskModal = false"
            class="transition-color absolute right-5 top-5 z-999 flex h-11 w-11 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-600 dark:bg-gray-700 dark:bg-white/[0.05] dark:text-gray-400 dark:hover:bg-white/[0.07] dark:hover:text-gray-300">
            <svg
              class="fill-current"
              width="24"
              height="24"
              viewBox="0 0 24 24"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M6.04289 16.5418C5.65237 16.9323 5.65237 17.5655 6.04289 17.956C6.43342 18.3465 7.06658 18.3465 7.45711 17.956L11.9987 13.4144L16.5408 17.9565C16.9313 18.347 17.5645 18.347 17.955 17.9565C18.3455 17.566 18.3455 16.9328 17.955 16.5423L13.4129 12.0002L17.955 7.45808C18.3455 7.06756 18.3455 6.43439 17.955 6.04387C17.5645 5.65335 16.9313 5.65335 16.5408 6.04387L11.9987 10.586L7.45711 6.04439C7.06658 5.65386 6.43342 5.65386 6.04289 6.04439C5.65237 6.43491 5.65237 7.06808 6.04289 7.4586L10.5845 12.0002L6.04289 16.5418Z"
                fill=""
              />
            </svg>
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
                    Country
                  </label>
                  <input
                    type="text"
                    value=""
                    class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                    required />
                </div>
              </div>
            </div>
            <div class="flex items-center gap-3 mt-6 lg:justify-end">
              <button
                @click="addTaskModal = false"
                type="button"
                class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto"
              >
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
  </AdminLayout>
</template>
