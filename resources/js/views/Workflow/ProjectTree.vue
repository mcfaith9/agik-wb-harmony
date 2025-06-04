<script setup lang="ts">
  import { ref, onMounted, computed } from "vue"
  import axios from "axios"
  import { userStore } from "@/stores/userStore"
  import TasklistForm from '@/views/Workflow/Modal/TasklistForm.vue'
  import Drawer from "@/components/common/Drawer.vue"
  import ProjectDrawer from "@/views/Workflow/ProjectDrawer.vue"
  import MentionTextarea from "@/components/common/MentionTextarea.vue"
  import { 
    ChevronRight,
    Folder,
    FolderOpen,
    Settings,
    CircleFadingPlus
  } from "lucide-vue-next"

  const props = defineProps<{
    users: Users[]
  }>()

  const newComment = ref<string>("")
  const user =  computed(() => userStore.user || {})

  const badgeClass = 'inline-flex items-center justify-center w-5 h-5 text-xs font-medium bg-brand-50 text-brand-500 dark:bg-brand-500/15 dark:text-brand-400 rounded-full'
  const projectRefs = {} as Record<number, HTMLElement>
  const projects = ref([])
  const addTasklistModal = ref<Boolean>(false)
  const tasklistParent = ref<{ id: number; name: string } | null>(null)
  const selectedTaskList = ref<TaskList | null>(null)
  const showDrawer = ref<boolean>(false)
  
  const fetchTree = async () => {
    const { data } = await axios.get('/api/projects')

    data.forEach((project, index) => {
      project.expanded = index < 2
      project.tasklists?.forEach(list => {
        list.expanded = index < 2
      })
    })

    projects.value = data
  }

  const scrollToProject = (projectId: number) => {
    const el = projectRefs[projectId]
    if (el) {
      el.scrollIntoView({ behavior: 'smooth', block: 'start' })
    }
  }

  const toggle = (item) => {
    item.expanded = !item.expanded
  }

  const openAddTasklistModal = (projectId: number, projectName: string) => {
    tasklistParent.value = { 'id': projectId, 'name': projectName }
    addTasklistModal.value = true
  }

  const onTasklistCreated = async () => {
    addTasklistModal.value = false
    await fetchTree()
  }

  const openTasklistDrawer = (tasklist: TaskList) => {
    showDrawer.value = true
    selectedTaskList.value = tasklist
  }

  onMounted(() => {
    fetchTree()
  })

  defineExpose({ 
    fetchTree 
  })
</script>

