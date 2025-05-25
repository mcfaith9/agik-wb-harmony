<script setup>
	import { ref } from "vue"
	import PageBreadcrumb from "@/components/common/PageBreadcrumb.vue"
	import AdminLayout from "@/components/layout/AdminLayout.vue"
	import ComponentCard from "@/components/common/ComponentCard.vue"
	import Table from "@/components/common/Table.vue"
	import AddProject from '@/views/Task/AddProject.vue'

	import { 
		Settings2, 
		CircleFadingPlus,
	  X
	} from "lucide-vue-next"

	const currentPageTitle = ref("Projects")
	const addProjectModal = ref(false)

	const users = ref([
	  {
	    name: 'Lindsey Curtis',
	    role: 'Web Designer',
	    avatar: '/images/user/user-17.jpg',
	    project: 'Agency Website',
	    team: ['/images/user/user-22.jpg', '/images/user/user-23.jpg', '/images/user/user-24.jpg'],
	    status: 'Active',
	    budget: '3.9K',
	  },
	  {
	    name: 'Kaiya George',
	    role: 'Project Manager',
	    avatar: '/images/user/user-18.jpg',
	    project: 'Technology',
	    team: ['/images/user/user-25.jpg', '/images/user/user-26.jpg'],
	    status: 'Pending',
	    budget: '24.9K',
	  },
	  {
	    name: 'Zain Geidt',
	    role: 'Content Writer',
	    avatar: '/images/user/user-19.jpg',
	    project: 'Blog Writing',
	    team: ['/images/user/user-27.jpg'],
	    status: 'Active',
	    budget: '12.7K',
	  },
	  {
	    name: 'Abram Schleifer',
	    role: 'Digital Marketer',
	    avatar: '/images/user/user-20.jpg',
	    project: 'Social Media',
	    team: ['/images/user/user-28.jpg', '/images/user/user-29.jpg', '/images/user/user-30.jpg'],
	    status: 'Cancel',
	    budget: '2.8K',
	  },
	  {
	    name: 'Carla George',
	    role: 'Front-end Developer',
	    avatar: '/images/user/user-21.jpg',
	    project: 'Website',
	    team: ['/images/user/user-31.jpg', '/images/user/user-32.jpg', '/images/user/user-33.jpg'],
	    status: 'Active',
	    budget: '4.5K',
	  },
	])

	const headers = [
	  { key: 'user', label: 'User', class: 'px-5 py-3 text-left w-3/11 sm:px-6' },
	  { key: 'project', label: 'Project Name', class: 'px-5 py-3 text-left w-2/11 sm:px-6' },
	  { key: 'team', label: 'Team', class: 'px-5 py-3 text-left w-2/11 sm:px-6' },
	  { key: 'status', label: 'Status', class: 'px-5 py-3 text-left w-2/11 sm:px-6' },
	  { key: 'budget', label: 'Budget', class: 'px-5 py-3 text-left w-2/11 sm:px-6' },
	]
</script>

<template>
	<AdminLayout>
	  <PageBreadcrumb :pageTitle="currentPageTitle" />
	  <div class="space-y-5 sm:space-y-6">
	    <ComponentCard title="Basic Table 1">
	    	<template #header-action>
    	    <div class="flex flex-wrap items-center gap-3 xl:justify-end">
    	      <button class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-white/[0.03]">
    	        <Settings2 class="w-4 h-4" />
    	        Filter & Sort
    	      </button>
    	      <button @click="addProjectModal = true" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">
    	        Add New Project
    	        <CircleFadingPlus class="w-4 h-4" />
    	      </button>
    	    </div>
    	  </template>

				<Table :headers="headers" :rows="users">
				  <template #cell-user="{ row }">
				    <div class="flex items-center gap-3">
				      <div class="w-10 h-10 overflow-hidden rounded-full">
				        <img :src="row.avatar" :alt="row.name" />
				      </div>
				      <div>
				        <span class="block font-medium text-gray-800 text-theme-sm dark:text-white/90">
				          {{ row.name }}
				        </span>
				        <span class="block text-gray-500 text-theme-xs dark:text-gray-400">
				          {{ row.role }}
				        </span>
				      </div>
				    </div>
				  </template>

				  <template #cell-team="{ value }">
				    <div class="flex -space-x-2">
				      <div
				        v-for="(member, memberIndex) in value"
				        :key="memberIndex"
				        class="w-6 h-6 overflow-hidden border-2 border-white rounded-full dark:border-gray-900"
				      >
				        <img :src="member" alt="team member" />
				      </div>
				    </div>
				  </template>

				  <template #cell-status="{ value }">
				    <span
				      :class="[
				        'rounded-full px-2 py-0.5 text-theme-xs font-medium',
				        {
				          'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-500': value === 'Active',
				          'bg-warning-50 text-warning-700 dark:bg-warning-500/15 dark:text-warning-400': value === 'Pending',
				          'bg-error-50 text-error-700 dark:bg-error-500/15 dark:text-error-500': value === 'Cancel',
				        },
				      ]"
				    >
				      {{ value }}
				    </span>
				  </template>
				</Table>
	    </ComponentCard>
	  </div>

	  <AddProject :isOpen="addProjectModal" @close="addProjectModal = false" />
	</AdminLayout>
</template>