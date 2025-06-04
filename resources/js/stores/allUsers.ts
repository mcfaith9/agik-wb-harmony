// resources/js/stores/allUsers.ts
import { reactive } from 'vue'

const users = reactive<User[]>([])
let fetched = false

async function fetchUsers() {
  if (fetched) {
    // Already fetched, just return existing data
    return users
  }
  const res = await fetch('/api/app/users')
  const data = await res.json()
  users.splice(0, users.length, ...data)
  fetched = true
  return users
}

export { users, fetchUsers }