<template>
  <div class="grid grid-cols-9 divide-x divide-gray-200 dark:divide-gray-800">    
    <aside class="col-span-2 w-full max-w-xs overflow-y-auto custom-scrollbar max-h-[26rem]">
      <div class="flex items-center gap-2 text-sm font-semibold text-gray-800 dark:text-white/90">
        <Folder class="w-4 h-4" />
        <span>Categories</span>
        <Settings class="w-4 h-4 left-2" />
      </div>

      <ul class="space-y-3 px-2 mt-2">
        <li class="flex items-center text-sm text-gray-800 dark:text-white px-1 border-l border-gray-200 dark:border-white/10">
          <span class="w-4 h-4 mr-1 inline-block shrink-0 select-none"></span>
          <span class="font-medium flex-1 truncate">
            All Projects
          </span>
          <span :class="badgeClass" class="ml-auto shrink-0">
            {{ projects.length }}
          </span>
        </li>

        <li v-for="project in projects" :key="project.id" class="border-l border-gray-200 dark:border-white/10">
          <div
            @click="scrollToProject(project.id)" 
            class="cursor-pointer flex items-center text-sm text-gray-800 dark:text-white px-1">
            <span class="w-4 h-4 mr-1 inline-block"></span> 
            <span class="font-medium flex-1 truncate">
              {{ project.name }}
            </span>
            <span :class="badgeClass" class="ml-auto shrink-0">
              {{ project.tasklists.length }}
            </span>
          </div>

          <ul class="ml-4 mt-1 pl-3 space-y-1">
            <li
              v-for="list in project.tasklists"
              :key="list.id"
              class="flex items-center text-sm text-gray-600 dark:text-white/70 hover:bg-gray-50 dark:hover:bg-white/5 rounded px-1 py-0.5 transition">
              <FolderOpen class="w-4 h-4 text-gray-400 mr-1 shrink-0" />              
              <span class="flex-1 truncate">
                {{ list.name }}
              </span>
              <span :class="badgeClass" class="ml-auto shrink-0">
                {{ list.tasks.length }}
              </span>
            </li>
          </ul>
        </li>
      </ul>
    </aside>

    <section class="overflow-y-auto custom-scrollbar max-h-[26rem] col-span-7">
      <div class="px-4">
        <ul class="space-y-4">
          <li 
            v-for="project in projects" 
            :key="project.id" 
            :ref="el => projectRefs[project.id] = el"
            class="p-5 bg-white border border-gray-200 rounded-xl shadow-sm dark:border-gray-800 dark:bg-white/5">
            <div @click="toggle(project)" class="cursor-pointer flex items-center gap-2 text-sm text-gray-800 dark:text-white">
              <ChevronRight 
                class="w-5 h-5 transition-transform duration-200" 
                :class="{ 'rotate-90': project.expanded }" />
              <Folder class="w-4 h-4" />
              <span class="font-medium">{{ project.name }}</span>
            </div>

            <ul v-if="project.expanded" class="ml-2 mt-2 pl-4 space-y-2 border-l border-gray-200 dark:border-white/10">
              <li v-for="list in project.tasklists" :key="list.id" class="mb-3">
                <div class="cursor-pointer flex items-center gap-2 text-sm text-gray-800 dark:text-white">
                  <ChevronRight 
                    @click="toggle(list)"
                    class="w-5 h-5 transition-transform duration-200" 
                    :class="{ 'rotate-90': list.expanded }" />
                  <FolderOpen class="w-4 h-4" />
                  <span
                    @click="openTasklistDrawer(list)" 
                    class="font-medium">{{ list.name }}</span>
                  <span 
                    class="text-xs inline-flex items-center gap-1" 
                    v-show="list.task_counts?.todo">
                    <span class="w-2 h-2 rounded-full bg-gray-400 inline-block"></span>
                    {{ list.task_counts.todo }} To Do
                  </span>

                  <span 
                    class="text-xs inline-flex items-center gap-1" 
                    v-show="list.task_counts?.in_progress">
                    <span class="w-2 h-2 rounded-full bg-yellow-400 inline-block"></span>
                    {{ list.task_counts.in_progress }} In Progress
                  </span>

                  <span 
                    class="text-xs inline-flex items-center gap-1" 
                    v-show="list.task_counts?.completed">
                    <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
                    {{ list.task_counts.completed }} Completed
                  </span>
                </div>

                <ul v-if="list.expanded" class="ml-2 mt-2 space-y-2 text-sm text-gray-800 dark:text-white/90 border-l border-gray-200 dark:border-white/10 pl-4">
                  <li v-for="task in list.tasks" :key="task.id">
                    <div class="flex items-center gap-2">
                      <span
                        class="w-2 h-2 rounded-full mr-1"
                        :class="{
                          'bg-gray-400': task.status === 'todo',
                          'bg-yellow-400': task.status === 'in_progress',
                          'bg-green-500': task.status === 'completed'
                        }">
                      </span>
                      <span :class="{ 'line-through text-gray-400': task.status === 'completed'}">
                        {{ task.name }}
                      </span>
                      <div class="flex flex-wrap gap-1">
                        <span
                          v-for="(tag, index) in task.tags"
                          :key="index"
                          :class="[
                            'px-2 py-0.5 text-xs font-medium inline-flex rounded-full',
                            tag.color == null ? 'bg-brand-50 text-brand-500 dark:bg-brand-500/15 dark:text-brand-400' : 'text-white'
                          ]"
                          :style="tag.color ? { backgroundColor: tag.color } : null">
                          {{ tag.label || 'Uncategorized' }}
                        </span>
                      </div> 
                    </div>
                  </li>                  
                </ul>
              </li>
              <li class="mt-4 w-[25%]">
                <div class="flex items-center gap-2 mt-2">
                  <button
                    @click="openAddTasklistModal(project.id, project.name)"
                    class="w-full inline-flex items-center rounded-full border border-dashed border-gray-300 px-2 py-1 text-xs font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                    <CircleFadingPlus class="w-3 h-3 mr-1" />
                    <span>Create Tasklist</span>
                  </button>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>      
    </section>
  </div>

  <TasklistForm 
    :isOpen="addTasklistModal"
    @close="addTasklistModal = false"
    @created="onTasklistCreated"
    :project="tasklistParent" />

  <Drawer v-model="showDrawer" :title="selectedTaskList?.name" :priority="selectedTaskList?.priority">    
    <template #body v-if="selectedTaskList">
      <ProjectDrawer :list="selectedTaskList" />
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
