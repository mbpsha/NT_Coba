<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../api'

const router = useRouter()
const login = ref('')
const password = ref('')
const loading = ref(false)
const errorMessage = ref('')

async function submit() {
	loading.value = true
	errorMessage.value = ''
	try {
		const { data } = await api.post('/login', { login: login.value, password: password.value })
		localStorage.setItem('token', data.token)
		// Set default header untuk request selanjutnya
		api.defaults.headers.common['Authorization'] = `Bearer ${data.token}`
		router.push({ name: 'dashboard' })
	} catch (err) {
		errorMessage.value = err?.response?.data?.error || err?.response?.data?.message || 'Login failed'
	} finally {
		loading.value = false
	}
}
</script>

<template>
	<div class="min-h-screen relative overflow-hidden">
		<!-- Background Image -->
		<div class="absolute inset-0" style="background-image: url('/BackgroundWOverlay.png'); background-size: cover; background-position: center;"></div>

		<!-- Logo -->
		<div class="relative z-10 flex justify-center pt-16 pb-8">
			<img src="/Logo.png" alt="NGUNDUR Logo" class="h-16 w-auto" />
		</div>

		<!-- Form Card -->
		<div class="relative z-10 flex justify-center px-4">
			<div class="w-full max-w-md rounded-xl shadow-lg p-8" style="background-color: #BCD6B1;">
				<h1 class="text-2xl font-semibold text-white mb-8 text-center">Sign In</h1>

				<form @submit.prevent="submit" class="space-y-6">
					<div>
						<label class="block text-sm font-medium text-white mb-2">Email or Username</label>
						<input v-model="login" type="text" required placeholder="Enter your email or username" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" />
					</div>
					<div>
						<label class="block text-sm font-medium text-white mb-2">Password</label>
						<input v-model="password" type="password" required placeholder="Enter your password" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" />
					</div>

					<button :disabled="loading" type="submit" class="w-full py-3 rounded-md bg-blue-600 text-white font-medium hover:bg-blue-700 transition-colors">
						{{ loading ? 'Signing in...' : 'Sign In' }}
					</button>
				</form>

				<p v-if="errorMessage" class="text-red-200 text-sm mt-4 text-center">{{ errorMessage }}</p>

				<p class="text-sm text-white mt-6 text-center">
					Don't Have an Account?
					<router-link to="/register" class="text-blue-200 underline">Sign Up</router-link>
				</p>
			</div>
		</div>
	</div>
</template>
