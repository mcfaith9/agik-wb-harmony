import { createRouter, createWebHistory } from 'vue-router'
import axios from 'axios'

const routes = [
  {
    path: '/',
    name: 'Ecommerce',
    component: () => import('../views/Ecommerce.vue'),
    meta: {
      title: 'eCommerce Dashboard',
      requiresAuth: true,
    },
  },
  {
    path: '/task-list',
    name: 'Task List',
    component: () => import('../views/TaskList.vue'),
    meta: {
      title: 'Task List',
      requiresAuth: true,
    },
  },
  {
    path: '/calendar',
    name: 'Calendar',
    component: () => import('../views/Others/Calendar.vue'),
    meta: {
      title: 'Calendar',
      requiresAuth: true,
    },
  },
  {
    path: '/profile',
    name: 'Profile',
    component: () => import('../views/Others/UserProfile.vue'),
    meta: {
      title: 'Profile',
      requiresAuth: true,
    },
  },
  {
    path: '/form-elements',
    name: 'Form Elements',
    component: () => import('../views/Forms/FormElements.vue'),
    meta: {
      title: 'Form Elements',
      requiresAuth: true,
    },
  },
  {
    path: '/basic-tables',
    name: 'Basic Tables',
    component: () => import('../views/Tables/BasicTables.vue'),
    meta: {
      title: 'Basic Tables',
      requiresAuth: true,
    },
  },
  {
    path: '/line-chart',
    name: 'Line Chart',
    component: () => import('../views/Chart/LineChart/LineChart.vue'),
  },
  {
    path: '/bar-chart',
    name: 'Bar Chart',
    component: () => import('../views/Chart/BarChart/BarChart.vue'),
  },
  {
    path: '/signin',
    name: 'Signin',
    component: () => import('../views/Auth/Signin.vue'),
    meta: {
      title: 'Signin',
      guestOnly: true,
    },
  },
  {
    path: '/signup',
    name: 'Signup',
    component: () => import('../views/Auth/Signup.vue'),
    meta: {
      title: 'Signup',
      guestOnly: true,
    },
  },
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  scrollBehavior(to, from, savedPosition) {
    return savedPosition || { left: 0, top: 0 }
  },
  routes,
})

// Auth check function
async function isAuthenticated() {
  try {
    await axios.get('/is-authenticated')
    return true
  } catch (error) {
    return false
  }
}

// Navigation guard
router.beforeEach(async (to, from, next) => {
  document.title = `agik | Harmony - ${to.meta.title || ''}`

  const auth = await isAuthenticated()

  if (to.meta.requiresAuth && !auth) {
    // If route requires auth and user not logged in → redirect to signin
    return next({ name: 'Signin' })
  }

  if (to.meta.guestOnly && auth) {
    // If route is guest only but user is logged in → redirect to dashboard
    return next({ name: 'Ecommerce' })
  }

  next()
})

export default router