import { createRouter, createWebHistory } from 'vue-router'
import Login from '../views/Login.vue'
import Register from '../views/Register.vue'
import Dashboard from '../views/Dashboard.vue'

const router = createRouter({
	history: createWebHistory(),
	routes: [
		{ path: '/', redirect: '/dashboard' },
		{ path: '/login', name: 'login', component: Login },
		{ path: '/register', name: 'register', component: Register },
		{ path: '/dashboard', name: 'dashboard', component: Dashboard, meta: { requiresAuth: true } },
	],
})

router.beforeEach((to, from, next) => {
	const token = localStorage.getItem('token')
	if (to.meta.requiresAuth && !token) {
		return next({ name: 'login' })
	}
	if (to.name === 'login' && token) {
		return next({ name: 'dashboard' })
	}
	next()
})

export default router 