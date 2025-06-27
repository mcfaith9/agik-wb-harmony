<script setup lang="ts">
  import ThemeProvider from "./components/layout/ThemeProvider.vue"
  import SidebarProvider from "./components/layout/SidebarProvider.vue"
  import Alert from "@/components/ui/Alert.vue"
  import { alert } from "@/composables/globalAlert"
</script>

<template>
  <ThemeProvider>
    <SidebarProvider>
      <Alert
        v-if="alert.visible"
        :title="alert.title"
        :message="alert.message"
        :variant="alert.variant"
        :show-link="alert.showLink"
        :link-href="alert.linkHref"
        :link-text="alert.linkText"
        :duration="alert.duration" />
      <RouterView />
    </SidebarProvider>
  </ThemeProvider>
</template>
