<script setup>
  import { RouterLink } from 'vue-router'
  import { ref, onMounted, onUnmounted } from 'vue'
  import axios from 'axios'
  import { useRouter } from 'vue-router'
  import { userStore } from '@/stores/userStore'
  import {
    UserRoundPen,
    UserRoundCog,
    HeartHandshake,
    DoorOpen,
    ChevronDown
  } from 'lucide-vue-next'

  const router = useRouter()
  const dropdownOpen = ref(false)
  const dropdownRef = ref(null)

  const user = ref({
    name: '',
    email: '',
    avatar: ''
  })

  const menuItems = [
    { href: '/profile', icon: UserRoundPen, text: 'Edit profile' },
    { href: '/#', icon: UserRoundCog, text: 'Account settings' },
    { href: '/#', icon: HeartHandshake, text: 'Support' },
  ]

  const toggleDropdown = () => {
    dropdownOpen.value = !dropdownOpen.value
  }

  const closeDropdown = () => {
    dropdownOpen.value = false
  }  

  const signOut = async () => {
    try {
      await axios.post('/logout')
      closeDropdown()
      router.push('/signin')
    } catch (error) {
      console.error('Logout failed', error)
    }
  }

  const fetchUserInfo = async () => {
    try {
      const response = await axios.get('/login-user')
      const u = response.data.user

      user.value.name = `${u.first_name} ${u.last_name}`
      user.value.email = u.email
      user.value.avatar = '/images/user/owner.jpg'

      userStore.setUser(response.data.user)
    } catch (error) {
      console.error('Failed to fetch user', error)
    }
  }

  const handleClickOutside = (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
      closeDropdown()
    }
  }

  onMounted(() => {
    fetchUserInfo()
    document.addEventListener('click', handleClickOutside)
  })

  onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
  })
</script>

<template>
  <div class="relative" ref="dropdownRef">
    <button
      class="flex items-center text-gray-700 dark:text-gray-400"
      @click.prevent="toggleDropdown">
      <span class="mr-3 overflow-hidden rounded-full h-11 w-11">
        <img src="@/images/user/owner.jpg" alt="User" />
      </span>
      
      <span class="block mr-1 font-medium text-theme-sm">{{ user.name }}</span>
      <ChevronDown :class="{ 'rotate-180': dropdownOpen }" />
    </button>

    <!-- Dropdown Start -->
    <div
      v-if="dropdownOpen"
      class="absolute right-0 mt-[17px] flex w-[260px] flex-col rounded-2xl border border-gray-200 bg-white p-3 shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark">
      <div>
        <span class="block font-medium text-gray-700 text-theme-sm dark:text-gray-400">
          {{ user.name }}
        </span>
        <span class="mt-0.5 block text-theme-xs text-gray-500 dark:text-gray-400">
          {{ user.email }}
        </span>
      </div>

      <ul class="flex flex-col gap-1 pt-4 pb-3 border-b border-gray-200 dark:border-gray-800">
        <li v-for="item in menuItems" :key="item.href">
          <router-link
            :to="item.href"
            class="flex items-center gap-3 px-3 py-2 font-medium text-gray-700 rounded-lg group text-theme-sm hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
            <!-- SVG icon would go here -->
            <component
              :is="item.icon"
              class="w-5 h-5 text-gray-500 group-hover:text-gray-700 dark:group-hover:text-gray-300"
            />
            {{ item.text }}
          </router-link>
        </li>
      </ul>
      <router-link
        to="/signin"
        @click="signOut"
        class="flex items-center gap-3 px-3 py-2 mt-3 font-medium text-gray-700 rounded-lg group text-theme-sm hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
        <DoorOpen class="w-5 h-5 text-gray-500 group-hover:text-gray-700 dark:group-hover:text-gray-300" />
        Sign out
      </router-link>
    </div>
    <!-- Dropdown End -->
  </div>
</template>
