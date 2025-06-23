<script setup>
  import { ref, onMounted } from "vue"
  import axios from "axios"
  import AdminLayout from "@/components/layout/AdminLayout.vue"
  import PageBreadcrumb from "@/components/common/PageBreadcrumb.vue"
  import ProjectBudget from "@/views/Admin/Accounting/Budget/ProjectBudget.vue"
  import BudgetSummaries from "@/views/Admin/Accounting/Budget/BudgetSummaries.vue"
  import BudgetGraph from "@/views/Admin/Accounting/Budget/BudgetGraph.vue"

  const currentPageTitle = ref("Accounting")
</script>

<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <div class="grid grid-cols-12 gap-4 md:gap-6">
      <div class="col-span-12 space-y-6 xl:col-span-7">
        <BudgetSummaries />
        <ProjectBudget />
      </div>
      <div class="col-span-12 xl:col-span-5">
        <BudgetGraph />
      </div>
    </div>
  </AdminLayout>
</template>
