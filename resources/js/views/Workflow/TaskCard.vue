<script setup lang="ts">
	import { ref, computed, watch, onMounted } from "vue"
	import { format } from "date-fns"
  import { userStore } from "@/stores/userStore"
  import axios from "axios"
	import draggable from "vuedraggable"
  import Drawer from "@/components/common/Drawer.vue"
  import TaskDrawer from "@/views/Workflow/TaskDrawer.vue"
  import MentionTextarea from "@/components/common/MentionTextarea.vue"
  import { 
    Flag,
    MessageCircle,
    Paperclip
  } from "lucide-vue-next"

	const props = defineProps<{
    users: Users[]
    data: Task[]
    status: string
  }>()

  const emit = defineEmits<{
    (e: 'edit-task'): void,
    (e: 'update-status'): void,
    (e: 'update:data'): void,
    (e: 'toggle-comment'): void,
  }>()

  const newComment = ref("")
  const comments = ref<Comments[]>([])
  const selectedTask = ref<Task | null>(null)
  const showDrawer = ref<boolean>(false)
	const dragging = ref<boolean>(false)
  const user =  computed(() => userStore.user || {})
	const tasks = computed(() => {
    return [...props.data].sort((a, b) => {
      const dateA = new Date(a.created_at || a.start_date).getTime()
      const dateB = new Date(b.created_at || b.start_date).getTime()
      return dateB - dateA
    })
  })  

	const formatDateRange = (start: string, end: string) => {
	  if (!start || !end) return ''
	  return `${format(new Date(start), 'MMM d')} to ${format(new Date(end), 'MMM d, yyyy')}`
	}

	const onTaskAdd = (evt: any) => {
	  const task = evt.item?._underlying_vm_
	  if (!task) return

	  emit('update-status', { task, newStatus: props.status })
	}

  const openTaskDrawer = (task: Task) => {
    if (dragging.value || !selectedTask || !showDrawer) return
    selectedTask.value = task
    showDrawer.value = true
  }

  const postComment = async () => {
    if (!newComment.value.trim() || !selectedTask.value) return;

    const { data } = await axios.post(`/api/tasks/${selectedTask.value.id}/comments`, {
      message: newComment.value
    });

    comments.value.unshift(data);
    newComment.value = "";

    const task = tasks.value.find(t => t.id === selectedTask.value?.id)
    if (task) task.comments_count = (task.comments_count || 0) + 1;
  }

  const fetchComments = async () => {
    if (!selectedTask.value) return
    const { data } = await axios.get(`/api/tasks/${selectedTask.value.id}/comments`)
    comments.value = data
  }

  watch(selectedTask, (task) => {
    if (task) fetchComments()
  })

	const onDragStart = () => { dragging.value = true }
	const onDragEnd = () => { dragging.value = false }
</script>

<template>
  <draggable
    :list="tasks"
    :group="{ name: 'tasks', pull: true, put: true }"
    item-key="id"
    animation="200"
    @start="onDragStart"
    @end="onDragEnd"
    @add="onTaskAdd"
    :class="{ 'cursor-grabbing': dragging }"
    class="flex flex-col gap-5 py-2">
    <template #item="{ element: task }">
      <div class="relative p-5 bg-white border border-gray-200 task rounded-xl shadow-theme-sm dark:border-gray-800 dark:bg-white/5">        
        <button class="absolute -top-1 right-2" @click="openTaskDrawer(task)">
          <span class="text-xs text-gray-500 dark:text-gray-400">View</span>          
        </button>
        <div class="flex items-start justify-between">
          <div>
            <div class="shrink-0 text-xs text-gray-500 dark:text-gray-400 uppercase">
              {{ task.privacy }}
            </div>
          	<div class="mb-2 text-xs text-gray-500 dark:text-gray-400">
          	  {{ task.tasklist?.project?.name }} • {{ task.tasklist?.name }}
          	</div>
            <h4 class="relative flex items-start gap-1 w-64 text-base text-gray-800 dark:text-white/90">
              <Flag 
                :class="{
                  'w-3 h-3 shrink-0': true,
                  'text-red-500': task.priority === 'high',
                  'text-yellow-500': task.priority === 'medium',
                  'text-blue-500' : task.priority === 'low'
                }" />
              <span class="flex-1 truncate">{{ task.name }}</span>
            </h4>
            <div class="flex items-center gap-x-3">
              <span class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                {{ formatDateRange(task.start_date, task.end_date) }}
              </span>
              <span class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                ETA {{ task.estimated_time }}
              </span>
            </div>            
            <div class="mt-2 flex flex-wrap gap-1">
              <span
                v-for="(tag, index) in task.tags"
                :key="index"
                :class="[
                  'px-2 py-0.5 text-xs font-medium rounded-full mt-3 inline-flex',
                  tag.color == null ? 'bg-brand-50 text-brand-500 dark:bg-brand-500/15 dark:text-brand-400' : 'text-white'
                ]"
                :style="tag.color ? { backgroundColor: tag.color } : null">
                {{ tag.label || 'Uncategorized' }}
              </span>
            </div>
            <div class="flex absolute bottom-2 right-2 items-center gap-2 text-xs text-gray-500 cursor-pointer dark:text-gray-400">
              <div class="inline-flex items-center gap-1">
                <Paperclip class="w-4 h-4" />
                <span class="leading-none"></span>
              </div>
              <div class="inline-flex items-center gap-1 comment-icon" @click="(e) => $emit('toggle-comment', task.id, task.name, e)" :data-task-id="task.id">
                <MessageCircle class="w-4 h-4" /> 
                <span class="leading-none">{{ task.comments_count || ''}}</span>
              </div>
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

  <Drawer v-model="showDrawer" :title="selectedTask?.name" :priority="selectedTask?.priority">    
    <template #body v-if="selectedTask">
      <TaskDrawer :task="selectedTask" :comments="comments" @edit-task="$emit('edit-task', $event)" />
    </template>
    <template #footer>
      <div class="space-y-2">
        <div class="flex items-start gap-3">
          <img 
            v-if="user.first_name && user.last_name"
            :src="`https://ui-avatars.com/api/?background=4961fe&color=fff&bold=true&name=${user.first_name}+${user.last_name}`"
            :alt="`${user.first_name} ${user.last_name}`"
            class="w-8 h-8 border-2 border-white rounded-full dark:border-gray-800" />            
          <div class="flex-1">
            <MentionTextarea 
              v-model="newComment" 
              :placeholder="'Type @ to mention'"
              :users="users" />
          </div>
        </div>
        <div class="flex justify-end">
          <button 
            type="button" 
            @click="postComment"
            class="rounded-full bg-brand-500 px-3 py-1.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">
            Post Comment
          </button>
        </div>
      </div>
    </template>
  </Drawer>
</template>

<style scoped>
	.cursor-grabbing {
	  cursor: grabbing !important;
	}
</style>
