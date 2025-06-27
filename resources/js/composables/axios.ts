import axios from "axios"
import { showAlert } from "@/composables/globalAlert"

axios.interceptors.response.use(
  (response) => {
    console.log("Interceptor: success", response)
    if (response.data?.message) {
      showAlert({
        title: "Success",
        message: response.data.message,
        variant: "success",
      })
    }
    return response
  },
  (error) => {
    console.log("Interceptor: error", error)
    const msg =
      error.response?.data?.message || error.message || "Something went wrong"
    showAlert({
      title: "Error",
      message: msg,
      variant: "error",
    })
    return Promise.reject(error)
  }
)
