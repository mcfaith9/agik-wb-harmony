<template>
  <div class="fixed inset-0 flex items-center justify-center overflow-y-auto modal z-99999">
    <div
      class="fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[2px]"
      aria-hidden="true"
      @click="$emit('close')"
    ></div>
    <slot name="body"></slot>
  </div>
</template>

<script setup>
  import { onMounted, onUnmounted } from 'vue'

  onMounted(() => {
    // Disable body scroll when modal mounts
    document.body.classList.add('overflow-hidden')
  })

  onUnmounted(() => {
    // Restore body scroll when modal unmounts
    document.body.classList.remove('overflow-hidden')
  })
</script>
