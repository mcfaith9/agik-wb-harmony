<template>
  <div class="relative" v-click-outside="closeDropdown" ref="dropdown">
    <button @click="toggleDropdown" class="w-3 h-3 text-gray-500 dark:text-gray-400">
      <Ellipsis width="20" height="20" />
    </button>

    <div v-if="open" :class="menuClass">
      <button
        @click="handleEdit"
        :class="itemClass"
        class="flex items-center gap-2">
        <Pencil class="w-4 h-4 text-gray-500" />
        Edit
      </button>
      <button
        @click="handleDelete"
        :class="itemClass"
        class="flex items-center gap-2">
        <Trash class="w-4 h-4 text-red-500" />
        Delete
      </button>
    </div>
  </div>
</template>

<script setup>
  import { ref } from 'vue'
  import vClickOutside from '@/components/common/v-click-outside.vue'
  import { Ellipsis, Pencil, Trash } from 'lucide-vue-next'

  const props = defineProps({
    row: Object,
    menuClass: {
      type: String,
      default:
        'absolute right-0 z-40 w-36 p-2 space-y-1 bg-white border border-gray-200 top-full rounded-2xl shadow-lg dark:border-gray-800 dark:bg-gray-dark',
    },
    itemClass: {
      type: String,
      default:
        'w-full px-3 py-2 font-medium text-left text-gray-500 rounded-lg text-theme-xs hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300',
    },
  })

  const emit = defineEmits(['edit', 'delete'])

  const open = ref(false)

  const toggleDropdown = () => {
    open.value = !open.value
  }

  const closeDropdown = () => {
    open.value = false
  }

  const handleEdit = () => {
    emit('edit', props.row)
    closeDropdown()
  }

  const handleDelete = () => {
    emit('delete', props.row)
    closeDropdown()
  }
</script>

<script>
  export default {
    directives: {
      clickOutside: vClickOutside,
    },
  }
</script>
