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
	  { accessorKey: 'open_tasks', header: 'Open Tasks' },
	  { accessorKey: 'conflicting_due_dates', header: 'Conflicting Due Dates' }
	]

	const transformData = data => ({
	  data,
	  last_page: 1,
	  from: 1,
	  to: data.length,
	  total: data.length
	})

	const rowClass = row =>
	  row.conflicting_due_dates > 2 ? 'bg-orange-50' : ''
</script>

<template>
  <McTable
    endpoint="/api/admin/health/over-assignment"
    :columns="columns"
    :perPage="10"
    :transformData="transformData"
    :rowClass="rowClass" />
</template>
