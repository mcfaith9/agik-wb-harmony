<script setup lang="ts">
  import { Cat, Goal, Circle } from 'lucide-vue-next'

  const hasPet = false
  const level = 3
  const xp = 75
  const maxXp = 100
</script>

<template>
  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:gap-6">
    <!-- My Pet -->
    <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-2">
          <Cat class="w-5 h-5 text-gray-500 dark:text-gray-400" />
          <h2 class="text-sm font-medium text-gray-700 dark:text-gray-300">My Pet</h2>
        </div>
        <span v-show="hasPet" class="text-xs text-gray-500 dark:text-gray-400">Lv. {{ level }}</span>
      </div>

      <div class="mt-4 flex flex-col items-center justify-center">
        <div v-if="hasPet">
          <img src="@/images/pet/Pet3.png" alt="My Pet" class="w-14 h-14 rounded-md mb-2" />
          <div class="w-full h-1 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
            <div class="h-full bg-indigo-500 transition-all duration-300"
              :style="{ width: (xp / maxXp * 100) + '%' }" />
          </div>
          <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
            {{ xp }} / {{ maxXp }} XP
          </div>
        </div>
        <div v-else class="text-sm text-gray-500 dark:text-gray-400 mt-2">No pet yet :'(</div>
      </div>
    </div>

    <div class="flex flex-col sm:flex-row items-stretch rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] space-y-4 sm:space-y-0 sm:space-x-6">
      <div class="flex-1 space-y-3">
        <div class="text-xs text-gray-500 dark:text-gray-400">Complete all objectives to earn your reward.</div>
        <div class="flex text-xs items-center text-gray-700 dark:text-gray-300">
          <Circle class="w-4 h-4 mr-2 text-gray-400 dark:text-gray-500" />
          <span class="inline-flex">Complete 5 tasks today.</span>
        </div>

        <div class="flex text-xs items-center text-gray-700 dark:text-gray-300">
          <Circle class="w-4 h-4 mr-2 text-gray-400 dark:text-gray-500" />
          <span class="inline-flex">Complete 3 high-priority tasks.</span>
        </div>
      </div>

      <div class="flex flex-col items-center justify-center w-full sm:w-30 bg-gray-50 dark:bg-gray-800 rounded-xl p-2 text-center">
        <span class="text-xs text-gray-500 dark:text-gray-400 mb-2">Reward:</span>
        <img src="@/images/pet/Pet6.png" width="64" height="64" class="rounded-md mb-2" alt="Reward Pet" />
        <button type="buttton" class="select-none px-3 py-1 text-xs font-medium text-white bg-indigo-500 rounded-md opacity-40 grayscale" disabled>
          Claim
        </button>
      </div>
    </div>
  </div>
</template>
