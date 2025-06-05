<script setup>
  import { ref, watch, onMounted, computed } from 'vue'
  import { useRoles } from '@/composables/useRoles'
  import { useVueTable, getCoreRowModel } from '@tanstack/vue-table'
  import { Ellipsis, ChevronLeft, ChevronRight } from 'lucide-vue-next'

  const rolesAll = ref([])
  const loading = ref(true)
  const pageIndex = ref(0)
  const pageSize = ref(8)

  const { getRoles } = useRoles()

  const total = computed(() => rolesAll.value.length)
  const pageCount = computed(() => Math.ceil(total.value / pageSize.value))

  const paginatedRoles = computed(() => {
    const start = pageIndex.value * pageSize.value
    return rolesAll.value.slice(start, start + pageSize.value)
  })

  const from = computed(() => (total.value === 0 ? 0 : pageIndex.value * pageSize.value + 1))
  const to = computed(() => Math.min((pageIndex.value + 1) * pageSize.value, total.value))

  const fetchRoles = async () => {
    loading.value = true
    try {
      const { data } = await getRoles()
      rolesAll.value = data
    } catch (err) {
      console.error('Failed to fetch roles:', err)
    } finally {
      loading.value = false
    }
  }

  const formatAttributes = attrs => {
    if (!attrs || typeof attrs !== 'object') return []
      return Object.entries(attrs)
    .filter(([_, value]) => value)
    .map(([key]) => {
      const action = key.replace(/^can_/, '').replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())
      return `Can ${action}`
    })
  }

  const columns = [
    { accessorKey: 'label', header: 'Label' },
    { accessorKey: 'name', header: 'Name' },
    {
      id: 'attributes',
      header: 'Attributes',
      accessorFn: row => row.attributes ?? {},
      cell: info => formatAttributes(info.getValue()),
    }
  ]

  const table = useVueTable({
    get data() {
      return paginatedRoles.value
    },
    columns,
    getCoreRowModel: getCoreRowModel(),
    manualPagination: true,
    get pageCount() {
      return pageCount.value
    },
    state: {
      get pagination() {
        return {
          pageIndex: pageIndex.value,
          pageSize: pageSize.value,
        }
      },
    },
    onPaginationChange: (updater) => {
      const newState = typeof updater === 'function'
      ? updater({ pageIndex: pageIndex.value, pageSize: pageSize.value })
      : updater

      pageIndex.value = newState.pageIndex
      pageSize.value = newState.pageSize
    },
  })

  function paginationRange(currentPage, totalPages, siblingCount = 1) {
    const totalPageNumbers = siblingCount * 2 + 5
    if (totalPages <= totalPageNumbers) return Array.from({ length: totalPages }, (_, i) => i + 1)

      const leftSibling = Math.max(currentPage - siblingCount, 1)
    const rightSibling = Math.min(currentPage + siblingCount, totalPages)

    const showLeftEllipsis = leftSibling > 2
    const showRightEllipsis = rightSibling < totalPages - 1

    const pages = []

    if (leftSibling > 1) pages.push(1)
      if (showLeftEllipsis) pages.push('left-ellipsis')
        else for (let i = 2; i < leftSibling; i++) pages.push(i)

      for (let i = leftSibling; i <= rightSibling; i++) pages.push(i)

        if (showRightEllipsis) pages.push('right-ellipsis')
          else for (let i = rightSibling + 1; i < totalPages; i++) pages.push(i)

        if (rightSibling < totalPages) pages.push(totalPages)

          return pages
      }

  const pagesToShow = computed(() => paginationRange(pageIndex.value + 1, pageCount.value, 2))

  defineExpose({ refresh: fetchRoles })

  onMounted(fetchRoles)
  watch([pageIndex, pageSize], () => {})
</script>

<template>
  <div class="overflow-y-auto custom-scrollbar h-[400px]">
    <table class="min-w-full">
      <thead>
        <tr class="px-5 py-3 text-left w-2/11 sm:px-6">
          <th
            v-for="header in table.getHeaderGroups()[0].headers"
            :key="header.id"
            class="px-5 py-3 text-left w-2/11 sm:px-6">
            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
              {{ header.column.columnDef.header }}
            </p>
          </th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
        <tr
          v-for="row in table.getRowModel().rows"
          :key="row.id"
          class="border-t border-gray-100 dark:border-gray-800">
          <td v-for="cell in row.getVisibleCells()" :key="cell.id" class="px-3 py-2 sm:px-6">
            <template v-if="cell.column.id === 'attributes'">
              <span
                v-for="tag in formatAttributes(cell.row.original.attributes)"
                :key="tag"
                class="inline-block bg-blue-500 text-xs font-semibold mr-1 px-2.5 py-0.5 dark:text-gray-200">
                {{ tag }}
              </span>
            </template>
            <template v-else>
              <span class="text-sm text-gray-800 dark:text-white/90">
                {{ cell.getValue() }}
              </span>
            </template>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="mt-4 flex justify-between items-center text-sm">
    <div>
      <span class="text-gray-500 text-theme-sm dark:text-gray-400">
        Displaying {{ from }}-{{ to }} out of {{ total }} items
      </span>
    </div>
    <div class="space-x-1 flex items-center">
      <button
        :class="[
          'flex items-center justify-center text-gray-500 border-gray-200 rounded-lg z-99999 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 lg:h-8 lg:w-8 lg:border dark:border-gray-800',
          'disabled:opacity-25 disabled:pointer-events-none',
        ]"
        @click="table.previousPage()"
        :disabled="!table.getCanPreviousPage()">
        <ChevronLeft class="text-xs w-5 h-5" />
      </button>

      <template v-for="page in pagesToShow" :key="page">
        <button
          v-if="page === 'left-ellipsis' || page === 'right-ellipsis'"
          disabled
          class="cursor-default">
          <Ellipsis class="text-xs text-gray-400 w-5 h-5" />
        </button>
        <button
          v-else
          @click="pageIndex = page - 1"
          :class="[
            'flex items-center justify-center w-8 h-8 border-gray-200 rounded-lg dark:text-gray-400 lg:h-8 lg:w-8 lg:border dark:border-gray-800',
            pageIndex === page - 1
              ? 'text-white bg-blue-500 hover:bg-brand-600 dark:text-white'
              : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 lg:h-8 lg:w-8 lg:border dark:border-gray-800',
          ]">
          {{ page }}
        </button>
      </template>

      <button
        :class="[
          'flex items-center justify-center text-gray-500 border-gray-200 rounded-lg z-99999 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 lg:h-8 lg:w-8 lg:border dark:border-gray-800',
          'disabled:opacity-25 disabled:pointer-events-none',
        ]"
        @click="table.nextPage()"
        :disabled="!table.getCanNextPage()">
        <ChevronRight class="text-xs w-5 h-5" />
      </button>
    </div>
  </div>
</template>
