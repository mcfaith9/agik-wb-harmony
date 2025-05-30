<script setup>
  import { ref, onMounted } from "vue"
  import axios from "axios"
  import { 
    ChevronRight,
    Folder,
    FolderOpen,
    Settings
  } from "lucide-vue-next"

  const badgeClass = 'inline-flex items-center justify-center w-5 h-5 text-xs font-medium bg-brand-50 text-brand-500 dark:bg-brand-500/15 dark:text-brand-400 rounded-full'

  const projects = ref([])
  
  const fetchTree = async () => {
    const { data } = await axios.get('/api/projects')

    data.forEach((project, index) => {
      project.expanded = index < 1
      project.tasklists?.forEach(list => {
        list.expanded = index < 1
      })
    })

    projects.value = data
  }

  const toggle = (item) => {
    item.expanded = !item.expanded
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
    <aside class="col-span-2 w-full max-w-xs overflow-y-auto">
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
          <div class="flex items-center text-sm text-gray-800 dark:text-white px-1">
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
            class="p-5 bg-white border border-gray-200 rounded-xl shadow-sm dark:border-gray-800 dark:bg-white/5">
            <div @click="toggle(project)" class="cursor-pointer flex items-center gap-2 text-sm text-gray-800 dark:text-white">
              <ChevronRight 
                class="w-5 h-5 transition-transform duration-200" 
                :class="{ 'rotate-90': project.expanded }" />
              <Folder class="w-4 h-4" />
              <span class="font-medium">{{ project.name }}</span>
            </div>

            <ul v-if="project.expanded" class="ml-2 mt-2 pl-4 space-y-2 border-l border-gray-200 dark:border-white/10">
              <li v-for="list in project.tasklists" :key="list.id">
                <div @click="toggle(list)" class="cursor-pointer flex items-center gap-2 text-sm text-gray-800 dark:text-white">
                  <ChevronRight 
                    class="w-5 h-5 transition-transform duration-200" 
                    :class="{ 'rotate-90': list.expanded }" />
                  <FolderOpen class="w-4 h-4" />
                  <span class="font-medium">{{ list.name }}</span>
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
            </ul>
          </li>
        </ul>
      </div>      
    </section>
  </div>
</template>
