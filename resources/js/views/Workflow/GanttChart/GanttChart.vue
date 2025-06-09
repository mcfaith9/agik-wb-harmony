<script setup lang="ts">
  import { ref, computed, reactive, onMounted, onUnmounted, watch } from 'vue'
  import {
    format,
    addDays,
    differenceInDays,
    parseISO,
    isBefore,
    isAfter,
    isWeekend,
    startOfMonth,
    endOfMonth, 
    getDate,
    setDate,
    addMonths
  } from 'date-fns'
  import { useHelpers } from '@/composables/useHelpers'
  import { ChevronLeft, ChevronRight, ChevronDown } from 'lucide-vue-next'

  const props = defineProps<{
    tasks: Task[];
  }>()

  const emit = defineEmits(['update-task'])
  const { avatar } = useHelpers()
  const collapsedGroups = reactive<Record<string, boolean>>({})
  const viewMode = ref<'month' | 'half-month'>('half-month')
  const groupedTasks = computed(() => {
    const groups = {}

    for (const task of processedTasks.value) {
      const isVisible = !(
        isAfter(task._start, visibleEnd.value) || isBefore(task._end, visibleStart.value)
      )
      if (!isVisible) continue

      const listName = task.tasklist?.name || 'Uncategorized'

      if (!groups[listName]) {
        groups[listName] = []
      }
      groups[listName].push(task)
    }

    return groups
  })

  const processedTasks = computed(() =>
    props.tasks.map(task => ({
      ...task,
      _start: parseISO(task.start),
      _end: parseISO(task.end),
    }))
  )

  const initialStart = startOfMonth(new Date('2025-06-01'))
  const visibleStart = ref(initialStart)
  const visibleEnd = ref(calculateVisibleEnd(initialStart))

  const headerTitle = computed(() => {
    if (viewMode.value === 'month') {
      return format(visibleStart.value, 'MMMM yyyy')
    } else {
      const endFormatted = format(visibleEnd.value, 'MMM d')
      return `${format(visibleStart.value, 'MMM d')} - ${endFormatted}`
    }
  })

  const timelineDates = computed(() => {
    const daysCount = differenceInDays(visibleEnd.value, visibleStart.value)
    return Array.from({ length: daysCount + 1 }, (_, i) => addDays(visibleStart.value, i))
  })

  const timelineLength = computed(() => timelineDates.value.length)

  const visibleTasks = computed(() =>
    processedTasks.value.filter(task =>
      !(isAfter(task._start, visibleEnd.value) || isBefore(task._end, visibleStart.value))
    )
  )

  const barStyle = (task, draft = null) => {
    const startDate = draft ? parseISO(draft.start) : task._start
    const endDate = draft ? parseISO(draft.end) : task._end

    const clampedStart = isBefore(startDate, visibleStart.value) ? visibleStart.value : startDate
    const clampedEnd = isAfter(endDate, visibleEnd.value) ? visibleEnd.value : endDate

    const offset = (differenceInDays(clampedStart, visibleStart.value) / timelineLength.value) * 100
    const width = ((differenceInDays(clampedEnd, clampedStart) + 1) / timelineLength.value) * 100

    return {
      left: `${offset}%`,
      width: `${width}%`,
      top: '8px',
    }
  }

  const positionState = reactive({
    original: null,
    draft: null,
    mode: null,
    initialX: 0,
  })

  function calculateVisibleEnd(startDate: Date) {
    if (viewMode.value === 'month') {
      return endOfMonth(startDate)
    }

    const monthEnd = endOfMonth(startDate)
    const daysInMonth = getDate(monthEnd)
    const startDay = getDate(startDate)

    if (daysInMonth === 31) {
      if (startDay === 1) {
        return new Date(startDate.getFullYear(), startDate.getMonth(), 15)
      } else {
        return monthEnd
      }
    }

    return addDays(startDate, 14)
  }


  function prevRange() {
    if (viewMode.value === 'month') {
      // just go to previous month start
      visibleStart.value = startOfMonth(addMonths(visibleStart.value, -1))
    } else {
      const startDay = getDate(visibleStart.value)
      const currentMonth = visibleStart.value.getMonth()
      const currentYear = visibleStart.value.getFullYear()

      if (startDay === 1) {
        // Move to 16th of previous month
        const prevMonthDate = addMonths(visibleStart.value, -1)
        visibleStart.value = new Date(prevMonthDate.getFullYear(), prevMonthDate.getMonth(), 16)
      } else {
        // Move to 1st of current month
        visibleStart.value = new Date(currentYear, currentMonth, 1)
      }
    }

    visibleEnd.value = calculateVisibleEnd(visibleStart.value)
  }

  function nextRange() {
    if (viewMode.value === 'month') {
      visibleStart.value = startOfMonth(addMonths(visibleStart.value, 1))
    } else {
      const startDay = getDate(visibleStart.value)
      const currentMonth = visibleStart.value.getMonth()
      const currentYear = visibleStart.value.getFullYear()

      if (startDay === 1) {
        visibleStart.value = new Date(currentYear, currentMonth, 16)
      } else {
        const nextMonthDate = addMonths(visibleStart.value, 1)
        visibleStart.value = startOfMonth(nextMonthDate)
      }
    }

    visibleEnd.value = calculateVisibleEnd(visibleStart.value)
  }

  const formatDate = (date) => format(date, 'MMM d')
  const isWeekendFn = (date) => isWeekend(date)

  function toggleCollapse(groupName: string) {
    collapsedGroups[groupName] = !collapsedGroups[groupName]
  }

  watch(viewMode, (newMode) => {
    visibleStart.value = startOfMonth(visibleStart.value)
    visibleEnd.value = calculateVisibleEnd(visibleStart.value)
  })
