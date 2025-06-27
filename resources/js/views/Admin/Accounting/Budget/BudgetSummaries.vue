<script setup lang="ts">
  import { ref, onMounted } from "vue"
  import axios from "axios"
  import { PiggyBank, Wallet, Goal, HandCoins } from "lucide-vue-next"

  const summaryData = ref([])

  onMounted(async () => {
    const { data } = await axios.get('/api/budget/summary')
    summaryData.value = data
  })
</script>

<template>
  <div class="grid grid-cols-1 gap-4 sm:grid-cols-4 md:gap-6">
    <!-- Total Budget -->
    <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
      <div class="flex items-center justify-between">      
        <div class="flex flex-col space-y-1">
          <div class="flex items-center space-x-2">
            <PiggyBank class="w-5 h-5 text-gray-500 dark:text-gray-400" />
            <h2 class="text-sm font-medium text-gray-700 dark:text-gray-300">Total Budget</h2>
          </div>
          <span class="text-xs text-gray-500 dark:text-gray-400 italic ml-7">
            Overall project limit
          </span>
        </div>

        <span class="text-base font-semibold text-gray-900 dark:text-white">₱ {{ summaryData.global_budget?.toLocaleString() || '0' }}</span>
      </div>
    </div>

    <!-- Total Expenses -->
    <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
      <div class="flex items-center justify-between">
        <div class="flex flex-col space-y-1">
          <div class="flex items-center space-x-2">
            <HandCoins class="w-5 h-5 text-gray-500 dark:text-gray-400" />
            <h2 class="text-sm font-medium text-gray-700 dark:text-gray-300">Total Expenses</h2>
          </div>
          <span class="text-xs text-gray-500 dark:text-gray-400 italic ml-7">
            Amount already spent
          </span>
        </div>
        <span class="text-base font-semibold text-gray-900 dark:text-white">₱ {{ summaryData.total_expenses?.toLocaleString() || '0' }}</span>
      </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-2">
          <Wallet class="w-5 h-5 text-gray-500 dark:text-gray-400" />
          <h2 class="text-sm font-medium text-gray-700 dark:text-gray-300">Remaining Budget</h2>
        </div>
        <span class="text-base font-semibold text-gray-900 dark:text-white">₱ {{ summaryData.remaining_budget?.toLocaleString() || '0' }}</span>
      </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
      <div class="flex items-start justify-between">
        <div class="flex flex-col space-y-1">
          <div class="flex items-center space-x-2">
            <Goal class="w-5 h-5 text-gray-500 dark:text-gray-400" />
            <h2 class="text-sm font-medium text-gray-700 dark:text-gray-300">Used Task Budget</h2>
          </div>
          <span class="text-xs text-gray-500 dark:text-gray-400 italic ml-7">Sum of all task budgets across projects</span>
        </div>
        <span class="text-base font-semibold text-emerald-600 dark:text-emerald-400">
          ₱ {{ summaryData.allocated_budget?.toLocaleString() || '0' }}
        </span>
      </div>
    </div>
  </div>
</template>
