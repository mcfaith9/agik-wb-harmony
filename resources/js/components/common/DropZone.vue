<script setup lang="ts">
  import { ref } from 'vue'
  import { CloudUpload, SquareCheckBig } from 'lucide-vue-next'

  interface FilePreview {
    src: string
    type: string
    name: string
  }

  const previews = ref<string[]>([])
  const progresses = ref<number[]>([])

  function simulateUpload(index: number) {
    progresses.value[index] = 0
    const interval = setInterval(() => {
      if (progresses.value[index] >= 100) {
        clearInterval(interval)
      } else {
        progresses.value[index] += 5
      }
    }, 100)
  }

  function handleFiles(fileList: FileList | null) {
    if (!fileList) return

    const files = Array.from(fileList)
    previews.value = []
    progresses.value = []

    files.forEach((file, idx) => {
      const reader = new FileReader()
      reader.onload = () => {
        const isImage = file.type.startsWith('image/')
        previews.value.push({
          src: isImage ? (reader.result as string) : '',
          type: file.type,
          name: file.name
        })
        progresses.value.push(0)
        simulateUpload(idx)
      }
      reader.readAsDataURL(file)
    })

    console.log('Selected files:', files)
  }

  function onFileChange(event: Event) {
    const input = event.target as HTMLInputElement
    handleFiles(input.files)
  }

  function onDrop(event: DragEvent) {
    event.preventDefault()
    handleFiles(event.dataTransfer?.files || null)
  }
</script>

<template>
  <div
    @dragover.prevent
    @drop="onDrop"
    class="flex flex-col items-center justify-center mt-2 w-full min-h-22 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-800 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600 transition">
    
    <label for="dropzone-file" class="w-full h-full flex flex-col items-center justify-center px-4 py-6 text-center">      
      <template v-if="!previews.length">
        <CloudUpload class="w-6 h-6 mb-1 text-gray-500 dark:text-gray-400" />
        <p class="mb-1 text-xs text-gray-500 dark:text-gray-400">
          <span class="font-semibold">Click to upload</span> or drag and drop
        </p>
        <p class="text-xs text-gray-400 dark:text-gray-500">SVG, PNG, JPG or GIF (MAX. 800x400px)</p>
      </template>

      <input
        id="dropzone-file"
        type="file"
        class="hidden"
        multiple
        accept="image/*"
        @change="onFileChange" />

      <div v-if="previews.length" class="grid grid-cols-3 gap-2 w-full">
        <div
          v-for="(file, index) in previews"
          :key="index"
          class="relative border rounded overflow-hidden h-20 w-full flex items-center justify-center bg-white dark:bg-gray-700">
          
          <img
            v-if="file.type.startsWith('image/')"
            :src="file.src"
            alt="preview"
            class="object-contain h-full w-full" />

          <div
            v-else
            class="flex flex-col items-center justify-center text-xs text-gray-700 dark:text-gray-200 p-2 text-center w-full h-full">
            <span class="truncate w-full">{{ file.name }}</span>
            <span class="text-gray-400 dark:text-gray-500 text-[10px] mt-1">{{ file.type }}</span>
          </div>

          <SquareCheckBig
            v-if="progresses[index] >= 100"
            class="absolute top-1 left-1 w-4 h-4 text-green-500 bg-white dark:bg-gray-800 rounded-full p-0.5 shadow-lg" />

          <div
            v-if="progresses[index] < 100"
            class="absolute bottom-0 left-0 w-full h-1 bg-gray-300 dark:bg-gray-600">
            <div
              class="h-full bg-blue-500 transition-all"
              :style="{ width: progresses[index] + '%' }"/>
          </div>
        </div>
      </div>
    </label>   
  </div>
</template>
