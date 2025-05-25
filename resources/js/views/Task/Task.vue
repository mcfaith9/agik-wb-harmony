<script setup lang="ts">
  import { onMounted, ref, computed } from "vue"
  import axios from "axios"
  import PageBreadcrumb from "@/components/common/PageBreadcrumb.vue"
  import AdminLayout from "@/components/layout/AdminLayout.vue"
  import AddTask from "@/views/Task/AddTask.vue"
  import TaskColumn from "@/views/Task/TaskColumn.vue"
  import { 
  	Settings2, 
  	CircleFadingPlus,
    X
  } from "lucide-vue-next"

  const currentPageTitle = ref("Tasks")
  const addTaskModal = ref(false)
  const tasks = ref([])

  onMounted(async () => {
    const res = await axios.get("/api/tasks")
    tasks.value = res.data
  })

  const todoTasks = computed(() =>
    tasks.value.filter(task => task.status === "todo")
  )

  const inProgressTasks = computed(() =>
    tasks.value.filter(task => task.status === "in_progress")
  )

  const completedTasks = computed(() =>
    tasks.value.filter(task => task.status === "completed")
  )

  const handleTaskDrop = async ({ task, newStatus }) => {
    if (task.status === newStatus) return;

    try {
      await axios.put(`/api/tasks/${task.id}`, { status: newStatus });

      const t = tasks.value.find(t => t.id === task.id);
      if (t) t.status = newStatus;

      // Force update reactivity to refresh UI
      tasks.value = [...tasks.value];
    } catch (e) {
      console.error("Error updating task status", e);
    }
  }
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
              <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium bg-brand-50 text-brand-500 dark:bg-brand-500/15 dark:text-brand-400">{{ tasks.length }}</span>
            </button>
            <button class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-md h group hover:text-gray-900 dark:hover:text-white text-gray-500 dark:text-gray-400">
              To do
              <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium bg-white dark:bg-white/[0.03]">{{ todoTasks.length }}</span>
            </button>
            <button class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-md h group hover:text-gray-900 dark:hover:text-white text-gray-500 dark:text-gray-400">
              Completed
              <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium bg-white dark:bg-white/[0.03]">{{ completedTasks.length }}</span>
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
        <div class="overflow-hidden" id="parentTodo">
          <div class="p-4 xl:p-6">
            <div class="flex items-center justify-between mb-1">
              <h3 class="flex items-center gap-3 text-base font-medium text-gray-800 dark:text-white/90">
                To Do
                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-700 dark:bg-white/[0.03] dark:text-white/80">{{ todoTasks.length }}</span>
              </h3>
            </div>

            <div class="overflow-y-auto max-h-[600px] p-4 xl:p-6 custom-scrollbar space-y-5 mt-5">
              <TaskColumn :all-tasks="tasks" status="todo" @update-status="handleTaskDrop" />
            </div>
          </div>
        </div>

        <div class="overflow-hidden" id="parentInProgress">
          <div class="p-4 xl:p-6">
            <div class="flex items-center justify-between mb-1">
              <h3 class="flex items-center gap-3 text-base font-medium text-gray-800 dark:text-white/90">
                In Progress
                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-700 dark:bg-white/[0.03] dark:text-white/80">{{ inProgressTasks.length }}</span>
              </h3>
            </div>

            <div class="overflow-y-auto max-h-[600px] p-4 xl:p-6 custom-scrollbar space-y-5 mt-5">
              <TaskColumn :all-tasks="tasks" status="in_progress" @update-status="handleTaskDrop" />
            </div>
          </div>
        </div>

        <div class="overflow-hidden" id="parentCompleted">
          <div class="p-4 xl:p-6">
            <div class="flex items-center justify-between mb-1">
              <h3 class="flex items-center gap-3 text-base font-medium text-gray-800 dark:text-white/90">
                Completed
                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-700 dark:bg-white/[0.03] dark:text-white/80">{{ completedTasks.length }}</span>
              </h3>
            </div>

            <div class="overflow-y-auto max-h-[600px] p-4 xl:p-6 custom-scrollbar space-y-5 mt-5">
              <TaskColumn :all-tasks="tasks" status="completed" @update-status="handleTaskDrop" />
            </div>
          </div>
        </div>
      </div>
    </div>

    <AddTask :isOpen="addTaskModal" @close="addTaskModal = false" />
  </AdminLayout>
</template>
