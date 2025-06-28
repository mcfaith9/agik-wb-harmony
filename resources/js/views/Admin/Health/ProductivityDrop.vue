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
	  	accessorKey: 'completed_last_week', 
	  	header: 'Last Week',
	  	meta: { align: 'center' },
	  	cell: info =>
	  	  h('div', { class: 'text-xs text-center w-full' }, info.getValue())
	  },
	  { 
	  	accessorKey: 'completed_this_week', 
	  	header: 'This Week',
	  	meta: { align: 'center' },
	  	cell: info =>
	  	  h('div', { class: 'text-xs text-center w-full' }, info.getValue())
	  },
	  {
	    accessorKey: 'productivity_change_percent',
	    header: 'Change (%)',
	    meta: { align: 'center' },
	    cell: info => {
	      const val = info.getValue()
	      const className = val === null
	        ? 'text-xs text-center'
	        : val < 0
	        ? 'text-red-500'
	        : 'text-green-500'

	      return h('span', { class: className }, val !== null ? `${val}%` : 'N/A')
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
