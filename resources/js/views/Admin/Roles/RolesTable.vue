<script setup lang="ts">
import { h } from 'vue'
import McTable from '@/components/common/McTable.vue'
import { Ellipsis } from "lucide-vue-next"

// Format attribute keys like "can_edit_users" => "Can Edit Users"
function formatAttributes(attrs) {
  if (!attrs || typeof attrs !== 'object') return []
  return Object.entries(attrs)
    .filter(([_, value]) => value)
    .map(([key]) => {
      const label = key.replace(/^can_/, '')
                       .replace(/_/g, ' ')
                       .replace(/\b\w/g, l => l.toUpperCase())
      return `Can ${label}`
    })
}

const columns = [
  { accessorKey: 'label', header: 'Label', meta: { class: 'w-1/4' } },
  { accessorKey: 'name', header: 'Name', meta: { class: 'w-1/4' } },
  {
    id: 'attributes',
    header: 'Attributes',
    cell: info => {
      const tags = formatAttributes(info.row.original.attributes)
      return h(
        'div',
        tags.map(tag =>
          h('span', {
            class:
              'inline-block bg-blue-500 text-xs font-semibold mr-1 mb-1 px-2.5 py-0.5 dark:text-gray-200'
          }, tag)
        )
      )
    },
    meta: { class: 'w-2/4' }
  },
  {
    id: 'edit',
    header: '',
    cell: info =>
      h(
        'button',
        {
          class: 'text-gray-500 hover:text-blue-500 p-1',
          onClick: () => {
            const row = info.row.original
            console.log('Edit clicked:', row)
            // You can emit an event or open a modal here
          }
        },
        [ h(Ellipsis, { class: 'w-5 h-5' }) ]
      ),
    meta: { class: 'text-right w-[40px]' }
  }
]

const transformData = (response) => ({
  data: response.data,
  from: response.from,
  to: response.to,
  total: response.total,
  last_page: response.last_page,
  current_page: response.current_page
})
</script>

<template>
  <McTable
    ref="tableRef"
    :columns="columns"
    :transformData="transformData"
    endpoint="/api/roles"
    :perPage="10"
  />
</template>
