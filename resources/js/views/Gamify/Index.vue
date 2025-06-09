<script setup lang="ts">
  import { ref } from "vue";
  import AdminLayout from "@/components/layout/AdminLayout.vue";
  import PageBreadcrumb from "@/components/common/PageBreadcrumb.vue";
  import ObjectivesAndPet from '@/views/Gamify/ObjectivesAndPet.vue'
  import MyBadge from '@/views/Gamify/MyBadge.vue'
  import LeaderBoard from '@/views/Gamify/LeaderBoard.vue'

  const currentPageTitle = ref("Gamify");
</script>

<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" class="font-pixelfy" />
    <div class="grid grid-cols-12 gap-4 md:gap-6">
      <div class="col-span-12 space-y-6 xl:col-span-7">
        <ObjectivesAndPet />
        <MyBadge />
      </div>
      <div class="col-span-12 xl:col-span-5">
        <LeaderBoard />
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
  @import url('https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400..700&display=swap');

  .font-pixelfy {
    font-family: Pixelify Sans;
  }
</style>
