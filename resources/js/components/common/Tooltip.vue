<script setup lang="ts">
  import { ref, onMounted, onUnmounted } from 'vue'

  const props = defineProps<{ text: string }>()

  const tooltipRef = ref<HTMLElement | null>(null)
  const visible = ref(false)
  const position = ref({ top: 0, left: 0 })

  function show(event: MouseEvent) {
    const target = event.currentTarget as HTMLElement
    const rect = target.getBoundingClientRect()
    position.value = {
      top: rect.top + window.scrollY - 20,
      left: rect.left + window.scrollX + rect.width / 2,
    }
    visible.value = true
  }

  function hide() {
    visible.value = false
  }
</script>

<template>
  <div
    ref="tooltipRef"
    class="inline-flex items-center justify-center relative"
    @mouseenter="show"
    @mouseleave="hide">
    <slot />
    <Teleport to="body">
      <div
        v-if="visible"
        :style="{
          position: 'absolute',
          top: `${position.top}px`,
          left: `${position.left}px`,
          transform: 'translateX(-50%)',
        }"
        class="z-[9999] bg-black text-white text-xs px-2 py-1 rounded shadow-md pointer-events-none transition-opacity duration-200">
        {{ text }}
      </div>
    </Teleport>
  </div>
</template>
