<script setup>
	import { ref, watch, onMounted, computed } from 'vue'
	import axios from 'axios'
	import {
	  useVueTable,
	  getCoreRowModel
	} from '@tanstack/vue-table'

	import {
		Ellipsis,
		ChevronLeft,
		ChevronRight
	} from "lucide-vue-next"

	const users = ref([])
	const loading = ref(true)
	const pageIndex = ref(0)
	const pageSize = ref(8)
	const pageCount = ref(1)
	const from = ref(0)
	const to = ref(0)
	const total = ref(0)

	const fetchUsers = async () => {
	  loading.value = true
	  try {
	    const response = await axios.get('/api/admin/users/list', {
	      params: {
	        page: pageIndex.value + 1,
	        per_page: pageSize.value,
	      },
	    })
	    users.value = response.data.data
	    pageCount.value = response.data.last_page

	    from.value = response.data.from
	    to.value = response.data.to
	    total.value = response.data.total
	  } catch (err) {
	    console.error('Failed to fetch users:', err)
	  } finally {
	    loading.value = false
	  }
	}

	onMounted(fetchUsers)
	watch([pageIndex, pageSize], fetchUsers)

	const columns = [
	  { accessorKey: 'first_name', header: 'First Name' },
	  { accessorKey: 'last_name', header: 'Last Name' },
	  { accessorKey: 'email', header: 'Email' },
	  { accessorKey: 'phone', header: 'Phone', cell: info => info.getValue() || '-' },
	  // { accessorKey: 'email_verified_at', header: 'Verified', cell: 'info => info.getValue()' ? '✔' : '✘' },
	]

	const table = useVueTable({
	  get data() {
	    return users.value
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
	  onPaginationChange: updater => {
	    const newState = typeof updater === 'function'
	      ? updater({ pageIndex: pageIndex.value, pageSize: pageSize.value })
	      : updater

	    pageIndex.value = newState.pageIndex
	    pageSize.value = newState.pageSize
	  },
	})

	// Helper function to create page range with ellipsis
	function paginationRange(currentPage, totalPages, siblingCount = 1) {
	  const totalPageNumbers = siblingCount * 2 + 5;

	  if (totalPages <= totalPageNumbers) {
	    // Show all pages if total is small
	    return Array.from({ length: totalPages }, (_, i) => i + 1);
	  }

	  const leftSiblingIndex = Math.max(currentPage - siblingCount, 1);
	  const rightSiblingIndex = Math.min(currentPage + siblingCount, totalPages);

	  const shouldShowLeftEllipsis = leftSiblingIndex > 2;
	  const shouldShowRightEllipsis = rightSiblingIndex < totalPages - 1;

	  const pages = [];

	  if (leftSiblingIndex > 1) {
	    pages.push(1);
	  }

	  if (shouldShowLeftEllipsis) {
	    pages.push('left-ellipsis');
	  } else {
	    for (let i = 2; i < leftSiblingIndex; i++) {
	      pages.push(i);
	    }
	  }

	  for (let i = leftSiblingIndex; i <= rightSiblingIndex; i++) {
	    pages.push(i);
	  }

	  if (shouldShowRightEllipsis) {
	    pages.push('right-ellipsis');
	  } else {
	    for (let i = rightSiblingIndex + 1; i < totalPages; i++) {
	      pages.push(i);
	    }
	  }

	  if (rightSiblingIndex < totalPages) {
	    pages.push(totalPages);
	  }

	  return pages;
	}

	const pagesToShow = computed(() => {
	  return paginationRange(pageIndex.value + 2, pageCount.value, 2)
	})
</script>

<template>
  <div class="overflow-y-auto custom-scrollbar h-[400px]">
    <table class="min-w-full">
      <thead>
        <tr class="px-5 py-3 text-left w-2/11 sm:px-6">
          <th v-for="header in table.getHeaderGroups()[0].headers"
              :key="header.id"
              class="px-5 py-3 text-left w-2/11 sm:px-6">
            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
              {{ header.column.columnDef.header }}
            </p>
          </th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
        <tr v-for="row in table.getRowModel().rows" :key="row.id" class="border-t border-gray-100 dark:border-gray-800">
          <td v-for="cell in row.getVisibleCells()" :key="cell.id" class="px-3 py-2 sm:px-6">
            <span class="text-gray-500 text-theme-sm dark:text-gray-400">
              {{ cell.getValue() }}
            </span>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="mt-4 flex justify-between items-center text-sm">
    <div>
      Displaying {{ from }}-{{ to }} out of {{ total }} items
    </div>
    <div class="space-x-1 flex items-center">
      <button
        :class="[
          'flex items-center justify-center text-gray-500 border-gray-200 rounded-lg z-99999 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 lg:h-8 lg:w-8 lg:border dark:border-gray-800',
          'disabled:opacity-50 disabled:pointer-events-none'
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
            'flex items-center justify-center w-8 h-8 text-gray-500 border-gray-200 rounded-lg dark:text-gray-400 lg:h-8 lg:w-8 lg:border dark:border-gray-800',
            pageIndex === page - 1
              ? 'bg-blue-500 text-white hover:bg-brand-600'
              : 'bg-white text-gray-700 hover:bg-gray-100 hover:bg-brand-600'
          ]">
          {{ page }}
        </button>
      </template>

      <button
        :class="[
          'flex items-center justify-center text-gray-500 border-gray-200 rounded-lg z-99999 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 lg:h-8 lg:w-8 lg:border dark:border-gray-800',
          'disabled:opacity-50 disabled:pointer-events-none'
        ]"
        @click="table.nextPage()"
        :disabled="!table.getCanNextPage()">
        <ChevronRight class="text-xs w-5 h-5" />
      </button>
    </div>
  </div>
</template>
