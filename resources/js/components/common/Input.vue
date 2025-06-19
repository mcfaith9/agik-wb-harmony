<script setup>
  import { computed } from 'vue'

  const props = defineProps({
    label: {
      type: String,
      default: '',
    },
    modelValue: {
      type: [String, Number],
      default: '',
    },
    placeholder: {
      type: String,
      default: '',
    },
    required: {
      type: Boolean,
      default: false,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    error: { 
      type: String, 
      default: '' 
    },
  })

  const emit = defineEmits(['update:modelValue'])

  const inputValue = computed({
    get: () => props.modelValue,
    set: (val) => emit('update:modelValue', val),
  })
</script>

<template>
  <div>
    <label
      v-if="label"
      class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
      {{ label }}
    </label>
    <input
      v-model="inputValue"
      type="text"
      :placeholder="placeholder"
      :required="required"
      :disabled="disabled"
      :class="[
        'h-11 w-full appearance-none rounded-lg px-4 py-2.5 text-sm shadow-theme-xs placeholder:text-gray-400 focus:outline-hidden focus:ring-3',
        'dark:bg-dark-900 dark:text-white/90 dark:placeholder:text-white/30',
        error
          ? 'border border-error-300 focus:border-error-300 focus:ring-error-500/10 dark:border-error-700 dark:focus:border-error-800'
          : 'border border-gray-300 focus:border-brand-300 focus:ring-brand-500/10 dark:border-gray-700 dark:focus:border-brand-800'
      ]"/>
      <p v-show="error" class="mt-1.5 text-theme-xs text-error-500"> 
        {{ error }}
      </p>
  </div>
</template>
