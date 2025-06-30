<script setup lang="ts">
	import McTable from "@/components/common/Table/McTable.vue"
	import { h } from "vue"
	import { TrendingUp, TrendingDown, Minus } from "lucide-vue-next"

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
	    accessorKey: 'tasks_assigned',
	    header: 'Tasks Assigned',
	    meta: { align: 'center' },
	    cell: info => h('div', { class: 'text-xs text-center' }, info.getValue())
	  },
	  {
	    accessorKey: 'in_progress_tasks',
	    header: 'In Progress',
	    meta: { align: 'center' },
	    cell: info => h('div', { class: 'text-xs text-center' }, info.getValue())
	  },
	  {
	    accessorKey: 'completed_last_week',
	    header: 'Last Week',
	    meta: { align: 'center' },
	    cell: info => h('div', { class: 'text-xs text-center' }, info.getValue())
	  },
	  {
	    accessorKey: 'completed_this_week',
	    header: 'This Week',
	    meta: { align: 'center' },
	    cell: info => h('div', { class: 'text-xs text-center' }, info.getValue())
	  },
	  {
	    accessorKey: 'productivity_trend',
	    header: 'Trend',
	    meta: { align: 'center' },
	    cell: info => {
	      const val = info.getValue()
	      const isNumber = typeof val === 'number' && !isNaN(val)

	      const isNegative = isNumber && val < 0
	      const isPositive = isNumber && val > 0
	      const isNeutral = isNumber && val === 0

	      return h('div', { class: 'flex items-center justify-center gap-1 text-xs' }, [
	        isPositive && h(TrendingUp, { class: 'w-4 h-4 text-green-500' }),
	        isNegative && h(TrendingDown, { class: 'w-4 h-4 text-red-500' }),
	        isNeutral && h(Minus, { class: 'w-4 h-4 text-gray-400' }),
	        h('span', {}, isNumber ? `${Math.abs(val)}%` : 'N/A')
	      ])
	    }
	  },
	  {
	    accessorKey: 'avg_completion_time_days',
	    header: 'Avg Days to Complete',
	    meta: { align: 'center' },
	    cell: info => h('div', { class: 'text-xs text-center' }, info.getValue())
	  },
	  {
	    accessorKey: 'high_priority_completed',
	    header: 'High Priority Done',
	    meta: { align: 'center' },
	    cell: info => h('div', { class: 'text-xs text-center' }, info.getValue())
	  },
	  {
	    accessorKey: 'completed_on_time_rate',
	    header: 'On-Time Rate',
	    meta: { align: 'center' },
	    cell: info => {
	      const val = info.getValue()
	      return h('div', { class: 'text-xs text-center' }, val !== null ? `${val}%` : 'N/A')
	    }
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

	const rowClass = row =>
	  row.productivity_trend !== null && row.productivity_trend < -50
	    ? 'bg-red-50'
	    : ''
</script>

<template>
  <McTable
    endpoint="/api/admin/health/productivity-drop"
    :columns="columns"
    :perPage="10"
    :transformData="transformData"
    :rowClass="rowClass"
  />
</template>
