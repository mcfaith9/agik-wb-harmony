<script setup lang="ts">
  import { ref, computed, watch, nextTick, onMounted, onBeforeUnmount, Ref } from 'vue'

  interface User {
    id: number | string
    first_name: string
    last_name: string
  }

  const props = defineProps<{
    modelValue: string
    users: User[]
  }>()

  const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void
  }>()

  const inputText = ref(props.modelValue)
  const mentionQuery = ref<string>('')
  const showPopover = ref<boolean>(false)
  const cursorPosition = ref<number>(0)
  const selectedIndex = ref<number>(0)

  // Properly typed refs for elements
  const textareaRef = ref<HTMLTextAreaElement | null>(null)
  const popoverRef = ref<HTMLElement | null>(null)
  const mentionUsers = ref<User[]>([])

  const filteredUsers = computed(() => {
    if (!mentionQuery.value) return []
    return mentionUsers.value.filter(user =>
      `${user.first_name} ${user.last_name}`
        .toLowerCase()
        .includes(mentionQuery.value.toLowerCase())
    )
  })

  const updatePopoverPosition = () => {
    nextTick(() => {
      const textarea = textareaRef.value
      const popover = popoverRef.value

      if (!textarea || !popover) return

      const style = window.getComputedStyle(textarea)
      const value = textarea.value
      const selectionStart = textarea.selectionStart

      const textUntilCursor = value.slice(0, selectionStart)
      const mentionMatch = textUntilCursor.match(/@([\w]*)$/)
      if (!mentionMatch) return

      const atPosition = selectionStart - mentionMatch[0].length

      const div = document.createElement('div')
      for (const prop of style) {
        div.style.setProperty(prop, style.getPropertyValue(prop))
      }
      div.style.position = 'absolute'
      div.style.visibility = 'hidden'
      div.style.whiteSpace = 'pre-wrap'
      div.style.wordWrap = 'break-word'
      div.style.overflow = 'hidden'
      div.style.width = textarea.offsetWidth + 'px'
      div.style.fontFamily = style.fontFamily
      div.style.fontSize = style.fontSize
      div.style.lineHeight = style.lineHeight
      div.style.padding = style.padding
      div.style.border = style.border

      div.textContent = value.substring(0, atPosition)

      const span = document.createElement('span')
      span.textContent = value.charAt(atPosition) || '@'
      div.appendChild(span)

      document.body.appendChild(div)

      const { offsetLeft, offsetTop } = span
      const { top, left } = textarea.getBoundingClientRect()
      const scrollY = window.scrollY || window.pageYOffset
      const scrollX = window.scrollX || window.pageXOffset

      const popoverHeight = popover.offsetHeight || 150
      const popoverWidth = popover.offsetWidth || 240

      let popoverLeft = left + offsetLeft + scrollX
      const popoverTop = top + offsetTop + scrollY - popoverHeight - 8

      // If it overflows right edge, reposition to the left
      const viewportWidth = window.innerWidth
      if (popoverLeft + popoverWidth > viewportWidth - 8) {
        popoverLeft = viewportWidth - popoverWidth - 8
      }

      popover.style.left = `${Math.max(popoverLeft, 8)}px`
      popover.style.top = `${popoverTop}px`

      document.body.removeChild(div)
    })
  }

  // Typing event parameter
  const onInput = (e: Event) => {
    const target = e.target as HTMLTextAreaElement
    const value = target.value
    inputText.value = value
    cursorPosition.value = target.selectionStart || 0

    const textUntilCursor = value.slice(0, cursorPosition.value)
    const match = textUntilCursor.match(/@([\w]*)$/)

    if (match) {
      mentionQuery.value = match[1]
      showPopover.value = true
      updatePopoverPosition()
    } else {
      showPopover.value = false
      mentionQuery.value = ''
    }

    emit('update:modelValue', inputText.value)
  }

  const insertMention = (user: User) => {
    const before = inputText.value.slice(0, cursorPosition.value)
    const after = inputText.value.slice(cursorPosition.value)
    const mentionText = `@${user.first_name}`
    const replaced = before.replace(/@\w*$/, mentionText)
    inputText.value = `${replaced} ${after}`
    showPopover.value = false
    mentionQuery.value = ''
    selectedIndex.value = 0

    nextTick(() => textareaRef.value?.focus())
    emit('update:modelValue', inputText.value)
  }

  // Typing event parameter as KeyboardEvent
  const onKeydown = (e: KeyboardEvent) => {
    if (!showPopover.value || !filteredUsers.value.length) return

    if (e.key === 'ArrowDown') {
      e.preventDefault()
      selectedIndex.value = (selectedIndex.value + 1) % filteredUsers.value.length
    } else if (e.key === 'ArrowUp') {
      e.preventDefault()
      selectedIndex.value = (selectedIndex.value - 1 + filteredUsers.value.length) % filteredUsers.value.length
    } else if (e.key === 'Enter') {
      e.preventDefault()
      insertMention(filteredUsers.value[selectedIndex.value])
    } else if (e.key === 'Escape') {
      showPopover.value = false
    }
  }

  const onWindowScrollOrResize = () => {
    if (showPopover.value) {
      updatePopoverPosition()
    }
  }

  watch(() => props.users, () => {
    mentionUsers.value = props.users
  },
  { immediate: true })

  watch(() => props.modelValue, (newVal) => {
    inputText.value = newVal
  })

  watch(inputText, (val) => {
    emit('update:modelValue', val)
  })

  onMounted(() => {
    window.addEventListener('scroll', onWindowScrollOrResize, true)
    window.addEventListener('resize', onWindowScrollOrResize)
  })

  onBeforeUnmount(() => {
    window.removeEventListener('scroll', onWindowScrollOrResize, true)
    window.removeEventListener('resize', onWindowScrollOrResize)
  })
</script>

<template>
  <div class="relative w-full">
    <textarea
      ref="textareaRef"
      v-model="inputText"
      class="custom-scrollbar w-full resize-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-none focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
      placeholder="Type @ to mention..."
      @input="onInput"
      @keydown="onKeydown"></textarea>

    <Teleport to="body">
      <ul
        v-if="showPopover && filteredUsers.length"
        ref="popoverRef"
        class="absolute z-[999999] w-60 max-h-48 overflow-auto border border-gray-300 divide-y divide-gray-200 custom-scrollbar max-h-60 dark:divide-gray-800 dark:border-gray-700 rounded-lg">
        <li
          v-for="(user, i) in filteredUsers"
          :key="user.id"
          @mousedown.prevent="insertMention(user)"
          :class="[
            'px-3 py-2 text-sm cursor-pointer select-none first:rounded-t-lg last:rounded-b-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-800',
            i === selectedIndex ? 'bg-gray-50 dark:bg-gray-900' : 'dark:bg-gray-800 hover:bg-gray-100'
          ]">
          <span class="text-sm text-gray-500 dark:text-gray-400">@{{ user.first_name }} {{ user.last_name }}</span>
        </li>
      </ul>
    </Teleport>
  </div>
</template>
