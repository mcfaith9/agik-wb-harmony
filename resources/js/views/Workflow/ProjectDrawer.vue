<script setup lang="ts">
  import { format } from "date-fns"
  import { computed } from 'vue'
  import { useHelpers } from '@/composables/useHelpers'
  import {
    MessageCircleReply
  } from "lucide-vue-next"

  const props = defineProps<{
    list: TaskList
    comments: Comments
  }>()

  const tasks = props.list.tasks || []
  const { formatDate, avatar, highlightMentions } = useHelpers()
  const total = computed(() => tasks.length)
  const completed = computed(() => tasks.filter(t => t.status === 'completed').length)

  const sortedTasks = computed(() =>
    [...tasks].sort((a, b) => {
      if (a.status === 'completed' && b.status !== 'completed') return -1
      if (a.status !== 'completed' && b.status === 'completed') return 1
      return 0
    })
  )

  const groupedTasks = computed(() => {
    const groups = {
      todo: [],
      in_progress: [],
      completed: []
    }

    for (const task of tasks) {
      if (groups[task.status]) {
        groups[task.status].push(task)
      }
    }

    return groups
  })

  const percent = computed(() => total.value > 0 ? (completed.value / total.value) * 100 : 0)
</script>

<template>
  <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-2 bg-white dark:bg-white/5">
    <div class="flex items-center gap-0.5 h-2 overflow-hidden rounded-md mb-1">
      <div
        v-for="task in sortedTasks"
        :key="task.id"
        class="flex-1 h-full select-none"
        :title="`${task.name || 'Untitled'} (${task.status})`"
        :class="{
          'bg-green-500': task.status === 'completed',
          'bg-gray-300 dark:bg-gray-600': task.status !== 'completed',
        }"/>
    </div>

    <div class="text-xs text-gray-500 dark:text-gray-400">
      {{ completed }} / {{ total }} tasks completed ({{ percent.toFixed(0) }}%)
    </div>
  </div>

  <div class="mt-2 space-y-4 text-sm text-gray-800 dark:text-white/90">
    <div v-if="groupedTasks.in_progress.length">
      <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 block">In Progress</label>
      <ul class="space-y-2 border-l border-gray-200 dark:border-white/10 pl-4">
        <li v-for="task in groupedTasks.in_progress" :key="task.id">
          <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-yellow-400 mr-1"></span>
            <span>{{ task.name }}</span>
          </div>
        </li>
      </ul>
    </div>

    <div v-if="groupedTasks.todo.length">
      <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 block">To Do</label>
      <ul class="space-y-2 border-l border-gray-200 dark:border-white/10 pl-4">
        <li v-for="task in groupedTasks.todo" :key="task.id">
          <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-gray-400 mr-1"></span>
            <span>{{ task.name }}</span>
          </div>
        </li>
      </ul>
    </div>

    <div v-if="groupedTasks.completed.length">
      <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 block">Completed</label>
      <ul class="space-y-2 border-l border-gray-200 dark:border-white/10 pl-4">
        <li v-for="task in groupedTasks.completed" :key="task.id">
          <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-green-500 mr-1"></span>
            <span class="line-through text-gray-400">{{ task.name }}</span>
          </div>
        </li>
      </ul>
    </div>
  </div>

  <div class="mt-10">
    <label class="block text-xs font-medium text-gray-700 dark:text-gray-400 mb-1">Comments</label>
    <div v-if="comments && Object.keys(comments).length">
      <article 
        v-for="comment in comments"
        :key="comment.id"
        class="py-3 text-base bg-white rounded-lg dark:bg-gray-900">
        <div class="flex justify-between items-center mb-2">
          <div class="flex items-center">
            <p class="inline-flex items-center mr-3 text-sm text-gray-900 dark:text-white font-semibold">
              <img
                class="mr-2 w-6 h-6 rounded-full"
                :src="avatar(comment.user.first_name, comment.user.last_name)">
                {{ comment.user.first_name }}
            </p>
            <p class="text-xs text-gray-600 dark:text-gray-400">
              <time 
                :datetime="formatDate(comment.created_at)"
                title="February 8th, 2022">{{ formatDate(comment.created_at, true) }}</time>
            </p>
          </div>
        </div>

        <p
          v-html="highlightMentions(comment.message)" 
          class="ml-2 text-xs text-gray-500 dark:text-gray-400"></p>
        <div class="flex items-center mt-4 ml-2 space-x-4">
          <button 
            type="button"
            class="flex items-center text-xs text-gray-500 hover:underline dark:text-gray-400 font-medium">
            <MessageCircleReply class="mr-1 w-3.5 h-3.5" />
            Reply
          </button>
        </div>
      </article>
    </div>
    <div v-else class="flex">
      <p class="text-xs text-gray-500 dark:text-gray-400">No comments yet. Be the first to share your thoughts!</p>
    </div>
  </div>
</template>
