<script setup lang="ts">	
	import axios from 'axios'
	import { ref, nextTick, onMounted } from 'vue'
	import { tags as staticTags, colorPalette } from '@/stores/data'
	import Modal from '@/components/common/Modal.vue'
	import { X } from 'lucide-vue-next'

	const props = defineProps<{
	  isOpen: boolean
	}>()

	const emit = defineEmits<{
	  (e: 'close'): void
	}>()

	const tags = ref([...staticTags])
	const newTag = ref<string>('')
	const newColor = ref('#4f46e5')
	const activeDropdownIndex = ref<number | null>(null)
	const dropdownPosition = ref({ top: 0, left: 0 })
	const editLabel = ref('')
	const editColor = ref('')

	function addTag() {
	  if (!newTag.value.trim()) return

	  const tagToAdd = {
	    label: newTag.value.trim(),
	    color: newColor.value,
	  }

	  tags.value.push(tagToAdd)
	  console.log('Added Tag:', tagToAdd)

	  newTag.value = ''
	}

	function removeTag(index: number) {
	  tags.value.splice(index, 1)
	}

	function toggleDropdown(index, tag) {
	  if (activeDropdownIndex.value === index) {
	    activeDropdownIndex.value = null
	  } else {
	    editLabel.value = tag.label
	    editColor.value = tag.color
	    activeDropdownIndex.value = index
	    nextTick(() => {
	      updateDropdownPosition(index)
	    })
	  }
	}

	function updateDropdownPosition(index) {
	  const tagEl = document.querySelector(`#tag-${index}`)
	  if (tagEl) {
	    const rect = tagEl.getBoundingClientRect()
	    dropdownPosition.value = {
	      top: rect.bottom + window.scrollY + 8,
	      left: rect.left + window.scrollX,
	    }
	  }
	}

	function getDropdownPosition() {
	  return `top: ${dropdownPosition.value.top}px; left: ${dropdownPosition.value.left}px; position: absolute;`
	}

	async function updateTag(index: number) {
	  const tag = tags.value[index]

	  if (!tag.id) {
	    console.error('Cannot update tag without ID')
	    return
	  }

	  try {
	    await axios.put(`/api/tags/${tag.id}`, {
	      label: tag.label,
	      color: tag.color,
	    })
	    activeDropdownIndex.value = null
	    console.log('Tag updated successfully')
	  } catch (error) {
	    console.error('Failed to update tag:', error)
	  }
	}

	onMounted(async () => {
	  try {
	    const res = await axios.get('/api/tags')
	    tags.value = res.data
	  } catch (error) {
	    console.error('Failed to load tags:', error)
	  }
	})
</script>

<template>
	<Modal v-if="isOpen" @close="emit('close')">
	  <template #body>
	    <div class="no-scrollbar relative w-full max-w-[700px] overflow-hidden rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11">
	      <button
	        @click="emit('close')"
	        class="transition-color absolute right-5 top-5 z-999 flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-600 dark:bg-gray-700 dark:bg-white/[0.05] dark:text-gray-400 dark:hover:bg-white/[0.07] dark:hover:text-gray-300">
	        <X class="w-5 h-5" />
	      </button>
	      <div class="px-2 pr-14">
	        <h4 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90">
	          Create Tags
	        </h4>
	        <p class="mb-4 text-sm text-gray-500 dark:text-gray-400 lg:mb-4">
	          Tag it, color it, make it yours — colorful chaos encouraged.
	        </p>
	      </div>     

        <div class="flex flex-wrap gap-2 mb-4 overflow-y-auto custom-scrollbar">
          <div
            v-for="(tag, index) in tags"
            :key="index"
            :id="`tag-${index}`"
            class="relative cursor-pointer mt-3 inline-flex rounded-full text-xs font-medium"
            :style="{ backgroundColor: tag.color, color: tag.color ? 'white' : '' }"
           	@click.stop.prevent="tag.id && toggleDropdown(index, tag)">
            <div class="flex items-center px-2 py-0.5 dark:text-white">
              {{ tag.label }}
              <button @click.stop="removeTag(index)" class="ml-2 text-xs">
                <X class="w-3 h-3" />
              </button>
            </div>

            <Teleport to="body">
              <div
                v-if="activeDropdownIndex === index"
                v-click-outside="() => { activeDropdownIndex = null }"
                class="fixed z-99999 left-0 top-0 tag-dropdown"
                :style="getDropdownPosition()"
                @click.stop>
                <div class="select-none w-[10rem] rounded-2xl border border-gray-200 bg-white p-3 shadow-lg dark:border-gray-800 dark:bg-gray-900">
                  <input
                    v-model="editLabel"
                    @input="tags[index].label = editLabel"
                    type="text"
                    class="dark:bg-dark-900 mb-2 h-8 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"/>
                  <div class="grid grid-cols-4 gap-2 mb-3">
                    <div
                      v-for="color in colorPalette"
                      :key="color"
                      class="select-none w-6 h-6 rounded-full cursor-pointer border-3 transition border-gray-900"
                      :style="{
                        backgroundColor: color,
                        borderColor: editColor === color ? '' : 'transparent'
                      }"
                      @click="() => {
                        editColor = color
                        tags[index].color = color
                      }"></div>
                  </div>
                  <button
                    class="select-none w-full justify-center rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto"
                    @click="updateTag(index)">
                    Save Changes
                  </button>
                </div>
              </div>
            </Teleport>
          </div>
        </div>

        <!-- <div
          v-if="newTag"
          class="inline-flex items-center px-3 py-1 rounded-full text-white mb-4"
          :style="{ backgroundColor: newColor }">
          {{ newTag }}
        </div>

        <form class="flex flex-col gap-2" @submit.prevent="addTag">
          <input
            v-model="newTag"
            type="text"
            placeholder="Enter tag"
            class="border px-2 py-1 rounded"
          />
          <input
            v-model="newColor"
            type="color"
            class="w-8 h-8 p-0 border rounded"
          />
          <button
            type="submit"
            class="bg-blue-500 text-white px-3 py-1 rounded self-start"
          >
            Add
          </button>
        </form> -->

	    </div>
	  </template>
	</Modal>
</template>