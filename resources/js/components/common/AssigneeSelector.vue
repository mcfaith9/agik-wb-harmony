<script setup>
  import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
  import { Check } from "lucide-vue-next"

  const props = defineProps({
    users: {
      type: Array,
      required: true
    },
    modelValue: {
      type: Array,
      default: () => []
    }
  })

  const emit = defineEmits(['update:modelValue'])
  const dropdownOpen = ref(false)
  const dropdownRef = ref(null)

  const maxVisible = 3

  const visibleSelectedUsers = computed(() => {
    return props.modelValue.slice(0, maxVisible)
  })

  const hiddenCount = computed(() => {
    return props.modelValue.length > maxVisible
      ? props.modelValue.length - maxVisible
      : 0
  })

  function toggleDropdown() {
    dropdownOpen.value = !dropdownOpen.value
  }

  function isSelected(user) {
    return props.modelValue.some(u => u.id === user.id)
  }

  function toggleUser(user) {
    const selected = [...props.modelValue]
    const index = selected.findIndex(u => u.id === user.id)
    if (index !== -1) {
      selected.splice(index, 1)
    } else {
      selected.push(user)
    }
    emit('update:modelValue', selected)
  }

  function handleClickOutside(event) {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
      dropdownOpen.value = false
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
  <div ref="dropdownRef" class="relative inline-block text-left w-full">
    <button type="button" @click="toggleDropdown" class="w-full">
      <div class="flex -space-x-4 rtl:space-x-reverse">        
        <template v-for="user in visibleSelectedUsers" :key="user.id">
          <img
            class="w-9 h-9 border-2 border-white rounded-full dark:border-gray-800"
            :src="user.avatar"
            :alt="user.name"/>
        </template>
        <a
          class="flex items-center justify-center w-9 h-9 text-xs font-medium text-white bg-gray-700 border-2 border-white rounded-full hover:bg-gray-600 dark:border-gray-800"
          href="#">
          +{{ hiddenCount }}
        </a>
      </div>
    </button>
    <transition
      enter-active-class="transition duration-100 ease-out"
      enter-from-class="transform scale-95 opacity-0"
      enter-to-class="transform scale-100 opacity-100"
      leave-active-class="transition duration-75 ease-in"
      leave-from-class="transform scale-100 opacity-100"
      leave-to-class="transform scale-95 opacity-0">

      <div
        v-if="dropdownOpen"
        class="shadow-lg absolute z-[40] w-full top-full mt-1 bg-white rounded-lg dark:bg-gray-900 min-w-[200px] max-w-md">
        <ul
          class="overflow-y-auto max-h-[30vh] border border-gray-300 divide-y divide-gray-200 custom-scrollbar max-h-60 dark:divide-gray-800 dark:border-gray-700 rounded-lg"
          role="listbox"
          aria-multiselectable="true">
          <li
            v-for="user in users"
            :key="user.id"
            @click="toggleUser(user)"
            class="relative flex items-center text-sm w-full px-3 py-2 border-transparent cursor-pointer first:rounded-t-lg last:rounded-b-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-800"
            :class="{ 'bg-gray-50 dark:bg-white/[0.03]': isSelected(user) }"
            role="option"
            :aria-selected="isSelected(user)">
            <img :src="user.avatar" alt="avatar" class="w-6 h-6 rounded-full mr-3" />
            <span class="grow">{{ user.name }}</span>
            <Check 
              v-if="isSelected(user)"
              class="w-4 h-4 text-gray-400 dark:text-gray-300" />
          </li>
        </ul>
      </div>
    </transition>
  </div>
</template>
