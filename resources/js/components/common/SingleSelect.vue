<script setup>
  import { 
    ChevronDown
  } from "lucide-vue-next"

  const props = defineProps({
    modelValue: [String, Number, null],
    options: {
      type: Array,
      default: () => []
    },
    placeholder: {
      type: String,
      default: 'Select Option'
    }
  })

  const emit = defineEmits(['update:modelValue'])

  const onChange = (event) => {
    emit('update:modelValue', event.target.value)
  }
</script>

<template>
  <select
    :value="modelValue"
    @change="onChange"
    class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">
    <option
      v-for="option in options"
      :key="option.value"
      :value="option.value"
      class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
      {{ option.label }}
    </option>
  </select>
  <span class="absolute z-30 text-gray-500 -translate-y-1/2 pointer-events-none right-4 top-1/2 dark:text-gray-400">
    <ChevronDown class="w-5 h-5" />
  </span>
</template>
