<script setup lang="ts">
  import { ref, onMounted } from 'vue'
  import axios from 'axios'
  import { format, addDays, differenceInDays, parseISO, isBefore } from 'date-fns'
  import AdminLayout from "@/components/layout/AdminLayout.vue"
  import PageBreadcrumb from "@/components/common/PageBreadcrumb.vue"
  import GanttChart from "@/views/Workflow/GanttChart/GanttChart.vue"

  const currentPageTitle = ref("Task Gantt Chart")  
  const tasks = ref<Task>([])

  function updateTask({ id, start, end }) {
    const idx = tasks.value.findIndex(t => t.id === id)
    if (idx !== -1) {
      tasks.value[idx] = { ...tasks.value[idx], start, end }
    }
  }

  onMounted(async () => {
    const res = await axios.get("/api/tasks")
    tasks.value = res.data.map((t) => ({
      id: t.id,
      name: t.name,
      start: t.start_date || '2025-06-01',
      end: t.end_date || '2025-06-05',
      progress: t.progress || '0%',
      status: t.status,
      users: t.users || [],
    }))
    console.log(tasks.value)
  })
</script>

<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
      <GanttChart :tasks="tasks" @update-task="updateTask"/>
    </div>
  </AdminLayout>
</template>
