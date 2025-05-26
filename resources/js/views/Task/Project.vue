<script setup>
	import { ref } from "vue"
	import PageBreadcrumb from "@/components/common/PageBreadcrumb.vue"
	import AdminLayout from "@/components/layout/AdminLayout.vue"
	import ComponentCard from "@/components/common/ComponentCard.vue"
	import AddProject from "@/views/Task/AddProject.vue"
	import ProjectTree from "@/views/Task/ProjectTree.vue"

	import { 
		Settings2, 
		CircleFadingPlus,
	  X
	} from "lucide-vue-next"

	const currentPageTitle = ref("Projects")
	const addProjectModal = ref(false)
</script>

<template>
	<AdminLayout>
	  <PageBreadcrumb :pageTitle="currentPageTitle" />
	  <div class="space-y-5 sm:space-y-6">
	    <ComponentCard title="List Project">
	    	<template #header-action>
    	    <div class="flex flex-wrap items-center gap-3 xl:justify-end">
            <button class="inline-flex items-center gap-2 rounded-full border border-dashed border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.03]">
              <Settings2 class="w-4 h-4" />
              Filter & Sort
            </button>
            <button @click="addProjectModal = true" class="inline-flex items-center gap-2 rounded-full bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">
              Add Project
              <CircleFadingPlus class="w-4 h-4" />
            </button>
          </div>
    	  </template>

    	  <div class="grid grid-cols-9 divide-x divide-gray-200 dark:border-gray-800 dark:divide-gray-800 sm:mt-0">	
    	    <div class="overflow-hidden col-span-2">
    	      <div class="p-2 xl:p-4">
    	        To do here....when?
    	      </div>
    	    </div>

    	    <div class="overflow-hidden col-span-7">
    	      <div class="p-2 xl:p-4">
    	      	<ProjectTree />
    	      </div>
    	    </div>
    	  </div>

    	  
	    </ComponentCard>
	  </div>

	  <AddProject :isOpen="addProjectModal" @close="addProjectModal = false" />
	</AdminLayout>
</template>