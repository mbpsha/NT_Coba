import { createRouter, createWebHistory } from 'vue-router'
import { useAuth } from '../composables/useAuth'
import HomePage from '../pages/user/HomePage.vue'
import LoginPage from '../pages/user/LoginPage.vue'
import RegisterPage from '../pages/user/RegisterPage.vue'
import CartPage from '../pages/user/CartPage.vue'
import ProfilePage from '../pages/user/ProfilePage.vue'
import NewsDetailPage from '../pages/user/NewsDetailPage.vue'
import AdminDashboard from '../pages/admin/Dashboard.vue'
import AdminProducts from '../pages/admin/Products.vue'
import TokoPage from '../pages/user/TokoPage.vue'

const routes = [
  { path: '/', name: 'home', component: HomePage },
  { path: '/login', name: 'login', component: LoginPage },
  { path: '/register', name: 'register', component: RegisterPage },
  { path: '/cart', name: 'cart', component: CartPage },
  { path: '/profile', name: 'profile', component: ProfilePage },
  { path: '/news/:slug', name: 'news-detail', component: NewsDetailPage },
  { path: '/toko', name: 'toko', component: TokoPage },
  { path: '/admin/products', name: 'admin-products', component: AdminProducts, meta: { requiresAuth: true, roles: ['admin'] } },
  { path: '/admin', name: 'admin', component: AdminDashboard, meta: { requiresAuth: true, roles: ['admin'] } },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() { return { top: 0 } }
})

// Global auth/role guard for routes that require specific roles
router.beforeEach((to) => {
  const { isAuthenticated, hasRole } = useAuth()
  if (to.meta?.requiresAuth && !isAuthenticated.value) {
    return { name: 'login', query: { redirect: to.fullPath } }
  }
  if (to.meta?.roles && Array.isArray(to.meta.roles)) {
    const allowed = to.meta.roles.some(r => hasRole(r))
    if (!allowed) return { name: 'home' }
  }
  return true
})

export default router
