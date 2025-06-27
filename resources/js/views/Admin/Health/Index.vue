<script setup>
  import { ref } from 'vue'
  import AdminLayout from '@/components/layout/AdminLayout.vue'
  import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
  import Burnout from '@/views/Admin/Health/Burnout.vue'
  import StalledTasks from '@/views/Admin/Health/StalledTasks.vue'
  import ProductivityDrop from '@/views/Admin/Health/ProductivityDrop.vue'
  import OverAssignment from '@/views/Admin/Health/OverAssignment.vue'

  const currentPageTitle = ref('Health Dashboard')
  const currentTab = ref('burnout')

  const tabs = [
    { id: 'burnout', label: 'Burnout Risk' },
    { id: 'stalled', label: 'Stalled Tasks' },
    { id: 'productivity', label: 'Productivity Drop' },
    { id: 'overassignment', label: 'Over Assignment' },
  ]
</script>

<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />

    <!-- Tabs -->
    <div class="border-b border-gray-200 mb-4">
      <nav class="flex space-x-4" aria-label="Tabs">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          @click="currentTab = tab.id"
          :class="[
            currentTab === tab.id
              ? 'border-b-2 border-pink-500 text-pink-600'
              : 'text-gray-500 hover:text-gray-700',
            'whitespace-nowrap px-4 py-2 text-sm font-medium'
          ]">
          {{ tab.label }}
        </button>
      </nav>
    </div>

    <!-- Tab Content -->
    <div class="space-y-6">
      <section class="rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12" v-show="currentTab === 'burnout'">
        <Burnout />
      </section>

      <section class="rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12" v-show="currentTab === 'stalled'">
        <StalledTasks />
      </section>

      <section class="rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12" v-show="currentTab === 'productivity'">
        <ProductivityDrop />
      </section>

      <section class="rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12" v-show="currentTab === 'overassignment'">
        <OverAssignment />
      </section>
    </div>
  </AdminLayout>
</template>
