<script setup lang="ts">
  import { ref, onMounted } from "vue"
  import axios from "axios"
  import AdminLayout from "@/components/layout/AdminLayout.vue"
  import PageBreadcrumb from "@/components/common/PageBreadcrumb.vue"
  import Input from "@/components/common/Input.vue"
  import { 
    Circle
  } from "lucide-vue-next"

  const currentPageTitle = ref("Admin Settings")

  const settings = ref<Record<string, any>>({})
  const saving = ref<boolean>(false)
  const message = ref<string>("")
  const messageType = ref<'success' | 'error' | ''>('')

  const fetchSettings = async () => {
    try {
      const res = await axios.get("/api/admin/settings")
      settings.value = res.data
    } catch (err) {
      console.error("Failed to fetch settings", err)
    } finally {
      // 
    }
  }

  const saveSetting = async (key: string) => {
    saving.value = true
    try {
      const setting = settings.value[key]
      await axios.put(`/api/admin/settings/${key}`, {
        value: setting.value,
        label: setting.label,
        type: setting.type
      })
      messageType.value = 'success'
      message.value = `Setting "${setting.label}" saved successfully.`
    } catch (err) {
      console.error(`Failed to save setting "${key}"`, err)
      messageType.value = 'error'
      message.value = `Failed to save setting "${key}".`
    } finally {
      saving.value = false
      setTimeout(() => {
        message.value = ""
        messageType.value = ""
      }, 3000)
    }
  }

  onMounted(fetchSettings)
</script>

