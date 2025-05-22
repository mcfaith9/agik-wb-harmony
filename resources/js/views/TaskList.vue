<script setup lang="ts">
	import { ref } from "vue";
	import PageBreadcrumb from "@/components/common/PageBreadcrumb.vue";
	import AdminLayout from "@/components/layout/AdminLayout.vue";

	const currentPageTitle = ref("Task List");
	
</script>

<template>
	<AdminLayout>
		<PageBreadcrumb :pageTitle="currentPageTitle" />
		<div>
			Task List
		</div>
	</AdminLayout>	
</template>