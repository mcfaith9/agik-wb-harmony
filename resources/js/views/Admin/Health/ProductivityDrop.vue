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
	  { accessorKey: 'completed_last_week', header: 'Last Week' },
	  { accessorKey: 'completed_this_week', header: 'This Week' },
	  {
	    accessorKey: 'productivity_change_percent',
	    header: 'Change (%)',
	    cell: info => {
	      const val = info.getValue()
	      const className = val === null
	        ? ''
	        : val < 0
	        ? 'text-red-500'
	        : 'text-green-500'

	      return h('span', { class: className }, val !== null ? `${val}%` : 'N/A')
	    }
	  }
	]

	const transformData = data => ({
	  data,
	  last_page: 1,
	  from: 1,
	  to: data.length,
	  total: data.length
	})

	const rowClass = row =>
	  row.productivity_change_percent !== null && row.productivity_change_percent < -50
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
