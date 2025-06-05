<script setup lang="ts">
  import { ref, watch, onMounted, onBeforeUnmount } from 'vue'
  import { 
    X,
    Flag
  } from "lucide-vue-next"

  const props = defineProps<{
    modelValue: boolean,
    title?: string,
    priority?: string
  }>()

  const emit = defineEmits(['update:modelValue'])

  const closeDrawer = () => emit('update:modelValue', false)

  // ESC key support
  const onKeydown = (e: KeyboardEvent) => {
    if (e.key === 'Escape') closeDrawer()
  }

  onMounted(() => {
    window.addEventListener('keydown', onKeydown)
  })

  onBeforeUnmount(() => {
    window.removeEventListener('keydown', onKeydown)
    document.body.style.overflow = '' // Clean up just in case
  })

  // Handle body scroll locking
  watch(() => props.modelValue, (newVal) => {
    if (newVal) {
      document.body.style.overflow = 'hidden'
    } else {
      document.body.style.overflow = ''
    }
  })
</script>

<template>
  <!-- Overlay -->
  <transition name="fade">
    <div
      v-if="modelValue"
      class="cursor-default fixed inset-0 z-[99999] h-full w-full bg-gray-400/50 backdrop-blur-[2px]"
      @click="closeDrawer"
    />
  </transition>

  <!-- Drawer -->
  <transition name="slide">
    <aside
      v-if="modelValue"
      class="cursor-default fixed right-0 top-0 z-[99999] h-full w-full max-w-lg bg-white shadow-lg dark:bg-gray-900 flex flex-col rounded-l-3xl">
      <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="flex text-lg font-semibold text-gray-800 dark:text-white">
          <Flag 
            v-if="priority"
            :class="{
              'w-4 h-4 shrink-0 mr-1': true,
              'text-gray-300': priority === 'none',
              'text-red-500': priority === 'high',
              'text-yellow-500': priority === 'medium',
              'text-blue-500' : priority === 'low'
            }" />
          <span>{{ title }}</span>
        </h2>
        <button
          @click="closeDrawer"
          class=" z-999 flex h-4 w-4 items-center justify-center text-gray-400 dark:text-gray-400">
          <X class="w-5 h-5" />
        </button>
      </div>
      <div class="flex-1 overflow-y-auto p-6 space-y-4 custom-scrollbar">
        <slot name="body" />
      </div>

      <div class="border-t border-gray-200 dark:border-gray-700 px-6 py-4">
        <slot name="footer" />
      </div>
    </aside>
  </transition>
</template>

<style scoped>
  .fade-enter-active, .fade-leave-active {
    transition: opacity 0.2s ease;
  }
  .fade-enter-from, .fade-leave-to {
    opacity: 0;
  }
  .slide-enter-active, .slide-leave-active {
    transition: transform 0.3s ease;
  }
  .slide-enter-from, .slide-leave-to {
    transform: translateX(100%);
  }
</style>