<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <div class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
      <div
        v-if="message"
        :class="[
          'mt-6 text-sm text-center',
          messageType === 'success' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'
        ]">
        {{ message }}
      </div>

      <!-- General Settings -->
      <div>
        <div class="mb-6">
          <h2 class="text-xl font-semibold text-gray-800 dark:text-white">General Settings</h2>
          <p class="text-xs text-gray-500 dark:text-gray-400">Basic configuration for your workspace or app.</p>
        </div>

        <div class="space-y-6">
          <div class="flex justify-between items-center ">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">App Name</label>
            <Input type="text" placeholder="" />
          </div>

          <div class="flex justify-between items-center ">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Default Language</label>
            <Input type="text" placeholder="e.g. English" />
          </div>
          <div class="flex justify-between items-center ">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Date Format</label>
            <Input type="text" placeholder="e.g. MM/DD/YYYY" />
          </div>
          <div class="flex justify-between items-center ">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Timezone</label>
            <Input type="text" placeholder="e.g. UTC+8" />
          </div>
          <div class="flex justify-between items-center ">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Right-to-left Layout</label>
            <Circle class="w-4 h-4 text-gray-500 cursor-pointer dark:text-gray-400" />
          </div>
        </div>

        <div class="mt-8 border-t border-dashed border-gray-300 dark:border-gray-700 my-3"></div>
      </div>

      <!-- Budget Settings -->
      <div>
        <div class="mb-6">
          <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Budget Settings</h2>
          <p class="text-xs text-gray-500 dark:text-gray-400">Define global budget constraints for your entire workspace.</p>
        </div>

        <div class="space-y-6">
          <div v-if="settings['global_total_budget']" class="flex justify-between items-center ">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ settings['global_total_budget'].label }}</label>
            <div class="flex gap-2">
              <Input 
                v-model="settings['global_total_budget'].value" 
                type="number" 
                placeholder="Enter total allowed budget" 
                required />
              <button type="button" @click="saveSetting('global_total_budget')" class="w-full items-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">
                {{ saving ? "Saving..." : "Save" }}
              </button>
            </div>
          </div>

          <div class="flex justify-between items-center ">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Currency Format</label>
            <Input type="text" placeholder="e.g. USD" />
          </div>
          <div class="flex justify-between items-center ">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Notify when X% of budget is spent</label>
            <Input type="number" placeholder="e.g. 90" />
          </div>
          <div class="flex justify-between items-center ">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Restrict task creation when budget is exhausted</label>
            <Circle class="w-4 h-4 text-gray-500 cursor-pointer dark:text-gray-400" />
          </div>
          <div class="flex justify-between items-center ">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Enable per-user expense tracking</label>
            <Circle class="w-4 h-4 text-gray-500 cursor-pointer dark:text-gray-400" />
          </div>
        </div>

        <div class="mt-8 border-t border-dashed border-gray-300 dark:border-gray-700 my-3"></div>
      </div>

      <!-- Notifications -->
      <div>
        <div class="mb-6">
          <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Notifications</h2>
          <p class="text-xs text-gray-500 dark:text-gray-400">Control how and when you receive alerts.</p>
        </div>

        <div class="space-y-6">
          <div class="flex justify-between items-center ">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Email Alerts</label>
            <Circle class="w-4 h-4 text-gray-500 cursor-pointer dark:text-gray-400" />
          </div>

          <div class="flex justify-between items-center ">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Push Notifications</label>
            <Circle class="w-4 h-4 text-gray-500 cursor-pointer dark:text-gray-400" />
          </div>

          <div class="flex justify-between items-center ">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Weekly Summary Emails</label>
            <Circle class="w-4 h-4 text-gray-500 cursor-pointer dark:text-gray-400" />
          </div>

          <div class="flex justify-between items-center ">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Notify on Budget Threshold Exceeded</label>
            <Circle class="w-4 h-4 text-gray-500 cursor-pointer dark:text-gray-400" />
          </div>

          <div class="flex justify-between items-center ">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Notify on Task Overdue</label>
            <Circle class="w-4 h-4 text-gray-500 cursor-pointer dark:text-gray-400" />
          </div>

          <div class="flex justify-between items-center ">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Notify on Project Completion</label>
            <Circle class="w-4 h-4 text-gray-500 cursor-pointer dark:text-gray-400" />
          </div>

          <div class="flex justify-between items-center ">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Notification Channels (Email, SMS, In-App, Slack)</label>
            <Input type="text" placeholder="Comma-separated list" />
          </div>
        </div>

        <div class="mt-8 border-t border-dashed border-gray-300 dark:border-gray-700 my-3"></div>
      </div>

      <!-- Security Settings -->
      <div>
        <div class="mb-6">
          <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Security Settings</h2>
          <p class="text-xs text-gray-500 dark:text-gray-400">Authentication and access policies.</p>
        </div>

        <div class="space-y-6">
          <div class="flex justify-between items-center ">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Enable 2FA</label>
            <Circle class="w-4 h-4 text-gray-500 cursor-pointer dark:text-gray-400" />
          </div>

          <div class="flex justify-between items-center ">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Idle Logout Timer (mins)</label>
            <Input type="number" placeholder="e.g. 15" />
          </div>

          <div class="flex justify-between items-center ">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">IP Whitelisting</label>
            <Input type="text" placeholder="Comma-separated IPs" />
          </div>

          <div class="flex justify-between items-center ">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Password Rotation Policy (Days)</label>
            <Input type="number" placeholder="e.g. 90" />
          </div>
        </div>

        <div class="mt-8 border-t border-dashed border-gray-300 dark:border-gray-700 my-3"></div>
      </div>

      <!-- Integrations -->
      <div>
        <div class="mb-6">
          <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Integrations</h2>
          <p class="text-xs text-gray-500 dark:text-gray-400">Connect with other tools.</p>
        </div>

        <div class="space-y-6">
          <div class="flex justify-between items-center ">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Teamwork Integration</label>
            <Circle class="w-4 h-4 text-gray-500 cursor-pointer dark:text-gray-400" />
          </div>

          <div class="flex justify-between items-center ">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Slack Integration</label>
            <Circle class="w-4 h-4 text-gray-500 cursor-pointer dark:text-gray-400" />
          </div>

          <div class="flex justify-between items-center ">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">GitHub Integration</label>
            <Circle class="w-4 h-4 text-gray-500 cursor-pointer dark:text-gray-400" />
          </div>

          <div class="flex justify-between items-center ">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Notion Integration</label>
            <Circle class="w-4 h-4 text-gray-500 cursor-pointer dark:text-gray-400" />
          </div>

          <div class="flex justify-between items-center ">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Webhook URL</label>
            <Input type="text" placeholder="https://hooks.example.com" />
          </div>

          <div class="flex justify-between items-center ">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Custom Automation Rules</label>
            <Input type="text" placeholder="Describe or link automation rules" />
          </div>

          <div class="flex justify-between items-center ">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Google Calendar Sync</label>
            <Circle class="w-4 h-4 text-gray-500 cursor-pointer dark:text-gray-400" />
          </div>
        </div>

        <div class="mt-8 border-t border-dashed border-gray-300 dark:border-gray-700 my-3"></div>
      </div>

      <!-- Advanced Settings -->
      <div>
        <div class="mb-6">
          <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Advanced</h2>
          <p class="text-xs text-gray-500 dark:text-gray-400">Settings for developers and power users.</p>
        </div>

        <div class="space-y-6">
          <div class="flex justify-between items-center ">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Debug Mode</label>
            <Circle class="w-4 h-4 text-gray-500 cursor-pointer dark:text-gray-400" />
          </div>

          <div class="flex justify-between items-center ">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">API Endpoint</label>
            <Input type="text" placeholder="https://api.example.com" />
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

