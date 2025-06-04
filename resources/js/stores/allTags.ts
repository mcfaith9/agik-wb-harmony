import { reactive } from 'vue'

const tags = reactive<Tag[]>([])
let fetched = false

async function fetchTags() {
  if (fetched) {
    return tags
  }
  const res = await fetch('/api/tags')
  const data = await res.json()
  tags.splice(0, tags.length, ...data)
  fetched = true
  return tags
}

export { tags, fetchTags }
