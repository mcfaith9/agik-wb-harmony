<script setup lang="ts">  
	import { format } from "date-fns"
	import { 
    CloudUpload,
    MessageCircleReply,
	} from "lucide-vue-next"

	const props = defineProps<{
	  task: Task,
    comments: Comments
	}>()

	const emit = defineEmits<{
	  (e: 'edit-task'): void,
	}>()

	const formatDate = (date: string, time = false) => {
    if (!date) return '';
    return format(new Date(date), time ? 'MMM d, yyyy hh:mm b' : 'MMM d, yyyy');
  }

  const avatar = (fname: string, lname: string) => {
    return `https://ui-avatars.com/api/?background=4961fe&color=fff&bold=true&name=${fname}+${lname}`
  }

  const highlightMentions = (message: string) => {
    return message.replace(/(@[A-Za-z]+(?:\s[A-Za-z]+)?)/g, '<span class="font-medium text-blue-500">$1</span>');
  }
</script>

<template>
  <div class="flex flex-col gap-6 sm:flex-row sm:justify-between">
    <div class="flex-1 space-y-4 overflow-hidden">
      <div>
        <button type="button" @click="emit('edit-task', task)">
          <span class="text-sm text-gray-500 dark:text-gray-400 underline">Edit</span>
        </button>
      </div>

      <div class="text-sm text-gray-600 dark:text-gray-300">        
        {{ task.tasklist?.project?.name }} • {{ task.tasklist?.name }}
      </div>

      <div class="flex gap-8">
      	<div>
      		<label class="block text-xs font-medium text-gray-700 dark:text-gray-400 mb-1">Privacy</label>
      		<span class="text-sm text-gray-600 uppercase dark:text-gray-300">
      		  {{ task.privacy }}
      		</span>
      	</div>
      	<div>
      	  <label class="block text-xs font-medium text-gray-700 dark:text-gray-400 mb-1">Start Date</label>
      	  <span class="text-sm text-gray-600 dark:text-gray-300">
      	    {{ formatDate(task.start_date) }}
      	  </span>
      	</div>
      	<div>
      		<label class="block text-xs font-medium text-gray-700 dark:text-gray-400 mb-1">Due Date</label>
      		<span class="text-sm text-gray-600 dark:text-gray-300">
      		  {{ formatDate(task.end_date) }}
      		</span>
      	</div>
      </div>

      <div class="flex gap-8">
        <div>
          <label class="block text-xs font-medium text-gray-700 dark:text-gray-400 mb-1">Priority</label>
          <div class="flex items-center gap-2">
            <span class="text-sm capitalize text-gray-600 dark:text-gray-300">{{ task.priority }}</span>
          </div>
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-700 dark:text-gray-400 mb-1">Estimated Time</label>
          <span class="text-sm text-gray-600 dark:text-gray-300">{{ task.estimated_time }}</span>
        </div>   
        <div>
          <label class="block text-xs font-medium text-gray-700 dark:text-gray-400 mb-1">Progress</label>
          <span class="text-sm text-gray-600 dark:text-gray-300">?</span>
        </div>     
      </div>

      <div>
        <label class="block text-xs font-medium text-gray-700 dark:text-gray-400 mb-1">Tags</label>
        <div class="max-h-24 block pr-1 flex flex-wrap gap-1">
          <span
            v-for="(tag, index) in task.tags"
            :key="index"
            :class="[
              'px-2 py-0.5 text-xs font-medium rounded-full inline-flex',
              tag.color == null ? 'bg-brand-50 text-brand-500 dark:bg-brand-500/15 dark:text-brand-400' : 'text-white'
            ]"
            :style="tag.color ? { backgroundColor: tag.color } : null">
            {{ tag.label || 'Uncategorized' }}
          </span>
        </div>
      </div>      
    </div>

    <div>
    	<label class="block text-xs font-medium text-gray-700 dark:text-gray-400 mb-1">Assignee</label>
    	<div class="flex items-start">
    	  <div class="flex -space-x-3 rtl:space-x-reverse">
    	    <template v-if="task.users?.length > 0">
    	      <template v-for="user in task.users" :key="user.id">
    	        <img
    	          v-if="user.first_name && user.last_name"                
    	          :src="avatar(user.first_name, user.last_name)"
    	          :alt="`${user.first_name} ${user.last_name}`"
    	          class="w-8 h-8 border-2 border-white rounded-full dark:border-gray-800"
    	        />
    	      </template>
    	    </template>
    	    <img
    	      v-else
    	      src="@/images/user/owner.jpg"
    	      alt="default avatar"
    	      class="w-8 h-8 border-2 border-white rounded-full dark:border-gray-800" />
    	  </div>
    	</div>
    </div>
  </div>  

  <div>
    <label class="block text-xs font-medium text-gray-700 dark:text-gray-400 mb-1">Files</label>
    <div class="flex items-center justify-center w-full">
      <label for="dropzone-file" class="flex flex-col items-center justify-center mt-2 w-full h-22 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-800 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
        <div class="flex flex-col items-center justify-center pt-6 pb-6">
            <CloudUpload class="w-6 h-6 mb-1 text-gray-500 dark:text-gray-400" />
            <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">
              <span class="font-semibold">Click to upload</span> or drag and drop</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">SVG, PNG, JPG or GIF (MAX. 800x400px)</p>
        </div>
        <input id="dropzone-file" type="file" class="hidden" />
      </label>
    </div>
  </div> 

  <div class="mt-6">
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
