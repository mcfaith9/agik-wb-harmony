<script setup lang="ts">
  interface BreadcrumbProps {
    pageTitle: string,
    pageDescription?: string, 
  }

  defineProps<BreadcrumbProps>()
</script>

<template>
  <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
    <div>
      <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
        {{ pageTitle }}
      </h2>
      <p v-if="pageDescription" class="text-xs text-gray-500 dark:text-gray-400">
        {{ pageDescription }}
      </p>
    </div>

    <nav class="shrink-0">
      <ol class="flex items-center gap-1.5">
        <li>
          <router-link
            class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400"
            to="/">
            Home
            <svg
              class="stroke-current"
              width="17"
              height="16"
              viewBox="0 0 17 16"
              fill="none"
              xmlns="http://www.w3.org/2000/svg">
              <path
                d="M6.0765 12.667L10.2432 8.50033L6.0765 4.33366"
                stroke=""
                stroke-width="1.2"
                stroke-linecap="round"
                stroke-linejoin="round" />
            </svg>
          </router-link>
        </li>
        <li class="text-sm text-gray-800 dark:text-white/90">
          {{ pageTitle }}
        </li>
      </ol>
    </nav>
  </div>
</template>
