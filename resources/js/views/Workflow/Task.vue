<script setup lang="ts">
  import { onMounted, ref, computed, onBeforeUnmount, nextTick } from "vue"
  import axios from "axios"
  import PageBreadcrumb from "@/components/common/PageBreadcrumb.vue"
  import AdminLayout from "@/components/layout/AdminLayout.vue"
  import TaskForm from "@/views/Workflow/Modal/TaskForm.vue"
  import TaskCard from "@/views/Workflow/TaskCard.vue"
  import MentionTextarea from "@/components/common/MentionTextarea.vue"
  import { users } from '@/stores/allUsers'
  import { 
  	Settings2, 
  	CircleFadingPlus,
    X
  } from "lucide-vue-next"

  const currentPageTitle = ref("Tasks")
  const addTaskModal = ref(false)
  const selectedStatuses = ref<string[]>([])
  const tasks = computed(() => Array.from(taskMap.value.values()))
  const selectedTask = ref<Task | null>(null)
  const taskMap = ref(new Map())

  onMounted(async () => {
    const res = await axios.get("/api/tasks")
    const map = new Map()
    res.data.forEach(task => map.set(task.id, task))
    taskMap.value = map
  })

  const groupedTasks = computed(() => {
    const groups = { todo: [], in_progress: [], completed: [] }
    for (const task of taskMap.value.values()) {
      if (groups[task.status]) {
        groups[task.status].push(task)
      }
    }
    return groups
  })

  function toggleStatusFilter(status: string) {
    const index = selectedStatuses.value.indexOf(status)
    if (index === -1) {
      selectedStatuses.value.push(status)
    } else {
      selectedStatuses.value.splice(index, 1)
    }
  }

  async function handleTaskDrop({ task, newStatus }) {
    if (task.status === newStatus) return

    try {
      await axios.put(`/api/tasks/${task.id}`, { status: newStatus })
      const t = taskMap.value.get(task.id)
      if (t) {
        t.status = newStatus
        taskMap.value = new Map(taskMap.value)
      }
    } catch (e) {
      console.error("Error updating task status", e)
    }
  }

  const upsertTask = (task) => {
    const existingTask = taskMap.value.get(task.id)
    if (existingTask) {
      taskMap.value.set(task.id, { ...existingTask, ...task })
    } else {
      taskMap.value.set(task.id, task)
    }
  }

  const handleTaskUpdated = (task) => {
    upsertTask(task)
    selectedTask.value = task
    addTaskModal.value = true
  }

  const handleTaskCreated = (newTask) => {
    upsertTask(newTask)
    selectedTask.value = null
    addTaskModal.value = true
  }
  
  function openAddTaskModal() {
    selectedTask.value = null
    addTaskModal.value = true
  }

  //comment logic
  const commentText = ref('')
  const floatingCommentVisible = ref(false)
  const floatingCommentPos = ref({ top: 0, left: 0 })
  const floatingCommentTaskId = ref<string | null>(null)
  const floatingTaskName = ref<string | null>(null)

  let scrollContainers: HTMLElement[] = []
  let iconElement: HTMLElement | null = null

  function updateFloatingCommentPosition() {
    if (!iconElement) {
      floatingCommentVisible.value = false
      return
    }

    const iconRect = iconElement.getBoundingClientRect()
    const commentBoxWidth = 300
    const commentBoxHeight = 200
    const padding = 8

    let top = iconRect.bottom + window.scrollY + padding
    let left = iconRect.left + window.scrollX

    const viewportWidth = window.innerWidth
    const viewportHeight = window.innerHeight

    // Correct right overflow
    if (left + commentBoxWidth > viewportWidth + window.scrollX) {
      left = viewportWidth + window.scrollX - commentBoxWidth - padding
    }

    // Correct bottom overflow
    if (top + commentBoxHeight > viewportHeight + window.scrollY) {
      top = iconRect.top + window.scrollY - commentBoxHeight - padding
    }

    // Ensure left doesn't go negative
    if (left < window.scrollX + padding) {
      left = window.scrollX + padding
    }

    floatingCommentPos.value = { top, left }
    floatingCommentVisible.value = true
  }

  function onScrollOrResize() {
    updateFloatingCommentPosition()
  }

  function toggleComment(taskId: string, taskName: string, event: MouseEvent) {
    commentText.value = ''
    floatingCommentTaskId.value = taskId
    floatingTaskName.value = taskName
    iconElement = (event.target as HTMLElement).closest('.comment-icon') as HTMLElement | null

    if (!iconElement) {
      floatingCommentVisible.value = false
      return
    }

    updateFloatingCommentPosition()

    // Add scroll listeners on window and all scrollable parent containers
    scrollContainers = []

    // Find all scrollable parents (including window)
    let el: HTMLElement | null = iconElement
    while (el) {
      const style = window.getComputedStyle(el)
      if (el.scrollHeight > el.clientHeight || el.scrollWidth > el.clientWidth) {
        scrollContainers.push(el)
      }
      el = el.parentElement
    }
    scrollContainers.push(window as any)

    scrollContainers.forEach(container => {
      container.addEventListener('scroll', onScrollOrResize, { passive: true })
    })

    window.addEventListener('resize', onScrollOrResize, { passive: true })
  }

  function closeFloatingComment() {
    floatingCommentVisible.value = false
    floatingCommentTaskId.value = null
    floatingTaskName.value = null
    iconElement = null
    scrollContainers.forEach(container => {
      container.removeEventListener('scroll', onScrollOrResize)
    })
    window.removeEventListener('resize', onScrollOrResize)
    scrollContainers = []
  }

  async function postComment() {
    if (!commentText.value.trim() || !floatingCommentTaskId.value) return

    try {
      const taskId = floatingCommentTaskId.value
      await axios.post(`/api/comments/task/${taskId}`, {
        message: commentText.value
      })

      commentText.value = ''
      closeFloatingComment()

      const task = taskMap.value.get(taskId)
      if (task) {
        task.comments_count = (task.comments_count || 0) + 1
        taskMap.value = new Map(taskMap.value)
      }

    } catch (e) {
      console.error("Failed to post comment", e)
    }
  }
