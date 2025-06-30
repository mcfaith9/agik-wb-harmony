<script setup lang="ts">
  import McTable from "@/components/common/Table/McTable.vue"
  import { h } from "vue"
  import { AlertTriangle } from "lucide-vue-next"

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
      accessorKey: 'delayed_tasks',
      header: 'Delayed Tasks',
      meta: { align: 'center' },
      cell: info =>
        h('div', { class: 'text-xs' }, info.getValue())
    },
    {
      accessorKey: 'delayed_task_avg_age',
      header: 'Avg Task Age',
      meta: { align: 'center' },
      cell: info =>
        h('div', { class: 'text-xs' }, `${Math.round(info.getValue())} days`)
    },
    {
      accessorKey: 'delayed_task_max_age',
      header: 'Oldest Task',
      meta: { align: 'center' },
      cell: info =>
        h('div', { class: 'text-xs' }, `${Math.round(info.getValue())} days ago`)
    },
    {
      id: 'delayed_task_titles',
      header: 'Example Tasks',
      cell: info => {
        const titles = info.row.original.delayed_task_titles
        return h('ul', { class: 'text-xs text-gray-600 list-disc list-inside' },
          titles.map((title: string) => h('li', title))
          )
      }
    },
    {
      id: 'delayed_task_distribution',
      header: 'Status Breakdown',
      cell: info => {
        const dist = info.row.original.delayed_task_distribution
        if (!dist || Object.keys(dist).length === 0) return '—'
          return h('div', { class: 'text-xs' },
            Object.entries(dist).map(([status, count]: [string, number]) =>
              h('div', `${status.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())}: ${count}`)
              )
            )
      }
    },
    {
      accessorKey: 'has_high_priority_delayed',
      header: 'Critical Tasks Late',
      meta: { align: 'center' },
      cell: info => info.getValue()
        ? h('div', {
            class: 'text-xs flex items-center gap-1 justify-center text-red-600'
          }, [
            h(AlertTriangle, { class: 'w-4 h-4' }),
            h('span', 'Yes')
          ])
        : h('div', {
            class: 'text-xs text-gray-400 text-center'
          }, '—')
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
</script>

<template>
  <McTable
    endpoint="/api/admin/health/delayed-tasks"
    :columns="columns"
    :perPage="10"
    :transformData="transformData"
  />
</template>
