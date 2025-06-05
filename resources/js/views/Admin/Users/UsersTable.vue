<script setup lang="ts">
	import { h, ref, watch, onMounted, computed, defineComponent } from 'vue'
	import axios from 'axios'
	import RolesTableDropdown from '@/views/Admin/Roles/RolesTableDropdown.vue'
	import { useRoles } from '@/composables/useRoles'
	import {
	  useVueTable,
	  getCoreRowModel
	} from '@tanstack/vue-table'
	import {
		Ellipsis,
		ChevronLeft,
		ChevronRight
	} from "lucide-vue-next"

	const allRoles = ref([])
	const userRolesMap = ref({})
	const users = ref([])
	const loading = ref(true)
	const pageIndex = ref(0)
	const pageSize = ref(8)
	const pageCount = ref(1)
	const from = ref(0)
	const to = ref(0)
	const total = ref(0)
	const { getRoles, getUserRoles, assignRole } = useRoles()

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

	    // Fetch roles per user
	    await Promise.all(users.value.map(async (user) => {
	      try {
	        const rolesResp = await getUserRoles(user.id)
	        userRolesMap.value[user.id] = rolesResp.data
	      } catch (err) {
	        userRolesMap.value[user.id] = []
	      }
	    }))
	  } catch (err) {
	    console.error('Failed to fetch users:', err)
	  } finally {
	    loading.value = false
	  }
	}	

	const fetchRoles = async () => {
	  try {
	    const response = await getRoles()
	    allRoles.value = response.data
	  } catch (err) {
	    console.error('Failed to fetch roles:', err)
	  }
	}

	const columns = [
	  { accessorKey: 'first_name', header: 'First Name' },
	  { accessorKey: 'last_name', header: 'Last Name' },
	  { accessorKey: 'email', header: 'Email' },
	  { accessorKey: 'phone', header: 'Phone', cell: info => info.getValue() || '' },
	  {
	    accessorKey: 'role',
	    header: 'Roles',
	    cell: info => {
	      const user = info.row.original
	      const roles = userRolesMap.value[user.id] || []
	      return roles.map(r => r.name).join(', ') || ''
	    }
	  },
	  {
      id: 'actions',
      header: '',
      cell: info => {
        const user = info.row.original
        return h(
          defineComponent({
            setup() {
              // pass props to RolesTableDropdown
              return () => h(RolesTableDropdown, {
                roles: allRoles.value,
                assignedRoles: userRolesMap.value[user.id] || [],
                userId: user.id,
                onAssignRole: async (roleId) => {
                  try {
                    await assignRole(user.id, roleId)
                    // refresh roles for this user
                    const resp = await getUserRoles(user.id)
                    userRolesMap.value[user.id] = resp.data
                  } catch (err) {
                    console.error('Failed to assign role:', err)
                  }
                }
              })
            }
          })
        )
      }
    }
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

	onMounted(() => {
	  fetchUsers()
	  fetchRoles()
	})
	watch([pageIndex, pageSize], fetchUsers)
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
          <td v-for="cell in row.getVisibleCells()" :key="cell.id" class="px-3 py-2 sm:px-6 text-sm text-gray-800 dark:text-white/90">
            <span class="text-sm text-gray-800 dark:text-white/90" v-if="typeof cell.column.columnDef.cell !== 'function'">
              {{ cell.getValue() }}
            </span>
            <component
            	class="px-3 py-2 sm:px-6"
              v-else
              :is="cell.column.columnDef.cell"
              v-bind="cell.getContext()" />
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
