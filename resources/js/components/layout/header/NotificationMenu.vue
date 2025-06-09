<script setup>
  import { ref, onMounted, onUnmounted } from 'vue'
  import { RouterLink } from 'vue-router'
  import {
    X, Bell
  } from 'lucide-vue-next'

  const dropdownOpen = ref(false)
  const notifying = ref(true)
  const dropdownRef = ref(null)

  const notifications = ref([
    {
      id: 1,
      userName: 'Mcfloat',
      userImage: new URL('@/images/user/default.jpg', import.meta.url).href,
      action: 'requests permission to change',
      project: 'Project - Harmony',
      type: 'Project',
      time: '5 min ago',
      status: 'online',
    },
    {
      id: 2,
      userName: 'Mcfloat',
      userImage: new URL('@/images/user/default.jpg', import.meta.url).href,
      action: 'requests permission to change',
      project: 'Project - Harmony',
      type: 'Project',
      time: '5 min ago',
      status: 'offline',
    },
    {
      id: 3,
      userName: 'Mcfloat',
      userImage: new URL('@/images/user/default.jpg', import.meta.url).href,
      action: 'requests permission to change',
      project: 'Project - Harmony',
      type: 'Project',
      time: '5 min ago',
      status: 'online',
    },
    {
      id: 4,
      userName: 'Mcfloat',
      userImage: new URL('@/images/user/default.jpg', import.meta.url).href,
      action: 'requests permission to change',
      project: 'Project - Harmony',
      type: 'Project',
      time: '5 min ago',
      status: 'online',
    },
    {
      id: 5,
      userName: 'Mcfloat',
      userImage: new URL('@/images/user/default.jpg', import.meta.url).href,
      action: 'requests permission to change',
      project: 'Project - Harmony',
      type: 'Project',
      time: '5 min ago',
      status: 'offline',
    },
    {
      id: 6,
      userName: 'Mcfloat',
      userImage: new URL('@/images/user/default.jpg', import.meta.url).href,
      action: 'requests permission to change',
      project: 'Project - Harmony',
      type: 'Project',
      time: '5 min ago',
      status: 'online',
    },
    {
      id: 7,
      userName: 'Mcfloat',
      userImage: new URL('@/images/user/default.jpg', import.meta.url).href,
      action: 'requests permission to change',
      project: 'Project - Harmony',
      type: 'Project',
      time: '5 min ago',
      status: 'online',
    },
    {
      id: 7,
      userName: 'Mcfloat',
      userImage: new URL('@/images/user/default.jpg', import.meta.url).href,
      action: 'requests permission to change',
      project: 'Project - Harmony',
      type: 'Project',
      time: '5 min ago',
      status: 'online',
    },
    // Add more notifications here...
  ])

  const toggleDropdown = () => {
    dropdownOpen.value = !dropdownOpen.value
    notifying.value = false
  }

  const closeDropdown = () => {
    dropdownOpen.value = false
  }

  const handleClickOutside = (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
      closeDropdown()
    }
  }

  const handleItemClick = (event) => {
    event.preventDefault()
    // Handle the item click action here
    console.log('Notification item clicked')
    closeDropdown()
  }

  const handleViewAllClick = (event) => {
    event.preventDefault()
    // Handle the "View All Notification" action here
    console.log('View All Notifications clicked')
    closeDropdown()
  }

  onMounted(() => {
    document.addEventListener('click', handleClickOutside)
  })

  onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
  })
</script>

<template>
  <div class="relative" ref="dropdownRef">
    <button
      class="relative flex items-center justify-center text-gray-500 transition-colors bg-white border border-gray-200 rounded-full hover:text-dark-900 h-8 w-8 hover:bg-gray-100 hover:text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
      @click="toggleDropdown">
      <span
        :class="{ hidden: !notifying, flex: notifying }"
        class="absolute right-0 top-0.5 z-1 h-2 w-2 rounded-full bg-orange-400">
        <span
          class="absolute inline-flex w-full h-full bg-orange-400 rounded-full opacity-75 -z-1 animate-ping select-none"></span>
      </span>
      <Bell class="w-4 h-4" />
    </button>

    <div
      v-if="dropdownOpen"
      class="absolute -right-[240px] mt-[17px] flex h-[480px] w-[350px] flex-col rounded-2xl border border-gray-200 bg-white p-3 shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark sm:w-[361px] lg:right-0">
      <div class="flex items-center justify-between pb-3 mb-3 border-b border-gray-100 dark:border-gray-800">
        <h5 class="text-md font-semibold text-gray-800 dark:text-white/90">Notification</h5>

        <button @click="closeDropdown" class="text-gray-500 dark:text-gray-400">
          <X class="w-4 h-4" />
        </button>
      </div>

      <ul class="flex flex-col h-auto overflow-y-auto custom-scrollbar">
        <li v-for="notification in notifications" :key="notification.id" @click="handleItemClick">
          <a
            class="flex gap-3 rounded-lg border-b border-gray-100 p-3 px-4.5 py-3 hover:bg-gray-100 dark:border-gray-800 dark:hover:bg-white/5"
            href="#">
            <span class="relative block w-full h-8 rounded-full z-1 max-w-8">
              <img :src="notification.userImage" alt="User" class="overflow-hidden rounded-full" />
              <span
                :class="notification.status === 'online' ? 'bg-success-500' : 'bg-error-500'"
                class="absolute bottom-0 right-0 z-10 h-2.5 w-full max-w-2.5 rounded-full border-[1.5px] border-white dark:border-gray-900"></span>
            </span>

            <span class="block">
              <span class="mb-1.5 block text-theme-xs text-gray-500 dark:text-gray-400">
                <span class="font-medium text-gray-800 dark:text-white/90">
                  {{ notification.userName }}
                </span>
                {{ notification.action }}
                <span class="font-medium text-gray-800 dark:text-white/90">
                  {{ notification.project }}
                </span>
              </span>

              <span class="flex items-center gap-2 text-gray-500 text-theme-xs dark:text-gray-400">
                <span>{{ notification.type }}</span>
                <span class="w-1 h-1 bg-gray-400 rounded-full"></span>
                <span>{{ notification.time }}</span>
              </span>
            </span>
          </a>
        </li>
      </ul>

      <router-link
        to="#"
        class="mt-3 flex justify-center rounded-full border border-gray-300 bg-white p-2 text-theme-xs font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200"
        @click="handleViewAllClick">
        View All Notification
      </router-link>
    </div>
    <!-- Dropdown End -->
  </div>
</template>
