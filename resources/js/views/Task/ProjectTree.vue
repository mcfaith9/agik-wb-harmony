<script setup>
  import { ref, onMounted } from "vue"
  import axios from "axios"
  import { 
    ChevronRight,
    Folder,
    FolderOpen,
    ClipboardList
  } from "lucide-vue-next"

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

  onMounted(fetchTree)

  const toggle = (item) => {
  	item.expanded = !item.expanded
  }
</script>

<template>
  <ul class="space-y-2">
    <li 
      v-for="project in projects" 
      :key="project.id" 
      class="p-5 bg-white border border-gray-200 task rounded-xl shadow-theme-sm dark:border-gray-800 dark:bg-white/5">
      <div @click="toggle(project)" class="cursor-pointer flex items-center gap-2 font-semibold text-md">        
        <ChevronRight 
          class="w-5 h-5 transition-transform duration-200" 
          :class="{ 'rotate-90': project.expanded }" />
          <Folder class="w-4 h-4 flex" />
          <p>{{ project.name }}</p>
      </div>
      <ul v-if="project.expanded" class="ml-2 mt-2 border-l pl-4 space-y-1">
        <li v-for="list in project.tasklists" :key="list.id">
          <div @click="toggle(list)" class="cursor-pointer flex items-center gap-1 text-md">            
            <ChevronRight 
              class="w-5 h-5 transition-transform duration-200" 
              :class="{ 'rotate-90': list.expanded }" />
              <FolderOpen class="w-4 h-4 flex" />
              <p>{{ list.name }} <span class="inline-flex rounded-full px-1 py-0.2 text-xs font-medium bg-brand-50 text-brand-500 dark:bg-brand-500/15 dark:text-brand-400">{{ list.tasks.length }}</span></p>
          </div>
          <ul v-if="list.expanded" class="ml-2 mt-1 space-y-1 border-l pl-4 text-md">
            <li v-for="task in list.tasks" :key="task.id">
              <div class="flex gap-1 items-center">
                <ClipboardList class="w-4 h-4 flex"/>
                <span>{{ task.name }}</span>
                <div class="flex flex-wrap">
                  <span
                    v-for="(tag, index) in task.tags"
                    :key="index"
                    class="px-2 py-0.5 text-xs font-medium bg-brand-50 text-brand-500 dark:bg-brand-500/15 dark:text-brand-400 inline-flex rounded-full text-xs font-medium">
                    {{ tag || 'Uncategorized' }}
                  </span>
                </div> 
              </div>
            </li>
          </ul>
        </li>
      </ul>
    </li>
  </ul>
</template>
