<script setup>
  import { ref } from "vue"
  import { useRoute } from "vue-router"
  import AdminLayout from "@/components/layout/AdminLayout.vue"
  import PageBreadcrumb from "@/components/common/PageBreadcrumb.vue"
  import UsersTable from "@/views/Admin/Users/UsersTable.vue"
  import { CircleFadingPlus } from "lucide-vue-next"

  const route = useRoute()
  const currentPageTitle = ref("User List") 
  const pageDescription = ref(route.meta.description || '')
</script>

<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" :pageDescription="pageDescription" />

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
      <div class="flex justify-end px-5 py-4">
        <button 
          class="inline-flex items-center gap-2 rounded-full bg-brand-500 px-3 py-1.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">
          Add Users
          <CircleFadingPlus class="w-4 h-4" />
        </button>
      </div>

      <div class="px-5 pb-6">
        <UsersTable />
      </div>
    </div>
  </AdminLayout>
</template>