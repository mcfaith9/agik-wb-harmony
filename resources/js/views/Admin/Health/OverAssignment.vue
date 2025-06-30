<script setup lang="ts">
	import McTable from "@/components/common/Table/McTable.vue"
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
	    accessorKey: 'open_tasks', 
	    header: 'Open Tasks',
	    meta: { align: 'center' },
	    cell: info => h('div', { class: 'text-xs text-center' }, info.getValue())
	  },
	  { 
	    accessorKey: 'high_priority_tasks',
	    header: 'High Priority',
	    meta: { align: 'center' },
	    cell: info => h('div', { class: 'text-xs text-center' }, info.getValue())
	  },
	  { 
	    accessorKey: 'conflicting_due_dates', 
	    header: 'Due Conflicts',
	    meta: { align: 'center' },
	    cell: info => h('div', { class: 'text-xs text-center' }, info.getValue())
	  },
	  { 
	    accessorKey: 'avg_task_age_days',
	    header: 'Avg Task Age',
	    meta: { align: 'center' },
	    cell: info => h('div', { class: 'text-xs text-center' }, `${info.getValue()}d`)
	  },
	  {
	    accessorKey: 'context_switching_risk',
	    header: 'Multitasking Pressure',
	    meta: { align: 'center' },
	    cell: info => {
	      const val = info.getValue()
	      return h('div', {
	        class: [
	          'text-xs text-center font-semibold',
	          val ? 'text-red-500' : 'text-green-500'
	        ]
	      }, val ? 'High' : 'Low')
	    }
	  },
	  {
	    accessorKey: 'tasks_due_soon',
	    header: 'Due in 7 Days',
	    meta: { align: 'center' },
	    cell: info => h('div', { class: 'text-xs text-center' }, info.getValue())
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

	const rowClass = row => {
	  const highConflict = row.conflicting_due_dates > 2
	  const highPriorityOverload = row.high_priority_tasks > 3
	  const contextSwitchRisk = row.context_switching_risk

	  if (highConflict || highPriorityOverload || contextSwitchRisk) {
	    return 'bg-gray-100 dark:bg-gray-700'
	  }

	  return ''
	}
</script>

<template>
  <McTable
    endpoint="/api/admin/health/over-assignment"
    :columns="columns"
    :perPage="10"
    :transformData="transformData"
    :rowClass="rowClass" />
</template>
