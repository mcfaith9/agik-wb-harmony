<script setup lang="ts">
  import McTable from '@/components/common/McTable.vue'
  import { h } from 'vue'

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
    { accessorKey: 'task_count', header: 'Total Tasks' },
    {
      accessorKey: 'overdue',
      header: 'Overdue',
      cell: info => h('span', { class: 'text-red-600' }, info.getValue())
    },
    {
      accessorKey: 'stale_in_progress',
      header: 'Stale In-Progress',
      cell: info => h('span', { class: 'text-yellow-600' }, info.getValue())
    },
    {
      accessorKey: 'high_priority',
      header: 'High Priority',
      cell: info => h('span', { class: 'text-orange-500' }, info.getValue())
    },
    {
      accessorKey: 'burnout_score',
      header: 'Burnout Score',
      cell: info => h('span', { class: 'font-semibold text-red-700' }, info.getValue().toFixed(1))
    }
  ]

  const transformData = data => ({
    data,
    last_page: 1,
    from: 1,
    to: data.length,
    total: data.length
  })

  const rowClass = row => row.burnout_score > 10 ? 'bg-red-50' : ''
</script>

<template>
  <div>
    <McTable
      endpoint="/api/admin/health/burnout"
      :columns="columns"
      :perPage="10"
      :transformData="transformData"
      :rowClass="rowClass" />
  </div>
</template>
