<script setup>
  import { ref } from "vue"
  import { useRoute } from "vue-router"
  import AdminLayout from "@/components/layout/AdminLayout.vue"
  import PageBreadcrumb from "@/components/common/PageBreadcrumb.vue"
  import UsersTable from "@/views/Admin/Users/UsersTable.vue"

  const route = useRoute()
  const currentPageTitle = ref("User List") 
  const pageDescription = ref(route.meta.description || '')
</script>

<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" :pageDescription="pageDescription" />
    <div
      class="rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
      <UsersTable />
    </div>
  </AdminLayout>
</template>