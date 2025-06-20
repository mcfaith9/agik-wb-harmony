<script setup lang="ts">
  import { ref, onMounted, computed } from 'vue'
  import badgeMap from '@/images/badges/badges'
  import axios from 'axios'
  import { ArrowUp, ArrowDown, Medal } from 'lucide-vue-next'

  interface Badge {
    id: number
    name: string
    icon: string
  }

  interface User {
    id: number
    first_name: string
    last_name: string
    points_sum_points: number | null
    rank_change?: number | null
    latest_badge?: Badge | null
  }

  interface TopEarners {
    today: User | null
    week: User | null
    month: User | null
  }

  const mode = ref<'user' | 'team'>('user')
  const userLeaderboard = ref<User[]>([])
  const teamLeaderboard = ref<User[]>([])
  const topEarners = ref<TopEarners>({
    today: null,
    week: null,
    month: null,
  })

  const leaderboard = computed(() => {
    return mode.value === 'user' ? userLeaderboard.value : teamLeaderboard.value
  })

  const loaded = ref<{ user: boolean; team: boolean }>({
    user: false,
    team: false,
  })

  const topEarnersArray = computed(() => {
    if (mode.value === 'team') return []
    if (!topEarners.value) return []
    return [
      { label: 'Today', data: topEarners.value.today },
      { label: 'This Week', data: topEarners.value.week },
      { label: 'This Month', data: topEarners.value.month },
    ]
  })

  function getBadgeImage(filename: string): string | undefined {
    return badgeMap[filename]
  }

  async function fetchLeaderboard(type: 'user' | 'team') {
    if (loaded.value[type]) return

    try {
      const { data } = await axios.get(
        type === 'user' ? '/api/leaderboard/users' : '/api/leaderboard/teams',
        { withCredentials: true }
      )

      console.log(`RAW ${type.toUpperCase()} DATA:`, JSON.stringify(data, null, 2))

      if (type === 'user') {
        userLeaderboard.value = data.leaderboard
        topEarners.value = data.top_earners
      } else {
        teamLeaderboard.value = data.teams
      }

      loaded.value[type] = true
    } catch (error) {
      console.error(`Failed to fetch ${type} leaderboard:`, error)
    }
  }

  function toggleMode() {
    mode.value = mode.value === 'user' ? 'team' : 'user'
  }

  onMounted(() => {
    if (!loaded.value.user) fetchLeaderboard('user')
    if (!loaded.value.team) fetchLeaderboard('team')
  })
</script>

<template>
  <div class="rounded-2xl border border-gray-200 bg-gray-100 dark:border-gray-800 dark:bg-white/[0.03] px-4 py-1">
    <div class="m-2 select-none">
      <label class="inline-flex items-center cursor-pointer">
        <input
          type="checkbox"
          class="sr-only peer"
          :checked="mode === 'user'"
          @change="toggleMode" />
        <div
          class="relative w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600 dark:peer-checked:bg-blue-600"></div>
        <span class="ms-3 text-xs font-medium text-gray-900 dark:text-gray-300">
          {{ mode === 'user' ? 'Users Leaderboard' : 'Teams Leaderboard' }}
        </span>
      </label>
    </div>
    <div class="px-3 pt-2 bg-white shadow-default rounded-2xl pb-11 dark:bg-gray-900 sm:px-6 sm:pt-6">
      <ul>
        <li
          v-for="(entry, index) in leaderboard"
          :key="entry.id"
          class="flex text-xs justify-between items-center px-4 py-3 border-b border-gray-200 dark:border-gray-800">
          <!-- RANK/MEDAL -->
          <span class="font-semibold text-xs text-gray-500 dark:text-gray-400">
            <Medal
              v-if="index < 3"
              :class="[
                'w-4 h-4',
                index === 0 ? 'text-yellow-400' : '',
                index === 1 ? 'text-gray-400' : '',
                index === 2 ? 'text-amber-700' : '',
              ]"
            />
            <span v-else>{{ index + 1 }}.</span>
          </span>

          <!-- NAME -->
          <span class="flex-1 ml-4 text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2">
            {{ mode === 'team' ? entry.name : `${entry.first_name} ${entry.last_name}` }}
            <img
              v-if="mode === 'user' && entry.latest_badge?.icon"
              :src="getBadgeImage(entry.latest_badge.icon)"
              :alt="entry.latest_badge.name"
              class="w-4 h-4 rounded-full"
              loading="lazy"
            />
          </span>

          <!-- POINTS -->
          <span class="font-semibold text-blue-600 dark:text-success-500">
            {{ mode === 'team' ? entry.total_points : (entry.points_sum_points ?? 0) }} pts
          </span>

          <!-- RANK CHANGE (users only) -->
          <ArrowUp
            v-if="mode === 'user' && entry.rank_change && entry.rank_change > 0"
            class="text-green-500 w-3 h-3" />
          <ArrowDown
            v-else-if="mode === 'user' && entry.rank_change && entry.rank_change < 0"
            class="text-red-500 w-3 h-3" />
        </li>
      </ul>
    </div>

    <div v-if="mode === 'user'" class="flex items-center justify-center gap-5 px-6 py-3.5 sm:gap-8 sm:py-5">
      <template v-for="(period, index) in topEarnersArray" :key="period.label">
        <div>
          <p class="text-xs mb-1 text-center text-gray-500 dark:text-gray-400">
            {{ period.label }}
          </p>
          <p class="flex items-center justify-center gap-1 text-xs font-semibold text-gray-800 dark:text-white/90">
            {{ period.data?.first_name ?? 'N/A' }}
            <span class="ml-2 font-mono text-blue-600 text-xs">{{ period.data?.total_points ?? 0 }} pts</span>
          </p>
        </div>

        <div
          v-if="index < topEarnersArray.length - 1"
          class="w-px bg-gray-200 h-7 dark:bg-gray-800"></div>
      </template>
    </div>
  </div>
</template>
