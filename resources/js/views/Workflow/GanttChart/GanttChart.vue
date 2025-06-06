<script setup>
import { ref, reactive, computed, onMounted, onUnmounted } from 'vue';
import {
  format,
  addDays,
  differenceInDays,
  parseISO,
  isWeekend,
  isBefore,
  isAfter,
  startOfMonth,
  min,
  max,
} from 'date-fns';
import { GitCommitHorizontal } from 'lucide-vue-next';

const tasks = ref([
  { id: 1, name: 'Design', progress: '60%', start: '2025-06-01', end: '2025-06-05', users: [{ name: 'Alice', avatar: 'https://i.pravatar.cc/20?img=1' }] },
  { id: 2, name: 'Development', progress: '30%', start: '2025-06-03', end: '2025-06-10', users: [{ name: 'Bob', avatar: 'https://i.pravatar.cc/20?img=2' }] },
  { id: 3, name: 'Testing', progress: '10%', start: '2025-06-08', end: '2025-06-12', users: [{ name: 'Charlie', avatar: 'https://i.pravatar.cc/20?img=3' }, { name: 'Dana', avatar: 'https://i.pravatar.cc/20?img=4' }] },
]);

const timelineRef = ref(null);
const initialStart = startOfMonth(new Date('2025-06-01'));
const initialEnd = addDays(initialStart, 14);

const visibleStart = ref(initialStart);
const visibleEnd = ref(initialEnd);

const timelineDates = computed(() => {
  const daysCount = differenceInDays(visibleEnd.value, visibleStart.value);
  return Array.from({ length: daysCount + 1 }, (_, i) => addDays(visibleStart.value, i));
});

const visibleTasks = computed(() =>
  tasks.value.filter((task) => {
    const taskStart = parseISO(task.start);
    const taskEnd = parseISO(task.end);
    return !(isAfter(taskStart, visibleEnd.value) || isBefore(taskEnd, visibleStart.value));
  })
);

const formatDate = (date) => format(date, 'MMM d');
const isWeekendFn = (date) => isWeekend(date);

const barStyle = (task) => {
  const taskStart = parseISO(task.start);
  const taskEnd = parseISO(task.end);

  const clampedStart = isBefore(taskStart, visibleStart.value) ? visibleStart.value : taskStart;
  const clampedEnd = isAfter(taskEnd, visibleEnd.value) ? visibleEnd.value : taskEnd;

  const offset = (differenceInDays(clampedStart, visibleStart.value) / timelineDates.value.length) * 100;
  const width = ((differenceInDays(clampedEnd, clampedStart) + 1) / timelineDates.value.length) * 100;

  return {
    left: `${offset}%`,
    width: `${width}%`,
    top: '8px',
  };
};

const dragState = reactive({
  task: null,
  mode: null,
  initialX: 0,
});

function startDrag(event, task, mode) {
  dragState.task = task;
  dragState.mode = mode;
  dragState.initialX = event.clientX;
}

// Extend or shrink timeline bounds if needed, with 2-day increments
const EXTEND_THRESHOLD = 2;

function extendOrShrinkTimeline(updatedStart, updatedEnd) {
  let changed = false;

  // Extend right if needed
  if (
    isAfter(updatedEnd, visibleEnd.value) &&
    differenceInDays(updatedEnd, visibleEnd.value) <= EXTEND_THRESHOLD
  ) {
    visibleEnd.value = addDays(visibleEnd.value, 2);
    changed = true;
  }

  // Extend left if needed
  if (
    isBefore(updatedStart, visibleStart.value) &&
    differenceInDays(visibleStart.value, updatedStart) <= EXTEND_THRESHOLD
  ) {
    visibleStart.value = addDays(visibleStart.value, -2);
    changed = true;
  }

  // Shrink right if possible
  if (
    differenceInDays(visibleEnd.value, visibleStart.value) > 14 && // more than default 15 days
    updatedEnd < visibleEnd.value
  ) {
    // Check if all tasks fit in initial 15-day window
    const allFitInDefaultRange = tasks.value.every(task => {
      const taskStart = parseISO(task.start);
      const taskEnd = parseISO(task.end);
      return (
        !isBefore(taskStart, initialStart) &&
        !isAfter(taskEnd, initialEnd)
      );
    });

    if (allFitInDefaultRange) {
      visibleStart.value = initialStart;
      visibleEnd.value = initialEnd;
      changed = true;
    }
  }

  // Shrink left if possible
  if (
    differenceInDays(visibleEnd.value, visibleStart.value) > 14 && // more than default 15 days
    updatedStart > visibleStart.value
  ) {
    const allFitInDefaultRange = tasks.value.every(task => {
      const taskStart = parseISO(task.start);
      const taskEnd = parseISO(task.end);
      return (
        !isBefore(taskStart, initialStart) &&
        !isAfter(taskEnd, initialEnd)
      );
    });

    if (allFitInDefaultRange) {
      visibleStart.value = initialStart;
      visibleEnd.value = initialEnd;
      changed = true;
    }
  }

  return changed;
}

