import { reactive } from 'vue'

export const userStore = reactive({
  user: null,
  setUser(data) {
    this.user = data
  }
})
