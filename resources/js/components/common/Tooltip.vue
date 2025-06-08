<script setup lang="ts">
  defineProps<{ text: string }>()
</script>

<template>
  <div class="relative group inline-flex justify-center items-center">
    <slot />
    <div class="absolute bottom-full w-max max-w-xs rounded bg-black px-2 py-1 text-xs text-white opacity-0 group-hover:opacity-100 transition-opacity z-50">
      {{ text }}
    </div>
  </div>
</template>
