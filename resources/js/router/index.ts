import { createRouter, createWebHistory } from 'vue-router'
import axios from 'axios'

const routes = [
  {
    path: '/',
    name: 'Dashboard',
    component: () => import('../views/Ecommerce.vue'),
    meta: {
      title: 'Dashboard',
      description: 'Monitor task progress, project performance, and team activity—all in one snapshot.',
      requiresAuth: true,
    },
  },
  {
    path: '/tasks',
    name: 'Tasks',
    component: () => import('../views/Workflow/Task.vue'),
    meta: {
      title: 'Tasks',
      description: 'Stay productive—create, track, and complete tasks with ease.',
      requiresAuth: true,
    },
  },
  {
    path: '/projects',
    name: 'Projects',
    component: () => import('../views/Workflow/Project.vue'),
    meta: {
      title: 'Projects',
      description: 'Organize your work—manage active and archived projects effortlessly.',
      requiresAuth: true,
    },
  },
  {
    path: '/calendar',
    name: 'Calendar',
    component: () => import('../views/Others/Calendar.vue'),
    meta: {
      title: 'Calendar',
      description: 'Never miss a deadline—view and plan events, meetings, and due dates.',
      requiresAuth: true,
    },
  },
  {
    path: '/profile',
    name: 'Profile',
    component: () => import('../views/Others/UserProfile.vue'),
    meta: {
      title: 'Profile',
      description: 'Customize your experience—edit your info, avatar, and account settings.',
      requiresAuth: true,
    },
  },
  {
    path: '/admin/users',
    name: 'Users',
    component: () => import('../views/Admin/Users/UserList.vue'),
    meta: {
      title: 'User List',
      description: 'Manage platform users—view, edit, and control user access effortlessly.',
      requiresAuth: true,
    },
  },
  {
    path: '/admin/roles',
    name: 'Roles',
    component: () => import('../views/Admin/Roles/RolesList.vue'),
    meta: {
      title: 'Roles',
      description: 'Define access—assign roles and permissions to control user capabilities.',
      requiresAuth: true,
    },
  },
  {
    path: '/admin/settings',
    name: 'Admin Settings',
    component: () => import('../views/Admin/Settings/AdminSettings.vue'),
    meta: {
      title: 'Admin Settings',
      description: 'Fine-tune system configurations and platform-wide administrative options.',
      requiresAuth: true,
    },
  },
  {
    path: '/settings',
    name: 'Settings',
    component: () => import('../views/Settings.vue'),
    meta: {
      title: 'Settings',
      description: 'Adjust your preferences—notifications, themes, and more.',
      requiresAuth: true,
    },
  },
  {
    path: '/signin',
    name: 'Signin',
    component: () => import('../views/Auth/Signin.vue'),
    meta: {
      title: 'Signin',
      description: 'Welcome back—sign in to access your projects, tasks, and team.',
      guestOnly: true,
    },
  },
  {
    path: '/signup',
    name: 'Signup',
    component: () => import('../views/Auth/Signup.vue'),
    meta: {
      title: 'Signup',
      description: 'Get started—create your account and join the productivity movement.',
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