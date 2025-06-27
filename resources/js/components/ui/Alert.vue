<script setup lang="ts">
  import { CircleCheck, CircleX, CircleAlert, Info } from "lucide-vue-next"
  import { computed, onMounted, ref } from "vue"

  interface AlertProps {
    variant: 'success' | 'error' | 'warning' | 'info'
    title: string
    message: string
    showLink?: boolean
    linkHref?: string
    linkText?: string
    position?: 'top-right' | 'top-left' | 'bottom-right' | 'bottom-left'
    duration?: number // auto-dismiss duration in ms
  }

  const props = withDefaults(defineProps<AlertProps>(), {
    showLink: false,
    linkHref: '#',
    linkText: 'Learn more',
    position: 'top-right',
    duration: 3000,
  })

  const isVisible = ref(true)

  onMounted(() => {
    if (props.duration > 0) {
      setTimeout(() => {
        isVisible.value = false
      }, props.duration)
    }
  })

  const positionClasses = computed(() => {
    switch (props.position) {
      case 'top-left': return 'top-4 left-4'
      case 'top-right': return 'top-4 right-4'
      case 'bottom-left': return 'bottom-4 left-4'
      case 'bottom-right': return 'bottom-4 right-4'
      default: return 'top-4 right-4'
    }
  })

  const variantClasses = {
    success: {
      container: 'border-green-500 bg-green-50 dark:border-green-500/30 dark:bg-green-500/15',
      icon: 'text-green-500',
    },
    error: {
      container: 'border-red-500 bg-red-50 dark:border-red-500/30 dark:bg-red-500/15',
      icon: 'text-red-500',
    },
    warning: {
      container: 'border-yellow-500 bg-yellow-50 dark:border-yellow-500/30 dark:bg-yellow-500/15',
      icon: 'text-yellow-500',
    },
    info: {
      container: 'border-blue-500 bg-blue-50 dark:border-blue-500/30 dark:bg-blue-500/15',
      icon: 'text-blue-500',
    },
  }

  const icons = {
    success: CircleCheck,
    error: CircleX,
    warning: CircleAlert,
    info: Info,
  }
</script>

<template>
  <transition name="fade-slide">
    <div
      v-if="isVisible"
      :class="[
        'fixed mt-14 z-[9999999] w-[500px] max-w-full rounded-xl border p-4 shadow-lg transition-all duration-300',
        variantClasses[variant].container,
        positionClasses
      ]">
      <div class="flex items-start gap-3">
        <div :class="['-mt-0.5', variantClasses[variant].icon]">
          <component :is="icons[variant]" />
        </div>

        <div class="flex-1">
          <h4 class="mb-1 text-sm font-semibold text-gray-800 dark:text-white/90">
            {{ title }}
          </h4>

          <p class="text-sm text-gray-500 dark:text-white/90">{{ message }}</p>

          <router-link
            v-if="showLink"
            :to="linkHref"
            class="inline-block mt-3 text-sm font-medium text-gray-500 underline dark:text-gray-400">
            {{ linkText }}
          </router-link>
        </div>
      </div>
    </div>
  </transition>
</template>

<style scoped>
  .fade-slide-enter-active,
  .fade-slide-leave-active {
    transition: all 0.4s ease;
  }
  .fade-slide-enter-from {
    opacity: 0;
    transform: translateY(-10px);
  }
  .fade-slide-leave-to {
    opacity: 0;
    transform: translateY(-10px);
  }
</style>
