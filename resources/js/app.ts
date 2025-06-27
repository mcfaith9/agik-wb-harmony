import '../css/app.css'
// Import Swiper styles
import 'swiper/css'
import 'swiper/css/navigation'
import 'swiper/css/pagination'
import 'flatpickr/dist/flatpickr.css'
import clickOutside from './composables/click-outside'

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import axios from 'axios'
import "@/composables/axios"

axios.defaults.withCredentials = true

const app = createApp(App)

app.use(router)
app.directive('click-outside', clickOutside)
app.mount('#app')
