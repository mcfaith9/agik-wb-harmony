<script setup lang="ts">
  import { onMounted, ref } from 'vue'
  import badgeMap from '@/images/badges/badges'
  import axios from 'axios'
  import Tooltip from '@/components/common/Tooltip.vue'

  interface Badge {
    id: number
    name: string
    description: string
    icon?: string
    pivot: {
      awarded_at: string
    }
  }

  const allBadges = ref<Badge[]>([])
  const userBadges = ref<Badge[]>([])

  function getBadgeImage(filename: string): string | undefined {
    return badgeMap[filename]
  }

  function isUnlocked(badgeId: number): boolean {
    return userBadges.value.some(b => b.id === badgeId)
  }

  onMounted(async () => {
    const [all, user] = await Promise.all([
      axios.get('/api/badges'),
      axios.get('/api/user/badges'),
    ])
    allBadges.value = all.data
    userBadges.value = user.data
  })
</script>

<template>
  <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-5 pt-5 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6 sm:pt-6">
    <div class="flex items-center justify-between">
      <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">My Badges</h3>
    </div>

    <div class="max-h-[345px] overflow-y-auto overflow-x-hidden custom-scrollbar items-center">
      <div class="grid grid-cols-4 gap-2 mt-2 mb-3">
        <div
          v-for="badge in allBadges"
          :key="badge.id"
          class="flex flex-col p-3 items-center bg-white border border-gray-200 task rounded-xl shadow-theme-sm dark:border-gray-800 dark:bg-white/5
         w-full max-w-[11rem"
          :class="{ 'opacity-40 grayscale': !isUnlocked(badge.id) }">
          <Tooltip :text="badge.description">
            <div class="w-16 h-16 flex items-center justify-center transition-transform duration-200 hover:scale-110">
              <img
                v-if="badge.icon"
                :src="getBadgeImage(badge.icon)"
                alt="Badge Icon"
                class="w-10 h-10 object-contain"
              />
            </div>
          </Tooltip>

          <div class="text-sm font-medium text-gray-800 dark:text-white">
            {{ badge.name }}
          </div>
          <div
            class="text-xs"
            :class="isUnlocked(badge.id) ? 'text-gray-500 dark:text-gray-400' : 'text-gray-400 italic'">
            <template v-if="isUnlocked(badge.id)">
              Awarded: {{
                new Date(userBadges.find(b => b.id === badge.id)?.pivot.awarded_at || '').toLocaleDateString(
                  'en-US',
                  { year: 'numeric', month: 'long', day: 'numeric' }
                )
              }}
            </template>
            <template v-else>
              Required pts {{ badge.required_points }} 
            </template>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>