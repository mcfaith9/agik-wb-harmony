<script setup lang="ts">
	import { ref } from "vue"
	import PageBreadcrumb from "@/components/common/PageBreadcrumb.vue"
	import AdminLayout from "@/components/layout/AdminLayout.vue"
	import ComponentCard from "@/components/common/ComponentCard.vue"
	import ProjectForm from "@/views/Workflow/Modal/ProjectForm.vue"
	import CreateTags from "@/views/Workflow/Modal/CreateTags.vue"
	import ProjectTree from "@/views/Workflow/ProjectTree.vue"
	import TasklistForm from '@/views/Workflow/Modal/TasklistForm.vue'
	import { users } from '@/stores/allUsers'

	import { 
		Settings2, 
		CircleFadingPlus,
	  X,
	  Tags
	} from "lucide-vue-next"

	const currentPageTitle = ref("Projects")
	const addProjectModal = ref<Boolean>(false)
	const createTagsModal = ref<Boolean>(false)
	const addTasklistModal = ref<Boolean>(false)
	const projectTreeRef = ref()

	const refreshProjectTree = () => {
	  projectTreeRef.value?.fetchTree()
	}
</script>

<template>
	<AdminLayout>
	  <PageBreadcrumb :pageTitle="currentPageTitle" />
	  <div class="space-y-5 sm:space-y-6">
	    <ComponentCard title="">
	    	<template #header-action>
	    		<div class="flex flex-col w-full gap-5 sm:justify-between xl:flex-row xl:items-center">	
				    <div class="flex flex-wrap items-center gap-3 xl:justify-end">   	    	
			        <button class="inline-flex items-center gap-2 rounded-full border border-dashed border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.03]">
			          <Settings2 class="w-4 h-4" />
			          Filter & Sort
			        </button>
			        <button @click="createTagsModal = true" class="inline-flex items-center gap-2 rounded-full bg-brand-500 px-3 py-1.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">
			          Tags
			          <Tags class="w-4 h-4" />
			        </button>
			        <button @click="addTasklistModal = true" class="inline-flex items-center gap-2 rounded-full bg-brand-500 px-3 py-1.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">
			          Tasklist
			          <CircleFadingPlus class="w-4 h-4" />
			        </button>
			        <button @click="addProjectModal = true" class="inline-flex items-center gap-2 rounded-full bg-brand-500 px-3 py-1.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">
			          Add Project
			          <CircleFadingPlus class="w-4 h-4" />
			        </button>
			      </div>
	    		</div>
    	  </template>

    	  <ProjectTree ref="projectTreeRef" :users="users" />	    
    	</ComponentCard>
	  </div>

	  <ProjectForm 
	  	:isOpen="addProjectModal" 
	  	@close="addProjectModal = false" 
	  	@created="refreshProjectTree" />

	  <CreateTags 
	  	:isOpen="createTagsModal" 
	  	@close="createTagsModal = false" />

	  <TasklistForm 
	    :isOpen="addTasklistModal"
	    @close="addTasklistModal = false"
	    @created="refreshProjectTree" />
	</AdminLayout>
</template>