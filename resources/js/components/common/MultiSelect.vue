<script setup>
  import { ref, watch, onMounted, onBeforeUnmount } from 'vue'
  import { 
    X,
    ChevronDown,
    Check
  } from "lucide-vue-next"

  const props = defineProps({
    options: {
      type: Array,
      required: true,
    },
    modelValue: {
      type: Array,
      default: () => [],
    },
    type: {
      type: String
    }
  })

  const emit = defineEmits(['update:modelValue'])

  const isOpen = ref(false)
  const selectedItems = ref([...props.modelValue])
  const multiSelectRef = ref(null)

  const toggleDropdown = () => {
    isOpen.value = !isOpen.value
  }

  const toggleItem = (item) => {
    const index = selectedItems.value.findIndex(s => s.id === item.id)
    if (index === -1) {
      selectedItems.value.push(item)
    } else {
      selectedItems.value.splice(index, 1)
    }
    emit('update:modelValue', [...selectedItems.value])
  }

  const removeItem = (item) => {
    const index = selectedItems.value.findIndex(s => s.id === item.id)
    if (index !== -1) {
      selectedItems.value.splice(index, 1)
      emit('update:modelValue', [...selectedItems.value])
    }
  }

  const isSelected = (item) => {
    return selectedItems.value.some(s => s.id === item.id)
  }

  // Watch for external changes to modelValue and update selectedItems accordingly
  watch(() => props.modelValue, (newVal) => {
    selectedItems.value = [...newVal]
  })

  const handleClickOutside = (event) => {
    if (multiSelectRef.value && !multiSelectRef.value.contains(event.target)) {
      isOpen.value = false
    }
  }

  onMounted(() => {
    document.addEventListener('click', handleClickOutside)
  })

  onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside)
  })
</script>

<template>
  <div class="relative" ref="multiSelectRef">
    <div
      @click="toggleDropdown"
      class="dark:bg-dark-900 min-h-[44px] flex flex-wrap items-start w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
      :class="{ 'text-gray-800 dark:text-white/90': isOpen }">
      <span v-if="selectedItems.length === 0" class="text-gray-400"> Select items... </span>
      <div class="flex flex-wrap items-center flex-auto gap-2">
        <div
          v-for="item in selectedItems"
          :key="item.value"
          :class="[
            'px-2 py-0.5 text-xs font-medium rounded-full inline-flex',
            item.color == null ? 'bg-brand-50 text-brand-500 dark:bg-brand-500/15 dark:text-brand-400' : 'text-white'
          ]"
          :style="item.color ? { backgroundColor: item.color } : null">
          <span class="text-xs">{{ item.label }}</span>
          <button
            type="button"
            @click.stop="removeItem(item)"
            class="pl-2 text-gray-500 cursor-pointer group-hover:text-gray-400 dark:text-gray-400"
            aria-label="Remove item">
            <X class="w-3 h-3 text-white" />
          </button>
        </div>
      </div>
      <span class="absolute z-30 text-gray-500 -translate-y-1/2 pointer-events-none right-4 top-1/2 dark:text-gray-400">
        <ChevronDown class="w-5 h-5" />
      </span>
    </div>
    <transition
      enter-active-class="transition duration-100 ease-out"
      enter-from-class="transform scale-95 opacity-0"
      enter-to-class="transform scale-100 opacity-100"
      leave-active-class="transition duration-75 ease-in"
      leave-from-class="transform scale-100 opacity-100"
      leave-to-class="transform scale-95 opacity-0">
      <div
        v-if="isOpen"
        class="shadow-lg absolute z-10 w-full top-full mt-1 bg-white rounded-lg dark:bg-gray-900">
        <ul
          class="overflow-y-auto max-h-[20vh] border border-gray-300 divide-y divide-gray-200 custom-scrollbar max-h-60 dark:divide-gray-800 dark:border-gray-700 rounded-lg"
          role="listbox"
          aria-multiselectable="true">
          <li
            v-for="item in props.options"
            :key="item.value"
            @click="toggleItem(item)"
            class="relative flex items-center text-sm w-full px-3 py-2 border-transparent cursor-pointer first:rounded-t-lg last:rounded-b-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-800"
            :class="{ 'bg-gray-50 dark:bg-white/[0.03]': isSelected(item) }"
            role="option"
            :aria-selected="isSelected(item)">
            <span class="grow">{{ item.label }}</span>
            <Check 
              v-if="isSelected(item)"
              class="w-4 h-4 text-gray-400 dark:text-gray-300" />
          </li>
        </ul>
      </div>
    </transition>
  </div>
</template>