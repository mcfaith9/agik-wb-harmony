<script setup lang="ts">
  import McTable from "@/components/common/McTable.vue"
  import { h } from "vue"

  const columns = [
    {
      id: 'user',
      header: 'User',
      cell: info => {
        const u = info.row.original.user
        return h('div', [
          h('div', { class: 'font-medium' }, `${u.first_name} ${u.last_name}`),
          h('div', { class: 'text-xs text-gray-500' }, u.email),
        ])
      }
    },
    {
      accessorKey: 'stalled_tasks',
      header: 'Stalled Tasks',
    }
  ]

  const transformData = data => ({
    data,
    last_page: 1,
    from: 1,
    to: data.length,
    total: data.length
  })
</script>

<template>
  <McTable
    endpoint="/api/admin/health/stalled-tasks"
    :columns="columns"
    :perPage="10"
    :transformData="transformData"
  />
</template>
