<script setup lang="ts">
import { ref } from "vue"
import PageBreadcrumb from "@/components/common/PageBreadcrumb.vue"
import AdminLayout from "@/components/layout/AdminLayout.vue"
import AddTask from '@/views/Task/AddTask.vue'
import { 
	Settings2, 
	CircleFadingPlus,
  X
} from "lucide-vue-next"

const currentPageTitle = ref("Tasks")
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

    <AddTask :isOpen="addTaskModal" @close="addTaskModal = false" />
  </AdminLayout>
</template>
