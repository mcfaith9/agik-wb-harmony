<script setup>
  import { ref } from "vue"
  import { useRoute } from "vue-router"
  import AdminLayout from "@/components/layout/AdminLayout.vue"
  import PageBreadcrumb from "@/components/common/PageBreadcrumb.vue"
  import RoleForm from "@/views/Admin/Roles/RoleForm.vue"
  import RolesTable from "@/views/Admin/Roles/RolesTable.vue"
  import { CircleFadingPlus } from "lucide-vue-next"

  const route = useRoute()
  const currentPageTitle = ref("Roles") 
  const pageDescription = ref(route.meta.description || "")
  const addRoleModal = ref(false)
  const rolesTableRef = ref()
</script>

<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" :pageDescription="pageDescription" />

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
      <div class="flex justify-end px-5 py-4">
        <button 
          @click="addRoleModal = true"
          class="inline-flex items-center gap-2 rounded-full bg-brand-500 px-3 py-1.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">
          Add Role
          <CircleFadingPlus class="w-4 h-4" />
        </button>
      </div>

      <div class="px-5 pb-6">
        <RolesTable ref="rolesTableRef" />
      </div>
    </div>

    <RoleForm 
      :isOpen="addRoleModal" 
      @close="addRoleModal = false"
      @created="(role) => {
        rolesTableRef?.handleCreated?.(role)
      }" />
  </AdminLayout>
</template>
