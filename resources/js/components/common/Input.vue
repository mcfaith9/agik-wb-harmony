<script setup>
  import { computed } from 'vue'

  const props = defineProps({
    label: String,
    modelValue: [String, Number],
    placeholder: String,
    required: Boolean,
    disabled: Boolean,
    error: String,
    type: {
      type: String,
      default: 'text',
    },
  })

  const emit = defineEmits(['update:modelValue'])

  const inputValue = computed({
    get: () => props.modelValue,
    set: (val) => {
      // Automatically convert to number if type is number
      if (props.type === 'number') {
        const numeric = parseFloat(val)
        emit('update:modelValue', isNaN(numeric) ? null : numeric)
      } else {
        emit('update:modelValue', val)
      }
    },
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
      :type="type"
      :placeholder="placeholder"
      :required="required"
      :disabled="disabled"
      :min="type === 'number' ? 0 : undefined"
      @keypress="type === 'number' && !/[0-9.]$/.test($event.key) && $event.preventDefault()"
      :class="[
        'h-11 w-full appearance-none rounded-lg px-4 py-2.5 text-sm shadow-theme-xs placeholder:text-gray-400 focus:outline-hidden focus:ring-3',
        'dark:bg-dark-900 dark:text-white/90 dark:placeholder:text-white/30',
        error
          ? 'border border-error-300 focus:border-error-300 focus:ring-error-500/10 dark:border-error-700 dark:focus:border-error-800'
          : 'border border-gray-300 focus:border-brand-300 focus:ring-brand-500/10 dark:border-gray-700 dark:focus:border-brand-800'
      ]" />
    
    <p v-show="error" class="mt-1.5 text-theme-xs text-error-500">
      {{ error }}
    </p>
  </div>
</template>
