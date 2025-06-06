<script setup lang="ts">
  import { ref, computed } from 'vue'
  import { format, addDays, differenceInDays, parseISO, isBefore } from 'date-fns'
  import AdminLayout from "@/components/layout/AdminLayout.vue"
  import PageBreadcrumb from "@/components/common/PageBreadcrumb.vue"
  import GanttChart from "@/views/Workflow/GanttChart/GanttChart.vue"

  const currentPageTitle = ref("Gantt Chart")
</script>

<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
      <GanttChart />
    </div>
  </AdminLayout>
</template>