function onMouseMove(event) {
  const task = dragState.task;
  const mode = dragState.mode;
  if (!task || !mode || !timelineRef.value) return;

  const deltaX = event.clientX - dragState.initialX;
  const deltaPercent = deltaX / timelineRef.value.offsetWidth;
  const deltaDays = Math.round(deltaPercent * timelineDates.value.length);
  if (deltaDays === 0) return;

  const start = parseISO(task.start);
  const end = parseISO(task.end);

  if (mode === 'move') {
    task.start = format(addDays(start, deltaDays), 'yyyy-MM-dd');
    task.end = format(addDays(end, deltaDays), 'yyyy-MM-dd');
  } else if (mode === 'start') {
    const newStart = addDays(start, deltaDays);
    if (newStart <= end) {
      task.start = format(newStart, 'yyyy-MM-dd');
    }
  } else if (mode === 'end') {
    const newEnd = addDays(end, deltaDays);
    if (newEnd >= start) {
      task.end = format(newEnd, 'yyyy-MM-dd');
    }
  }

  dragState.initialX = event.clientX;

  // Adjust timeline bounds based on updated task
  const updatedStart = parseISO(task.start);
  const updatedEnd = parseISO(task.end);
  extendOrShrinkTimeline(updatedStart, updatedEnd);
}

function onMouseUp() {
  dragState.task = null;
  dragState.mode = null;
}

onMounted(() => {
  document.addEventListener('mousemove', onMouseMove);
  document.addEventListener('mouseup', onMouseUp);
});

onUnmounted(() => {
  document.removeEventListener('mousemove', onMouseMove);
  document.removeEventListener('mouseup', onMouseUp);
});

function prevRange() {
  visibleStart.value = addDays(visibleStart.value, -15);
  visibleEnd.value = addDays(visibleEnd.value, -15);
}

function nextRange() {
  visibleStart.value = addDays(visibleStart.value, 15);
  visibleEnd.value = addDays(visibleEnd.value, 15);
}
</script>

<template>
  <!-- Controls -->
  <div class="sticky top-0 z-20 flex justify-end items-center px-4 py-1 gap-2 h-12">
    <button class="bg-blue-500 text-white px-3 py-1 rounded text-sm" @click="prevRange">
      Prev 15d
    </button>
    <button class="bg-green-500 text-white px-3 py-1 rounded text-sm" @click="nextRange">
      Next 15d
    </button>
  </div>

  <div class="flex w-full p-4 overflow-hidden">
    <!-- Sidebar: Task List -->
    <div class="w-1/7 bg-gray-100 overflow-y-auto custom-scrollbar" ref="sidebarRef">
      <!-- Spacer to align with controls height -->
      <div class="h-10"></div>
      <div
        v-for="task in visibleTasks"
        :key="task.id"
        class="text-sm flex items-center pl-2 h-8 border-b">
        {{ task.name }}
        <span>{{ task.start }} - {{ task.end }}</span>
      </div>
    </div>

    <!-- Timeline Area -->
    <div class="flex-1 overflow-auto relative" ref="timelineRef">
      <div class="flex flex-col h-full w-full">
        <!-- Timeline Header -->
        <div class="flex border-y border-gray-200 dark:border-gray-400 w-full min-w-max">
          <div
            v-for="(date, index) in timelineDates"
            :key="index"
            class="text-xs text-center text-gray-700 dark:text-gray-400 border-r text-sm flex-1 py-2.5 border-gray-200 dark:border-gray-400"
            :class="{ 'bg-red-800 font-medium text-white dark:text-gray-100': isWeekendFn(date) }" >
            {{ formatDate(date) }}
          </div>
        </div>

        <!-- Task Bars -->
        <div class="flex-1 overflow-y-auto">
          <div v-for="task in visibleTasks" :key="task.id" class="relative h-8 group">
            <div
              class="absolute bg-gray-300 shadow px-1 cursor-move flex items-center gap-1 h-5"
              :style="barStyle(task)"
              @mousedown.prevent="(e) => startDrag(e, task, 'move')"
              :title="`${task.name}: ${task.start} → ${task.end}`">
              <div class="flex -space-x-1">
                <img
                  v-for="(user, idx) in task.users || []"
                  :key="idx"
                  :src="user.avatar"
                  class="w-4 h-4 rounded-full border border-white"
                  :alt="user.name"
                />
              </div>

              <div class="flex-1 z-20 text-center text-xs whitespace-nowrap overflow-hidden">
                {{ task.name }} - {{ task.progress || '0%' }}
              </div>
              <div
                class="absolute left-0 top-0 bottom-0 bg-green-500 rounded-l"
                :style="{ width: task.progress || '0%' }"></div>
              <div
                class="absolute right-0 top-0 bottom-0 w-2 cursor-ew-resize bg-gray-100"
                @mousedown.stop.prevent="(e) => startDrag(e, task, 'end')"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.absolute > div:first-child {
  position: relative;
  z-index: 10;
}
</style>
