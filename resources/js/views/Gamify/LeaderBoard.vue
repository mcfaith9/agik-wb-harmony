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

  const leaderboard = ref<User[]>([])
  const topEarners = ref<TopEarners>({
    today: null,
    week: null,
    month: null,
  })

  const topEarnersArray = computed(() => [
    { label: 'Today', data: topEarners.value.today },
    { label: 'This Week', data: topEarners.value.week },
    { label: 'This Month', data: topEarners.value.month },
  ])

  function getBadgeImage(filename: string): string | undefined {
    return badgeMap[filename]
  }

  async function fetchLeaderboard() {
    try {
      const { data } = await axios.get('/api/leaderboard')
      leaderboard.value = data.leaderboard
      topEarners.value = data.top_earners
    } catch (error) {
      console.error('Failed to fetch leaderboard:', error)
    }
  }

  onMounted(fetchLeaderboard)
</script>

<template>
  <div class="rounded-2xl border border-gray-200 bg-gray-100 dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="px-3 pt-2 bg-white shadow-default rounded-2xl pb-11 dark:bg-gray-900 sm:px-6 sm:pt-6">
      <ul>
        <li
          v-for="(user, index) in leaderboard"
          :key="user.id"
          class="flex text-xs justify-between items-center px-4 py-3 border-b border-gray-200 dark:border-gray-800">
          <span class="font-semibold text-xs text-gray-500 dark:text-gray-400">
            <Medal
              v-if="index < 3"
              :class="[
                'w-4 h-4',
                index === 0 ? 'text-yellow-400' : '',
                index === 1 ? 'text-gray-400' : '',
                index === 2 ? 'text-amber-700' : '',
              ]" />
            <span v-else>{{ index + 1 }}.</span>
          </span>
          <span class="flex-1 ml-4 text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2">
            {{ user.first_name }} {{ user.last_name }}
            <img
              v-if="user.latest_badge?.icon"
              :src="getBadgeImage(user.latest_badge.icon)"
              :alt="user.latest_badge.name"
              class="w-6 h-6 rounded-full"
              loading="lazy" />
          </span>
          <span class="font-semibold text-blue-600 dark:text-success-500">{{ user.points_sum_points ?? 0 }} pts</span>
          <ArrowUp v-if="user.rank_change && user.rank_change > 0" class="text-green-500 w-3 h-3" />
          <ArrowDown v-else-if="user.rank_change && user.rank_change < 0" class="text-red-500 w-3 h-3" />
        </li>
      </ul>
    </div>

    <div class="flex items-center justify-center gap-5 px-6 py-3.5 sm:gap-8 sm:py-5">
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
