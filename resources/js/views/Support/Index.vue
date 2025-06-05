<template>
  <admin-layout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />

    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
      <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90 lg:mb-7">Profile</h3>
      <h1 class="text-3xl font-bold text-center">Support Center</h1>

      <!-- 1. Knowledge Base -->
      <section>
        <h2 class="text-2xl font-semibold mb-4">📘 Knowledge Base</h2>
        <ul class="space-y-2">
          <li v-for="article in knowledgeBase" :key="article.id">
            <a href="#" class="text-blue-600 hover:underline">{{ article.title }}</a>
          </li>
        </ul>
      </section>

      <!-- 2. Submit a Ticket -->
      <section>
        <h2 class="text-2xl font-semibold mb-4">🎫 Submit a Ticket</h2>
        <form @submit.prevent="submitTicket" class="space-y-4">
          <input v-model="ticket.subject" type="text" placeholder="Subject" class="w-full p-2 border rounded" required />
          <textarea v-model="ticket.description" placeholder="Describe your issue..." class="w-full p-2 border rounded" rows="4" required></textarea>
          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Send Ticket</button>
        </form>
      </section>

      <!-- 4. FAQs -->
      <section>
        <h2 class="text-2xl font-semibold mb-4">❓ Frequently Asked Questions</h2>
        <div v-for="faq in faqs" :key="faq.q" class="mb-3">
          <p class="font-medium">Q: {{ faq.q }}</p>
          <p class="text-gray-600">A: {{ faq.a }}</p>
        </div>
      </section>

      <!-- 5. Changelog -->
      <section>
        <h2 class="text-2xl font-semibold mb-4">📝 Changelog</h2>
        <ul class="space-y-2 text-gray-700">
          <li v-for="entry in changelog" :key="entry.date">
            <p><strong>{{ entry.date }}</strong>: {{ entry.change }}</p>
          </li>
        </ul>
      </section>
    </div>
  </admin-layout>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import AdminLayout from '../../components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'

const currentPageTitle = ref('User Profile')
const knowledgeBase = ref([
  { id: 1, title: 'How to create a project' },
  { id: 2, title: 'How to assign tasks to users' },
  { id: 3, title: 'Understanding project deadlines' }
])

const ticket = ref({
  subject: '',
  description: ''
})

function submitTicket() {
  console.log('Ticket Submitted:', ticket.value)
  alert('Support ticket submitted!')
  ticket.value.subject = ''
  ticket.value.description = ''
}

const faqs = ref([
  { q: 'How do I reset my password?', a: 'Go to Account Settings > Reset Password.' },
  { q: 'Can I export my project data?', a: 'Yes, use the Export button on the dashboard.' }
])

const changelog = ref([
  { date: '2025-06-01', change: 'Added new Gantt chart view.' },
  { date: '2025-05-25', change: 'Improved task filtering options.' },
  { date: '2025-05-10', change: 'Initial release of project dashboard.' }
])
</script>

<style scoped>
section {
  border-bottom: 1px solid #e5e7eb;
  padding-bottom: 1.5rem;
}
</style>
