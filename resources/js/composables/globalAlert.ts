import { ref } from "vue"

export const alert = ref<{
  title: string
  message: string
  variant: "success" | "error" | "warning" | "info"
  showLink?: boolean
  linkHref?: string
  linkText?: string
  duration?: number
  visible: boolean
}>({
  title: "",
  message: "",
  variant: "info",
  visible: false,
})

export function showAlert({
  title,
  message,
  variant = "info",
  showLink = false,
  linkHref = "#",
  linkText = "Learn more",
  duration = 3000,
}: Partial<typeof alert.value>) {
  alert.value = {
    title: title || "Notice",
    message: message || "",
    variant,
    showLink,
    linkHref,
    linkText,
    duration,
    visible: true,
  }
}
