<script setup>
import { ref } from 'vue'
import { ChevronDown } from 'lucide-vue-next'

const props = defineProps({
  modelValue: [String, Number, null],
  options: {
    type: Array,
    default: () => []
  },
  placeholder: {
    type: String,
    default: 'Select Option'
  },
  required: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:modelValue'])

const isOpen = ref(false)

const toggleDropdown = () => {
  isOpen.value = !isOpen.value
}

const selectOption = (option) => {
  emit('update:modelValue', option.value)
  isOpen.value = false
}

const getSelectedLabel = () => {
  const found = props.options.find(o => o.value === props.modelValue)
  return found ? found.label : props.placeholder
}
</script>

<template>
  <div class="relative w-full">
    <input
      class="sr-only"
      :value="modelValue"
      :required="required"
      tabindex="-1"
      autocomplete="off"
    />

    <button
      @click="toggleDropdown"
      type="button"
      class="dark:bg-dark-900 flex h-11 w-full items-center justify-between rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-none focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">
      <span>{{ getSelectedLabel() }}</span>
      <ChevronDown class="w-5 h-5 text-gray-500 dark:text-gray-400" />
    </button>

    <ul
      v-if="isOpen"
      class="absolute z-30 max-h-[20vh] w-full custom-scrollbar mt-2 max-h-60 w-full overflow-y-auto rounded-lg border border-gray-300 bg-white py-2 shadow-lg dark:border-gray-700 dark:bg-gray-900">
      <li
        v-for="option in options"
        :key="option.value"
        @click="selectOption(option)"
        class="cursor-pointer px-4 py-2 text-sm text-gray-800 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700">
        {{ option.label }}
      </li>
    </ul>
  </div>
</template>

