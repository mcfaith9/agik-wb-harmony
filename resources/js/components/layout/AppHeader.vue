<template>
  <header
    class="sticky top-0 flex w-full bg-white border-gray-200 z-99999 dark:border-gray-800 dark:bg-gray-900 lg:border-b"
  >
    <div class="flex flex-col items-center justify-between grow lg:flex-row lg:px-6">
      <div class="flex items-center justify-between w-full gap-2 px-3 py-3 border-b border-gray-200 dark:border-gray-800 sm:gap-4 lg:justify-normal lg:border-b-0 lg:px-0 lg:py-4 select-none">
        <button
          @click="handleToggle"
          class="flex items-center justify-center w-8 h-8 text-gray-500 border-gray-200 rounded-lg z-99999 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 lg:h-8 lg:w-8 lg:border dark:border-gray-800"
          :class="[
            isMobileOpen
              ? 'lg:bg-transparent dark:lg:bg-transparent bg-gray-100 dark:bg-gray-800'
              : '',
          ]">
          <PanelLeft class="w-5 h-5" />
        </button>

        <HeaderLogo />

        <button
          @click="toggleApplicationMenu"
          class="flex items-center justify-center w-8 h-8 text-gray-500 rounded-lg z-99999 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 lg:hidden">
          <Ellipsis class="w-6 h-6" />
        </button>
        <SearchBar />
      </div>

      <div
        :class="[isApplicationMenuOpen ? 'flex' : 'hidden']"
        class="items-center justify-between w-full gap-4 px-5 py-4 shadow-theme-md lg:flex lg:justify-end lg:px-0 lg:shadow-none"
      >
        <div class="flex items-center gap-2 2xsm:gap-3">
          <ThemeToggler />
          <NotificationMenu />
        </div>
        <UserMenu />
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useSidebar } from '@/composables/useSidebar'
import ThemeToggler from '../common/ThemeToggler.vue'
import SearchBar from './header/SearchBar.vue'
import HeaderLogo from './header/HeaderLogo.vue'
import NotificationMenu from './header/NotificationMenu.vue'
import UserMenu from './header/UserMenu.vue'
import { 
  PanelLeft,
  Ellipsis
} from "lucide-vue-next"

const { toggleSidebar, toggleMobileSidebar, isMobileOpen } = useSidebar()

const handleToggle = () => {
  if (window.innerWidth >= 1024) {
    toggleSidebar()
  } else {
    toggleMobileSidebar()
  }
}

const dropdownOpen = ref(false)
const notifying = ref(false)

const toggleDropdown = () => {
  dropdownOpen.value = !dropdownOpen.value
  notifying.value = false
}

const isApplicationMenuOpen = ref(false)

const toggleApplicationMenu = () => {
  isApplicationMenuOpen.value = !isApplicationMenuOpen.value
}
</script>
