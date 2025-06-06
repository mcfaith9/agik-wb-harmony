<script setup lang="ts">
  import { ref, computed, reactive, onMounted, onUnmounted } from 'vue'
  import {
    format,
    addDays,
    differenceInDays,
    parseISO,
    isBefore,
    isAfter,
    isWeekend,
    startOfMonth,
  } from 'date-fns'
  import { useHelpers } from '@/composables/useHelpers'
  import { ChevronLeft, ChevronRight } from 'lucide-vue-next'

  const props = defineProps<{
    tasks: Task[];
  }>()

  const { avatar } = useHelpers()

  // Pre-parse start and end dates for performance
  const processedTasks = computed(() =>
    props.tasks.map(task => ({
      ...task,
      _start: parseISO(task.start),
      _end: parseISO(task.end),
    }))
  )

  const initialStart = startOfMonth(new Date('2025-06-01'))
  const initialEnd = addDays(initialStart, 14)

  const visibleStart = ref(initialStart)
  const visibleEnd = ref(initialEnd)
  const headerTitle = computed(() => format(visibleStart.value, 'MMMM yyyy'))

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
    const startDate = draft ? parseISO(draft.start) : task._start;
    const endDate = draft ? parseISO(draft.end) : task._end;

    const clampedStart = isBefore(startDate, visibleStart.value) ? visibleStart.value : startDate;
    const clampedEnd = isAfter(endDate, visibleEnd.value) ? visibleEnd.value : endDate;

    const offset = (differenceInDays(clampedStart, visibleStart.value) / timelineLength.value) * 100;
    const width = ((differenceInDays(clampedEnd, clampedStart) + 1) / timelineLength.value) * 100;

    return {
      left: `${offset}%`,
      width: `${width}%`,
      top: '8px',
    };
  }

  // Drag logic
  const dragState = reactive({
    original: null,
    draft: null,
    mode: null,
    initialX: 0,
  })

  function startDrag(event, task, mode) {
    dragState.original = task
    dragState.draft = { ...task } // shallow clone
    dragState.mode = mode
    dragState.initialX = event.clientX
  }

  const timelineRef = ref(null)
  let rafId = null

  function onMouseMove(event) {
    if (!dragState.draft || !dragState.draft.start || !dragState.draft.end || !timelineRef.value) return
    if (rafId) return

    rafId = requestAnimationFrame(() => {
      const deltaX = event.clientX - dragState.initialX
      const deltaPercent = deltaX / timelineRef.value.offsetWidth
      const deltaDays = Math.round(deltaPercent * timelineDates.value.length)
      if (deltaDays === 0) {
        rafId = null
        return
      }

      const start = parseISO(dragState.draft.start)
      const end = parseISO(dragState.draft.end)

      if (dragState.mode === 'move') {
        dragState.draft.start = format(addDays(start, deltaDays), 'yyyy-MM-dd')
        dragState.draft.end = format(addDays(end, deltaDays), 'yyyy-MM-dd')
      } else if (dragState.mode === 'start') {
        const newStart = addDays(start, deltaDays)
        if (newStart <= end) {
          dragState.draft.start = format(newStart, 'yyyy-MM-dd')
        }
      } else if (dragState.mode === 'end') {
        const newEnd = addDays(end, deltaDays)
        if (newEnd >= start) {
          dragState.draft.end = format(newEnd, 'yyyy-MM-dd')
        }
      }

      dragState.initialX = event.clientX
      rafId = null
    })
  }

  const emit = defineEmits(['update-task'])

  function onMouseUp() {
    if (dragState.draft) {
      emit('update-task', {
        id: dragState.original.id,
        start: dragState.draft.start,
        end: dragState.draft.end,
      })
    }
    dragState.draft = null
    dragState.original = null
    dragState.mode = null
  }

  onMounted(() => {
    document.addEventListener('mousemove', onMouseMove)
    document.addEventListener('mouseup', onMouseUp)
  })

  onUnmounted(() => {
    document.removeEventListener('mousemove', onMouseMove)
    document.removeEventListener('mouseup', onMouseUp)
  })

  function prevRange() {
    visibleStart.value = addDays(visibleStart.value, -15)
    visibleEnd.value = addDays(visibleEnd.value, -15)
  }

  function nextRange() {
    visibleStart.value = addDays(visibleStart.value, 15)
    visibleEnd.value = addDays(visibleEnd.value, 15)
  }

  const formatDate = (date) => format(date, 'MMM d')
  const isWeekendFn = (date) => isWeekend(date)
</script>

<template>
  <!-- Controls -->
  <div class="sticky top-0 z-20 flex justify-end items-center px-4 py-1 gap-2 h-12">
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

  <div class="w-full py-3 px-5">
    <!-- Header -->
    <div class="flex">
      <div class="w-1/6 h-12 flex items-center justify-center">
        <div class="text-lg font-semibold text-center dark:text-white">
          <span>{{ headerTitle }}</span>
        </div>
      </div>
      <div class="flex-1 flex items-center">
        <div
          v-for="(date, index) in timelineDates"
          :key="index"
          class="text-xs text-center text-gray-700 dark:text-gray-400 border text-sm flex-1 py-2.5 border-gray-200 dark:border-gray-400"
          :class="{ 'bg-red-800 font-medium text-white dark:text-gray-100': isWeekendFn(date) }" >
          {{ formatDate(date) }}
        </div>
      </div>
    </div>

    <!-- Rows: sidebar + timeline bars per row -->
    <div
      v-for="task in visibleTasks"
      v-memo="[task.start, task.end, visibleStart, visibleEnd]"
      :key="task.id"
      class="flex border-b border-gray-200 dark:border-gray-400 h-8">
      <!-- Sidebar Column -->
      <div class="w-1/6 pl-2 flex items-center text-xs text-gray-700 dark:text-gray-400">
        {{ task.name }}
      </div>

      <!-- Timeline Bar Column -->
      <div class="flex-1 relative">        
        <div
          class="absolute bg-gray-300 shadow px-1 flex items-center h-5"
          :style="dragState.original?.id === task.id ? barStyle(task, dragState.draft) : barStyle(task)"
          @mousedown.prevent="(e) => startDrag(e, task, 'move')"
          :title="`${task.name}: ${task.start} → ${task.end}`">
          <div class="flex -space-x-2">
            <img
              v-if="task.users && task.users.length > 0"
              v-for="(user, idx) in task.users || []"
              :key="idx"
              :src="avatar(user.first_name, user.last_name)"
              class="w-4 h-4 border-2 border-white rounded-full dark:border-gray-300"
              :alt="user.first_name" />

            <img v-else src="@/images/user/owner.jpg" class="w-4 h-4 border-2 border-white rounded-full dark:border-gray-300" />
          </div>
          <div class="flex-1 text-center text-xs z-20 truncate">{{ task.name }}</div>
          <div
            class="absolute left-0 top-0 bottom-0 bg-green-600"
            :style="{ width: task.status === 'completed' ? '100%' : (task.progress || '0%') }">
          </div>
          <div
            class="absolute right-0 top-0 bottom-0 w-2"
            :class="{
              'bg-gray-400': task.status === 'todo',
              'bg-yellow-500': task.status === 'in_progress',
            }">
          </div>
        </div>
      </div>
    </div>

    <div v-if="visibleTasks.length === 0" class="text-center text-gray-500 py-10 dark:text-gray-400">
      No tasks available for this range.
    </div>
  </div>
</template>

<style scoped>
  * {
    box-sizing: border-box;
  }
  .absolute > div:first-child {
    position: relative;
    z-index: 10;
  }
</style>
