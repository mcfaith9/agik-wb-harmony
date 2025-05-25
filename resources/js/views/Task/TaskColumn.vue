<script setup lang="ts">
	import { defineEmits, defineProps, ref, computed, watch } from "vue"
	import { format } from "date-fns"
	import draggable from "vuedraggable"

	const props = defineProps({
	  allTasks: {
	    type: Array,
	    required: true
	  },
	  status: {
	    type: String,
	    required: true
	  }
	})

	const filteredTasks = ref([])
	const dragging = ref(false)
	const emit = defineEmits(['update-status', 'update:allTasks'])
	const tasks = computed(() =>
	  props.allTasks.filter(t => t.status === props.status)
	)	

	watch(() => props.allTasks, () => {
	  filteredTasks.value = props.allTasks.filter(t => t.status === props.status)
	}, { immediate: true })

	const formatDateRange = (start: string, end: string) => {
	  if (!start || !end) return ''
	  return `${format(new Date(start), 'MMM d')} to ${format(new Date(end), 'MMM d, yyyy')}`
	}

	const onTaskAdd = (evt: any) => {
	  const task = evt.item?._underlying_vm_;
	  if (!task) return;

	  emit('update-status', { task, newStatus: props.status })

	  // Optional: refresh filtered list (not always needed)
	  filteredTasks.value = props.allTasks.filter(t => t.status === props.status)
	}

	const onDragStart = (evt) => {
	  dragging.value = true
	}

	const onDragEnd = (evt) => {
	  dragging.value = false
	}
</script>

<template>
  <draggable
    :list="filteredTasks"
    :group="{ name: 'tasks', pull: true, put: true }"
    item-key="id"
    animation="200"
    @start="onDragStart"
    @end="onDragEnd"
    @add="onTaskAdd"
    :class="{ 'cursor-grabbing': dragging }"
    class="min-h-[200px] flex flex-col gap-5"
  >
    <template #item="{ element: task }">
      <div
        class="p-5 bg-white border border-gray-200 task rounded-xl shadow-theme-sm dark:border-gray-800 dark:bg-white/5"
      >
        <div class="flex items-start justify-between gap-6">
          <div>
            <h4 class="mb-5 text-base text-gray-800 dark:text-white/90">
              {{ task.name }}
            </h4>
            <div class="flex items-center gap-3">
              <span class="flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400">
                📅 {{ formatDateRange(task.start_date, task.end_date) }}
              </span>
              <span class="flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400">
                🕓 {{ task.estimated_time }}
              </span>
            </div>
            <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">
              {{ task.tasklist?.project?.name }} • {{ task.tasklist?.name }}
            </div>
            <span
              class="mt-2 inline-block rounded-full bg-orange-400/10 px-2 py-0.5 text-xs font-medium text-orange-400"
            >
              {{ task.tags ?? "Uncategorized" }}
            </span>
          </div>
          <div
            class="h-6 w-6 shrink-0 overflow-hidden rounded-full border-[0.5px] border-gray-200 dark:border-gray-800"
          >
            <img src="@/images/user/user-07.jpg" alt="Assignee" />
          </div>
        </div>
      </div>
    </template>
  </draggable>
</template>

<style scoped>
	.cursor-grabbing {
	  cursor: grabbing !important;
	}
</style>
