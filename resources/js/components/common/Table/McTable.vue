<script setup lang="ts">
  import { h, ref, watch, onMounted, computed, defineComponent } from 'vue'
  import { useVueTable, getCoreRowModel } from '@tanstack/vue-table'
  import { ChevronLeft, ChevronRight, Ellipsis } from 'lucide-vue-next'
  import axios from 'axios'

  const props = defineProps({
    endpoint: { type: String, required: true },
    columns: { type: Array, required: true },
    perPage: { type: Number, default: 10 },
    transformData: Function,
    fetchParams: { type: Object, default: () => ({}) },
    rowClass: Function,
    data: {
      type: Array,
      required: false,
      default: () => []
    }
  })  

  const emits = defineEmits(['loaded'])

  const internalData = ref(props.data?.length ? props.data : [])
  const loading = ref(true)
  const pageIndex = ref(0)
  const pageSize = ref(props.perPage)
  const pageCount = ref(1)
  const from = ref(0)
  const to = ref(0)
  const total = ref(0)

  const fetchData = async () => {
    loading.value = true
    try {
      const res = await axios.get(props.endpoint, {
        params: {
          page: pageIndex.value + 1,
          per_page: pageSize.value,
          ...props.fetchParams,
        },
      })
      const raw = res.data
      const responseData = props.transformData
        ? props.transformData(raw)
        : (raw.data && raw.total ? raw : { data: raw, total: raw.length || 0, from: 1, to: raw.length, last_page: 1 })
      internalData.value = responseData.data
      pageCount.value = responseData.last_page
      from.value = responseData.from
      to.value = responseData.to
      total.value = responseData.total

      emits('loaded', internalData.value)
    } catch (err) {
      console.error('Failed to fetch data:', err)
    } finally {
      loading.value = false
    }
  }

  const table = useVueTable({
    get data() {
      return internalData.value
    },
    columns: props.columns,
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
    onPaginationChange: updater => {
      const newState = typeof updater === 'function'
        ? updater({ pageIndex: pageIndex.value, pageSize: pageSize.value })
        : updater
      pageIndex.value = newState.pageIndex
      pageSize.value = newState.pageSize
    },
  })

  function paginationRange(currentPage, totalPages, siblingCount = 1) {
    const totalPageNumbers = siblingCount * 2 + 5
    if (totalPages <= totalPageNumbers) {
      return Array.from({ length: totalPages }, (_, i) => i + 1)
    }
    const leftSiblingIndex = Math.max(currentPage - siblingCount, 1)
    const rightSiblingIndex = Math.min(currentPage + siblingCount, totalPages)
    const shouldShowLeftEllipsis = leftSiblingIndex > 2
    const shouldShowRightEllipsis = rightSiblingIndex < totalPages - 1
    const pages = []
    if (leftSiblingIndex > 1) pages.push(1)
    if (shouldShowLeftEllipsis) pages.push('left-ellipsis')
    else for (let i = 2; i < leftSiblingIndex; i++) pages.push(i)
    for (let i = leftSiblingIndex; i <= rightSiblingIndex; i++) pages.push(i)
    if (shouldShowRightEllipsis) pages.push('right-ellipsis')
    else for (let i = rightSiblingIndex + 1; i < totalPages; i++) pages.push(i)
    if (rightSiblingIndex < totalPages) pages.push(totalPages)
    return pages
  }

  const pagesToShow = computed(() => {
    return paginationRange(pageIndex.value + 1, pageCount.value, 2)
  })

  onMounted(() => {
    setTimeout(fetchData, 0)
  })

  defineExpose({
    setData(newData) {
      internalData.value = [...newData]
    },
    refresh: fetchData
  })

  watch([pageIndex, pageSize], fetchData)

  watch(
    () => props.data,
    (newData) => {
      if (newData?.length) {
        internalData.value = [...newData]
      }
    },
    { deep: true }
  )
</script>

<template>
  <div class="overflow-y-auto custom-scrollbar h-[400px]">
    <table class="min-w-full">
      <thead>
        <tr>
          <th
            v-for="header in table.getHeaderGroups()[0].headers"
            :key="header.id"
            :class="[
              'px-5 py-3',
              header.column.columnDef.meta?.align === 'center' ? 'text-center' : 'text-left'
            ]">
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
          :class="[
            'border-t border-gray-100 dark:border-gray-800',
            props.rowClass?.(row.original)
          ]">
          <td
            v-for="cell in row.getVisibleCells()"
            :key="cell.id"
            :class="[
              'px-3 py-2 text-sm text-gray-800 dark:text-white/90',
              cell.column.columnDef.meta?.align === 'center' ? 'text-center' : '',
              cell.column.columnDef.meta?.class
            ]">
            <span v-if="typeof cell.column.columnDef.cell !== 'function'">
              {{ cell.getValue() }}
            </span>
            <component v-else :is="cell.column.columnDef.cell" v-bind="cell.getContext()" />
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
    <div class="space-x-1 flex items-center select-none">
      <button
        :class="[
          'flex items-center justify-center text-gray-500 border-gray-200 rounded-lg z-99999 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 lg:h-8 lg:w-8 lg:border dark:border-gray-800',
          'disabled:opacity-25 disabled:pointer-events-none'
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
              : 'text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 lg:h-8 lg:w-8 lg:border dark:border-gray-800'
          ]">
          {{ page }}
        </button>
      </template>

      <button
        :class="[
          'flex items-center justify-center text-gray-500 border-gray-200 rounded-lg z-99999 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 lg:h-8 lg:w-8 lg:border dark:border-gray-800',
          'disabled:opacity-25 disabled:pointer-events-none'
        ]"
        @click="table.nextPage()"
        :disabled="!table.getCanNextPage()">
        <ChevronRight class="text-xs w-5 h-5" />
      </button>
    </div>
  </div>
</template>