</script>

<template>
  <!-- Controls -->
  <div class="sticky top-0 z-20 flex justify-between items-center px-4 py-1 gap-2 h-12">
    <!-- Toggle on the left -->
    <div>
      <label class="inline-flex items-center cursor-pointer">
        <input
          type="checkbox"
          class="sr-only peer"
          :checked="viewMode === 'month'"
          @change="viewMode = $event.target.checked ? 'month' : 'half-month'" />
        <div
          class="relative w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600 dark:peer-checked:bg-blue-600"></div>
        <span class="ms-3 text-xs font-medium text-gray-900 dark:text-gray-300">
          {{ viewMode === 'month' ? 'Full Month' : '15 Days' }}
        </span>
      </label>
    </div>

    <!-- Buttons on the right -->
    <div class="flex gap-2">
      <button
        class="flex items-center justify-center text-gray-500 border-gray-200 rounded-lg z-99999 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 lg:h-8 lg:w-8 lg:border dark:border-gray-800"
        @click="prevRange">
        <ChevronLeft class="text-xs w-5 h-5" />
      </button>
      <button
        class="flex items-center justify-center text-gray-500 border-gray-200 rounded-lg z-99999 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 lg:h-8 lg:w-8 lg:border dark:border-gray-800"
        @click="nextRange">
        <ChevronRight class="text-xs w-5 h-5" />
      </button>
    </div>
  </div>

  <div class="w-full py-3 px-5">
    <!-- Header -->
    <div class="flex">
      <div class="w-1/6 h-12 flex items-center justify-center">
        <div class="text-md font-semibold text-center text-gray-800 dark:text-white/90">
          <span>{{ headerTitle }}</span>
        </div>
      </div>
      <div class="flex-1 flex items-center">
        <div
          v-for="(date, index) in timelineDates"
          :key="index"
          class="text-xs text-center text-gray-700 dark:text-gray-400 border-r border-dashed text-sm flex-1 py-2.5 border-gray-200 dark:border-gray-400"
          :class="[
            isWeekendFn(date) ? 'bg-red-800 font-medium text-white dark:text-gray-100' : '',
            // index === 0 ? 'rounded-l-3xl' : '',
            index === timelineDates.length - 1 ? 'border-none' : ''
          ]">
          {{ formatDate(date) }}
        </div>
      </div>
    </div>

    <!-- Rows: sidebar + timeline bars per row -->
    <div v-for="(tasks, tasklistName) in groupedTasks" :key="tasklistName">
      <!-- Tasklist Header -->
      <div
        class="bg-gray-100 dark:bg-gray-700 text-xs font-bold px-3 py-2 border-y border-gray-300 dark:border-gray-500 cursor-pointer"
        @click="toggleCollapse(tasklistName)">
        <div class="flex items-center space-x-1">
          <component
            :is="collapsedGroups[tasklistName] ? ChevronRight : ChevronDown"
            class="w-4 h-4 text-gray-600 dark:text-gray-300"
          />
          <span class="truncate text-gray-600 dark:text-gray-300">{{ tasklistName }}</span>
        </div>
      </div>

      <div
        v-show="!collapsedGroups[tasklistName]"
        v-for="(task, index) in tasks"
        v-memo="[task.start, task.end, visibleStart, visibleEnd]"
        :key="task.id"
        :class="[
          'flex h-8',
          index !== tasks.length - 1
            ? 'border-b border-gray-200 dark:border-gray-400'
            : ''
        ]">
        <!-- Sidebar Column -->
        <div class="w-1/6 pl-2 flex items-center text-xs text-gray-700 dark:text-gray-400">
          {{ task.name }}
        </div>

        <!-- Timeline Bar Column -->
        <div class="flex-1 relative">        
          <div
            class="absolute bg-gray-300 shadow px-1 flex items-center h-4 rounded-l-xl rounded-r-xl"
            :style="positionState.original?.id === task.id ? barStyle(task, positionState.draft) : barStyle(task)"
            :title="`${task.name}: ${task.start} → ${task.end}`">
            <div class="flex -space-x-2 select-none z-50">
              <img
                v-if="task.users && task.users.length > 0"
                v-for="(user, idx) in task.users || []"
                :key="idx"
                :src="avatar(user.first_name, user.last_name)"
                class="w-4 h-4 border-1 border-white rounded-full dark:border-gray-300"
                :alt="user.first_name" />

              <img v-else src="@/images/user/owner.jpg" class="w-4 h-4 border-2 border-white rounded-full dark:border-gray-300" />
            </div>
            <div class="flex-1 text-center text-xs z-20 truncate">{{ task.name }}</div>
            <div
              class="absolute left-0 top-0 bottom-0 bg-green-600"
              :style="{ width: task.status === 'completed' ? '100%' : (task.progress || '0%') }">
            </div>
            <div
              class="absolute right-0 top-0 bottom-0 w-2 rounded-r-xl"
              :class="{
                'bg-gray-400': task.status === 'todo',
                'bg-yellow-500': task.status === 'in_progress',
              }">
            </div>
          </div>
        </div>
      </div>
    </div>   

    <div v-if="visibleTasks.length === 0" class="text-sm text-center text-gray-500 py-10 dark:text-gray-400">
      No tasks available for this range.
    </div>
  </div>
</template>
