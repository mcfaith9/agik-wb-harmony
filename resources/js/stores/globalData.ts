import { fetchUsers } from '@/stores/allUsers'
import { fetchTags } from '@/stores/allTags'
// import { fetchRoles } from '@/stores/allRoles'
// import { fetchSettings } from '@/stores/settings'

export default {
  install: async () => {
    try {
      await Promise.all([
        fetchUsers(),
        fetchTags(),
        // fetchRoles(),
        // fetchSettings()
      ])
    } catch (err) {
      console.error('Failed to preload app data:', err)
      // Optionally show global error toast or fallback
    }
  }
}
