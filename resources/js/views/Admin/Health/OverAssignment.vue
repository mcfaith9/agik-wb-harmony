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
	    accessorKey: 'open_tasks', 
	    header: 'Open Tasks',
	    meta: { align: 'center' },
	    cell: info => h('div', { class: 'text-xs text-center w-full' }, info.getValue())
	  },
	  { 
	    accessorKey: 'conflicting_due_dates', 
	    header: 'Conflicting Due Dates',
	    meta: { align: 'center' },
	    cell: info => h('div', { class: 'text-xs text-center w-full' }, info.getValue())
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
