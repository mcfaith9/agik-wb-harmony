import { createRouter, createWebHistory } from 'vue-router'
import axios from 'axios'

const routes = [
  {
    path: '/',
    name: 'Dashboard',
    component: () => import('../views/Ecommerce.vue'),
    meta: {
      title: 'Dashboard',
      description: 'Get a real-time overview of sales, revenue, and user activity across your platform.',
      requiresAuth: true,
    },
  },
  {
    path: '/tasks',
    name: 'Tasks',
    component: () => import('../views/Task/Task.vue'),
    meta: {
      title: 'Tasks',
      description: 'View, create, and manage your personal or team tasks efficiently.',
      requiresAuth: true,
    },
  },
  {
    path: '/projects',
    name: 'Projects',
    component: () => import('../views/Task/Project.vue'),
    meta: {
      title: 'Projects',
      description: 'Browse and organize all your active and archived projects in one place.',
      requiresAuth: true,
    },
  },
  {
    path: '/calendar',
    name: 'Calendar',
    component: () => import('../views/Others/Calendar.vue'),
    meta: {
      title: 'Calendar',
      description: 'Check your schedule and manage important events and deadlines.',
      requiresAuth: true,
    },
  },
  {
    path: '/profile',
    name: 'Profile',
    component: () => import('../views/Others/UserProfile.vue'),
    meta: {
      title: 'Profile',
      description: 'Update your personal information, avatar, and account settings.',
      requiresAuth: true,
    },
  },
  {
    path: '/basic-tables',
    name: 'Basic Tables',
    component: () => import('../views/Tables/BasicTables.vue'),
    meta: {
      title: 'Basic Tables',
      description: 'View structured data in a sortable and filterable table layout.',
      requiresAuth: true,
    },
  },
  {
    path: '/line-chart',
    name: 'Line Chart',
    component: () => import('../views/Chart/LineChart/LineChart.vue'),
    meta: {
      title: 'Line Chart',
      description: 'Visualize trends and performance over time with line graphs.',
    },
  },
  {
    path: '/bar-chart',
    name: 'Bar Chart',
    component: () => import('../views/Chart/BarChart/BarChart.vue'),
    meta: {
      title: 'Bar Chart',
      description: 'Analyze categorical data using customizable bar charts.',
    },
  },
  {
    path: '/signin',
    name: 'Signin',
    component: () => import('../views/Auth/Signin.vue'),
    meta: {
      title: 'Signin',
      description: 'Access your account by signing in with your credentials.',
      guestOnly: true,
    },
  },
  {
    path: '/signup',
    name: 'Signup',
    component: () => import('../views/Auth/Signup.vue'),
    meta: {
      title: 'Signup',
      description: 'Create a new account to start managing your projects and tasks.',
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
    return next({ name: 'Dashboard' })
  }

  next()
})

export default router