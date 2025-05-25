<script setup>
  defineProps({
    headers: {
      type: Array,
      required: true,
    },
    rows: {
      type: Array,
      required: true,
    },
  })
</script>

<template>
  <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="max-w-full overflow-x-auto custom-scrollbar">
      <table class="min-w-full">
        <thead>
          <tr class="border-b border-gray-200 dark:border-gray-700">
            <th
              v-for="(header, index) in headers"
              :key="index"
              :class="header.class"
            >
              <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                {{ header.label }}
              </p>
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
          <tr
            v-for="(row, rowIndex) in rows"
            :key="rowIndex"
            class="border-t border-gray-100 dark:border-gray-800">
            <td
              v-for="(header, colIndex) in headers"
              :key="colIndex"
              :class="header.class">
              <slot
                :name="`cell-${header.key}`"
                :value="row[header.key]"
                :row="row">
                {{ row[header.key] }}
              </slot>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
