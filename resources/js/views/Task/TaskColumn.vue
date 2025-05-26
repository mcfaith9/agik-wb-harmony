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
    class="min-h-[200px] flex flex-col gap-5">
    <template #item="{ element: task }">
      <div class="p-5 bg-white border border-gray-200 task rounded-xl shadow-theme-sm dark:border-gray-800 dark:bg-white/5">
        <div class="flex items-start justify-between gap-6">
          <div>
          	<div class="mb-2 text-xs text-gray-500 dark:text-gray-400">
          	  {{ task.tasklist?.project?.name }} • {{ task.tasklist?.name }}
          	</div>
            <h4 class="text-base text-gray-800 dark:text-white/90">
              {{ task.name }}
            </h4>
            <div class="flex items-center gap-x-3">
              <span class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                {{ formatDateRange(task.start_date, task.end_date) }}
              </span>
              <span class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                🕓 {{ task.estimated_time }}
              </span>
            </div>            
            <div class="mt-2 flex flex-wrap gap-1">
              <span
                v-for="(tag, index) in task.tags"
                :key="index"
                class="px-2 py-0.5 text-xs font-medium bg-brand-50 text-brand-500 dark:bg-brand-500/15 dark:text-brand-400 mt-3 inline-flex rounded-full text-xs font-medium">
                {{ tag || 'Uncategorized' }}
              </span>
            </div> 
          </div>
          <div
            v-if="task.users && task.users.length > 0" 
            class="flex -space-x-3 rtl:space-x-reverse">
            <template v-for="user in task.users" :key="user.id">
              <img
                v-if="user.first_name && user.last_name"
                :src="`https://ui-avatars.com/api/?background=4961fe&color=fff&bold=true&name=${user.first_name}+${user.last_name}`"
                :alt="`${user.first_name} ${user.last_name}`"
                class="w-6 h-6 border-2 border-white rounded-full dark:border-gray-800"
              />
            </template>
	        </div>
          <img
            v-else
            src="@/images/user/owner.jpg"
            alt="default avatar"
            class="w-6 h-6 border-2 border-white rounded-full dark:border-gray-800"/>
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
