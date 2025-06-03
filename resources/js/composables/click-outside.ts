import type { DirectiveBinding } from 'vue'

export default {
  beforeMount(el: HTMLElement, binding: DirectiveBinding) {
    el.__clickOutside__ = (event: MouseEvent) => {
      if (!(el === event.target || el.contains(event.target as Node))) {
        binding.value(event)
      }
    }
    document.body.addEventListener('click', el.__clickOutside__)
  },
  unmounted(el: HTMLElement) {
    document.body.removeEventListener('click', el.__clickOutside__)
    el.__clickOutside__ = null
  },
}
