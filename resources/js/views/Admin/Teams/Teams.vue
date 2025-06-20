<script lang="ts">
  export default {
    directives: {
      clickOutside: vClickOutside,
    },
  }
</script>

<script setup lang="ts">
  import { ref, onMounted, computed } from 'vue'
  import vClickOutside from '@/components/common/v-click-outside.vue'
  import { useTeams } from '@/composables/useTeams'
  import { users, fetchUsers } from '@/stores/allUsers'
  import { useVueTable, getCoreRowModel } from '@tanstack/vue-table'
  import { Ellipsis, ChevronLeft, ChevronRight, ChevronDown, UserRoundPlus, CircleCheck, Circle } from 'lucide-vue-next'

  // State
  const teamsAll = ref<Team[]>([])
  const loading = ref<boolean>(true)
  const pageIndex = ref<number>(0)
  const pageSize = ref<number>(3)
  const openDropdowns = ref<Users<number, boolean>>({})
  const expandedTasks = ref<Record<string, boolean>>({})  
  
  // Composable
  const { getTeams, addUserToTeam, removeUserFromTeam } = useTeams()

  // Pagination Calculations
  const total = computed(() => teamsAll.value.length)
  const pageCount = computed(() => Math.ceil(total.value / pageSize.value))

  const paginatedTeams = computed(() => {
    const start = pageIndex.value * pageSize.value
    return teamsAll.value.slice(start, start + pageSize.value)
  })

  const from = computed(() =>
    total.value === 0 ? 0 : pageIndex.value * pageSize.value + 1
  )
  const to = computed(() =>
    Math.min((pageIndex.value + 1) * pageSize.value, total.value)
  )

  // Data Fetch
  const fetchTeams = async () => {
    loading.value = true
    try {
      const { data } = await getTeams()
      teamsAll.value = data
    } catch (err) {
      console.error('Failed to fetch teams:', err)
    } finally {
      loading.value = false
    }
  }

  const toggleUserOnTeam = async (userId: number, teamId: number) => {
    const team = teamsAll.value.find(t => t.id === teamId)
    const isAdded = team?.users.some(user => user.id === userId)

    try {
      if (isAdded) {
        await removeUserFromTeam(teamId, userId)
      } else {
        await addUserToTeam(teamId, userId)
      }

      await fetchTeams()
      // Keep dropdown open so user can keep toggling
    } catch (error) {
      console.error('Error toggling user on team:', error)
    }
  }

  // Table Setup
  const columns = [
    { accessorKey: 'name', header: 'Team Name' },
    { accessorKey: 'created_at', header: 'Created At' },
  ]

  const isAlreadyAdded = (userId: number, teamUsers: User[]) => {
    return teamUsers.some(user => user.id === userId)
  }

  const toggleDropdown = (teamId: number) => {
    openDropdowns.value[teamId] = !openDropdowns.value[teamId]
  }

  const closeDropdown = (teamId: number) => {
    openDropdowns.value[teamId] = false
  }

  const getTeamUniqueTaskCount = (team: Team) => {
    const allTasks = team.users.flatMap(user => user.tasks.map(task => task.id))
    return new Set(allTasks).size
  }

  const getTeamTotalPoints = (team: Team) => {
    return team.users.reduce((sum, user) => sum + (user.total_points || 0), 0)
  }

  const getTaskKey = (teamId: number, userId: number) => `${teamId}-${userId}`

  const toggleUserTasks = (teamId: number, userId: number) => {
    const key = getTaskKey(teamId, userId)
    expandedTasks.value[key] = !expandedTasks.value[key]
  }

  const isUserTasksExpanded = (teamId: number, userId: number) => {
    return expandedTasks.value[getTaskKey(teamId, userId)]
  }

  const table = useVueTable({
    get data() {
      return paginatedTeams.value
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

  // Pagination Range Generator
  function paginationRange(currentPage, totalPages, siblingCount = 2) {
    const totalPageNumbers = siblingCount * 2 + 5
    if (totalPages <= totalPageNumbers)
      return Array.from({ length: totalPages }, (_, i) => i + 1)

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

  const pagesToShow = computed(() => paginationRange(pageIndex.value + 1, pageCount.value))

  // Expose refresh method
  defineExpose({ refresh: fetchTeams })

  onMounted(async () => {
    await fetchUsers()
    fetchTeams()
  })
</script>

<template>
  <div class="overflow-y-auto custom-scrollbar h-[400px] px-2">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 auto-rows-fr">
      <div
        v-for="team in table.getRowModel().rows"
        :key="team.id"
        class="p-4 bg-white border border-gray-200 task rounded-xl shadow-theme-sm dark:border-gray-800 dark:bg-white/5">
        <div class="flex items-center">
          <div class="text-lg font-semibold text-gray-800 dark:text-white mb-2">
            {{ team.original.name }}
          </div>          
          <div class="inline-flex ml-auto text-xs font-semibold text-gray-800 dark:text-white space-y-1 gap-x-3">
            <span>Team Score: {{ getTeamTotalPoints(team.original) }}</span>
            <span>Total Task: {{ getTeamUniqueTaskCount(team.original) }}</span>
          </div>
        </div>
        <div class="text-xs text-gray-500 dark:text-gray-400 mb-3">
          No Descrition
        </div>

        <div class="mt-4 text-sm text-gray-700 dark:text-gray-300">
          <div class="flex items-center gap-2 font-semibold mb-2">            
            <span class="text-md">Members</span>             
            <div class="relative" v-click-outside="() => closeDropdown(team.original.id)" ref="dropdown">
              <button 
                @click="toggleDropdown(team.original.id)"
                class="text-xs inline-flex items-center gap-2 rounded-full bg-brand-500 px-2 py-1 font-medium text-white shadow-theme-xs hover:bg-brand-600">
                Add Members
                <UserRoundPlus class="w-3.5 h-3.5" />
              </button> 

              <div v-if="openDropdowns[team.original.id]" class="mt-2 w-48 absolute right-0 z-40 p-2 space-y-1 bg-white border border-gray-200 top-full rounded-2xl shadow-lg dark:border-gray-800 dark:bg-gray-dark">
                <div v-if="users.length" class="h-52 overflow-y-auto custom-scrollbar">
                  <button
                    v-for="user in users"
                    :key="user.id"
                    @click="toggleUserOnTeam(user.id, team.original.id)"
                    class="flex items-centerpx-3 py-2 font-medium text-left text-gray-500 rounded-lg text-theme-xs hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                    <CircleCheck
                      v-if="isAlreadyAdded(user.id, team.original.users)"
                      class="w-4 h-4 text-green-500 shrink-0 mr-1" />
                    <Circle
                      v-else
                      class="w-4 h-4 text-gray-100 shrink-0 mr-1 opacity-25" />
                    <span>{{ user.name }}</span>          
                  </button>
                </div>
                <p v-else class="px-3 py-2 text-gray-500 text-theme-xs">No users available</p>
              </div>
            </div>
          </div>
          <ul class="pl-1">
            <li
              v-for="user in team.original.users"
              :key="user.id"
              class="mb-2">
              <div class="flex items-center justify-between">
                <span class="font-semibold text-gray-800 dark:text-white">
                  {{ user.full_name }}
                </span>
                <button
                  @click="toggleUserTasks(team.original.id, user.id)"
                  class="text-xs text-brand-600 hover:underline">
                  <component
                    :is="isUserTasksExpanded(team.original.id, user.id) ? ChevronDown : ChevronRight"
                    class="w-4 h-4 text-gray-600 dark:text-gray-300" />
                </button>
              </div>
              <ul
                v-if="isUserTasksExpanded(team.original.id, user.id)"
                class="pl-1 mt-1">
                <li v-for="task in user.tasks" :key="task.id">
                  <div class="flex items-center gap-1">
                    <span
                      class="w-2 h-2 rounded-full mr-1"
                      :class="{
                        'bg-gray-400': task.status === 'todo',
                        'bg-yellow-400': task.status === 'in_progress',
                        'bg-green-500': task.status === 'completed'
                      }">
                    </span>
                    <span :class="{ 'line-through text-gray-400': task.status === 'completed'}">
                      {{ task.name }}
                    </span>
                  </div>
                </li>
                <li v-if="user.tasks.length === 0">
                  <em class="text-gray-400">No tasks assigned.</em>
                </li>
              </ul>
            </li>

            <li v-if="!team.original.users || team.original.users.length === 0">
              <em class="text-sm text-gray-400">No users in this team.</em>
            </li>
          </ul>
        </div>
      </div>
    </div>
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