</script>

<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
      <div class="flex flex-col items-center px-4 py-5 xl:px-6 xl:py-6">
        <div class="flex flex-col w-full gap-5 sm:justify-between xl:flex-row xl:items-center">
          <div class="flex flex-wrap items-center gap-x-1 gap-y-2 rounded-full bg-gray-100 p-0.5 dark:bg-gray-900">
            <button 
              :class="[
                'inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium rounded-full h group',
                selectedStatuses.length === 0 ? 'text-gray-900 dark:text-white bg-white dark:bg-gray-800 shadow' : 'text-gray-500 dark:text-gray-400',
                ]"
                @click="selectedStatuses = []">
                All Task
              <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium bg-brand-50 text-brand-500 dark:bg-brand-500/15 dark:text-brand-400">{{ tasks.length }}</span>
            </button>
            <button 
              :class="[
                'inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium rounded-full h group',
                selectedStatuses.includes('todo') ? 'text-gray-900 dark:text-white bg-white dark:bg-gray-800 shadow' : 'text-gray-500 dark:text-gray-400'
                ]"
              @click="toggleStatusFilter('todo')">
              To do
              <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium bg-brand-50 text-brand-500 dark:bg-brand-500/15 dark:text-brand-400">
                {{ groupedTasks.todo.length }}
              </span>
            </button>
            <button 
              :class="[
                'inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium rounded-full h group',
                selectedStatuses.includes('in_progress') ? 'text-gray-900 dark:text-white bg-white dark:bg-gray-800 shadow' : 'text-gray-500 dark:text-gray-400'
                ]"
              @click="toggleStatusFilter('in_progress')">
              In Progres
              <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium bg-brand-50 text-brand-500 dark:bg-brand-500/15 dark:text-brand-400">
                {{ groupedTasks.in_progress.length }}
              </span>
            </button>
            <button 
              :class="[
                'inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium rounded-full h group',
                selectedStatuses.includes('completed') ? 'text-gray-900 dark:text-white bg-white dark:bg-gray-800 shadow' : 'text-gray-500 dark:text-gray-400'
                ]"
              @click="toggleStatusFilter('completed')">
              Completed
              <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium bg-brand-50 text-brand-500 dark:bg-brand-500/15 dark:text-brand-400">
                {{ groupedTasks.completed.length }}
              </span>
            </button>
          </div>

          <div class="flex flex-wrap items-center gap-3 xl:justify-end">
            <button class="inline-flex items-center gap-2 rounded-full border border-dashed border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.03]">
              <Settings2 class="w-4 h-4" />
              Filter & Sort
            </button>
            <button  @click="openAddTaskModal" class="inline-flex items-center gap-2 rounded-full bg-brand-500 px-3 py-1.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">
              Add Task
              <CircleFadingPlus class="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 border-t border-gray-200 divide-x divide-gray-200 dark:border-gray-800 mt-7 dark:divide-gray-800 sm:mt-0 sm:grid-cols-2 xl:grid-cols-3">	
        <div class="overflow-hidden" id="parentTodo">
          <div class="p-2 xl:p-4">
            <div class="flex items-center justify-between mb-1">
              <h3 class="flex items-center gap-3 text-base font-medium text-gray-800 dark:text-white/90">
                To Do
                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-700 dark:bg-white/[0.03] dark:text-white/80">{{ groupedTasks.todo.length }}</span>
              </h3>
            </div>

            <div
              v-if="selectedStatuses.length === 0 || selectedStatuses.includes('todo')" 
              class="overflow-y-auto max-h-[600px] custom-scrollbar p-2 space-y-5 mt-1 cursor-grab">
              <TaskCard :users="users" :data="groupedTasks.todo" status="todo" @edit-task="handleTaskUpdated" @update-status="handleTaskDrop" @toggle-comment="toggleComment" />
            </div>
          </div>
        </div>

        <div class="overflow-hidden" id="parentInProgress">
          <div class="p-2 xl:p-4">
            <div class="flex items-center justify-between mb-1">
              <h3 class="flex items-center gap-3 text-base font-medium text-gray-800 dark:text-white/90">
                In Progress
                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-700 dark:bg-white/[0.03] dark:text-white/80">{{ groupedTasks.in_progress.length }}</span>
              </h3>
            </div>

            <div
              v-if="selectedStatuses.length === 0 || selectedStatuses.includes('in_progress')" 
              class="overflow-y-auto max-h-[600px] custom-scrollbar p-2 space-y-5 mt-1 cursor-grab">
              <TaskCard :users="users" :data="groupedTasks.in_progress" status="in_progress" @edit-task="handleTaskUpdated" @update-status="handleTaskDrop" @toggle-comment="toggleComment" />
            </div>
          </div>
        </div>

        <div class="overflow-hidden" id="parentCompleted">
          <div class="p-2 xl:p-4">
            <div class="flex items-center justify-between mb-1">
              <h3 class="flex items-center gap-3 text-base font-medium text-gray-800 dark:text-white/90">
                Completed
                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-700 dark:bg-white/[0.03] dark:text-white/80">{{ groupedTasks.completed.length }}</span>
              </h3>
            </div>

            <div
              v-if="selectedStatuses.length === 0 || selectedStatuses.includes('completed')" 
              class="overflow-y-auto max-h-[600px] custom-scrollbar p-2 space-y-5 mt-1 cursor-grab">
              <TaskCard :users="users" :data="groupedTasks.completed" status="completed" @edit-task="handleTaskUpdated" @update-status="handleTaskDrop" @toggle-comment="toggleComment" />
            </div>
          </div>
        </div>
      </div>
    </div>

    <TaskForm 
      :isOpen="addTaskModal" 
      :task="selectedTask"
      @close="addTaskModal = false" 
      @task-created="handleTaskCreated"
      @task-updated="handleTaskUpdated" />

    <div
      v-if="floatingCommentVisible"
      class="absolute z-50 rounded-2xl border border-gray-200 bg-white p-3 shadow-lg dark:border-gray-700 dark:bg-gray-800 shadow-lg"
      :style="{ top: `${floatingCommentPos.top}px`, left: `${floatingCommentPos.left}px`, width: '', height: '' }">
      <div class="flex justify-between items-center mb-2">
        <h4 class="flex items-center gap-3 text-sm font-medium text-gray-800 dark:text-white/90">Task {{ floatingTaskName }}</h4>
        <button 
          class="text-gray-500 dark:text-gray-400"
          @click="closeFloatingComment">
          <X class="w-4 h-4" />
        </button>
      </div>

      <MentionTextarea 
        v-model="commentText" 
        :placeholder="'Message...'"
        :key="floatingCommentTaskId"
        :users="users" />
      <div class="flex justify-end mt-2">
        <button
          class="inline-flex items-center gap-2 rounded-full bg-brand-500 px-3 py-1.5 text-xs font-medium text-white shadow-theme-xs hover:bg-brand-600"
          @click="postComment">
          Post Comment
        </button>
      </div>
    </div>
  </AdminLayout>
</template>
