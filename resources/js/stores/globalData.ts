import { fetchUsers } from './allUsers'
import { fetchTags } from './allTags'

let loaded = false

export async function preloadGlobalData() {
  if (loaded) return

  try {
    await Promise.all([
      fetchUsers(),
      fetchTags(),
    ])
    loaded = true
  } catch (err) {
    console.error('Failed to preload global data:', err)
  }
}

export function isGlobalDataLoaded() {
  return loaded
}

export function resetGlobalDataLoadedFlag() {
  loaded = false
}
