<script setup lang="ts">
  import { ref } from "vue";
  import AdminLayout from "@/components/layout/AdminLayout.vue";
  import PageBreadcrumb from "@/components/common/PageBreadcrumb.vue";
  import EcommerceMetrics from '@/components/ecommerce/EcommerceMetrics.vue'
  import MonthlySale from '@/components/ecommerce/MonthlySale.vue'
  import LeaderBoard from '@/views/Gamify/LeaderBoard.vue'

  const currentPageTitle = ref("Gamify");
</script>

<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <div class="grid grid-cols-12 gap-4 md:gap-6">
      <div class="col-span-12 space-y-6 xl:col-span-7">
        <EcommerceMetrics />
        <MonthlySale />
      </div>
      <div class="col-span-12 xl:col-span-5">
        <LeaderBoard />
      </div>
    </div>
  </AdminLayout>
</template>
