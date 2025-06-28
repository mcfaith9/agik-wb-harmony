<template>
  <div class="relative" v-click-outside="closeDropdown" ref="dropdown">
    <button @click="toggleDropdown" class="w-3 h-3 text-gray-500 dark:text-gray-400">
      <Ellipsis width="20" height="20"/>
    </button>

    <div v-if="open" :class="menuClass">
      <div v-if="roles && roles.length">
        <button
          v-for="role in roles"
          :key="role.id"
          @click="selectRole(role.id)"
          :class="[itemClass, 'flex items-center']">
          <CircleCheck
            v-if="isAssigned(role)"
            class="w-4 h-4 text-green-500 shrink-0 mr-1"/>
          <Circle
            v-else
            class="w-4 h-4 text-gray-100 shrink-0 mr-1 opacity-25"/>
          <span>{{ role.name }}</span>          
        </button>
      </div>
      <p v-else class="px-3 py-2 text-gray-500 text-theme-xs">No roles available</p>
    </div>
  </div>
</template>

<script setup>
  import { ref } from 'vue'
  import vClickOutside from '@/components/common/v-click-outside.vue'
  import {
    Ellipsis,
    CircleCheck,
    Circle
  } from "lucide-vue-next"

  const props = defineProps({
    roles: {
      type: Array,
      default: () => [],
    },
    assignedRoles: {
      type: Array,
      default: () => [],
    },
    menuClass: {
      type: String,
      default:
        'absolute right-0 z-40 w-40 p-2 space-y-1 bg-white border border-gray-200 top-full rounded-2xl shadow-lg dark:border-gray-800 dark:bg-gray-dark',
    },
    itemClass: {
      type: String,
      default:
        'flex w-full px-3 py-2 font-medium text-left text-gray-500 rounded-lg text-theme-xs hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300',
    },
  })

  const emit = defineEmits(['assignRole'])

  const open = ref(false)

  const toggleDropdown = () => {
    open.value = !open.value
  }

  const closeDropdown = () => {
    open.value = false
  }

  const isAssigned = (role) => {
    const assigned = props.assignedRoles.some(r => r.id === role.id)
    return assigned
  }

  const selectRole = (roleId) => {
    emit('assignRole', roleId)
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
