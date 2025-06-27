<script setup>
  import { ref, onMounted } from "vue"
  import axios from "axios"
  import { useHelpers } from "@/composables/useHelpers"

  const projectBudget = ref([])
  const { formatMoney } = useHelpers()

  onMounted(async () => {
    const { data } = await axios.get('/api/projects/budget')
    projectBudget.value = data
  })
</script>

<template>
  <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] py-2">
    <div class="overflow-y-auto custom-scrollbar h-[400px] px-2">
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 gap-4 auto-rows-fr p-3">
        <div
          v-for="project in projectBudget"
          :key="project.id"
          class="rounded-xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm dark:bg-white/5">
          <div class="flex justify-between items-center mb-2">
            <h3 class="text-md font-bold text-gray-700 dark:text-white truncate">{{ project.name }}</h3>
            <span class="text-xs text-gray-500 dark:text-gray-400">
              ₱ {{ formatMoney(project.remaining_budget) }} remaining
            </span>
          </div>

          <div class="h-1 w-full  bg-gray-200 dark:bg-gray-800 rounded-full overflow-hidden">
            <div
              class="h-full bg-green-500 transition-all duration-300"
              :style="{ width: `${(project.used_budget / project.total_budget) * 100}%` }"></div>
          </div>

          <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">
            Used: ₱ {{ formatMoney(project.used_budget) }} / ₱ {{ formatMoney(project.total_budget) }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>
