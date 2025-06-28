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
    { 
      accessorKey: 'task_count', 
      header: 'Total Tasks', 
      meta: { align: 'center' },
      cell: info => h('span', { class: 'text-xs' }, info.getValue())
    },
    {
      accessorKey: 'overdue',
      header: 'Overdue',
      meta: { align: 'center' },
      cell: info => h('span', { class: 'text-xs text-red-600' }, info.getValue())
    },
    {
      accessorKey: 'stale_in_progress',
      header: 'Aging Tasks',
      meta: { align: 'center' },
      cell: info => h('span', { class: 'text-xs text-yellow-600' }, info.getValue())
    },
    {
      accessorKey: 'high_priority',
      header: 'High Priority',
      meta: { align: 'center' },
      cell: info => h('span', { class: 'text-xs text-orange-500' }, info.getValue())
    },
    {
      accessorKey: 'burnout_score',
      header: 'Burnout Score',
      meta: { align: 'center' },
      cell: info => h('span', { class: 'text-sm font-semibold text-red-700' }, info.getValue().toFixed(1))
    }
  ]

  const transformData = response => ({
    data: response.data,
    from: response.from,
    to: response.to,
    total: response.total,
    last_page: response.last_page,
    current_page: response.current_page,
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
