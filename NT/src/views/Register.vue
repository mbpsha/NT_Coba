<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../api'

const router = useRouter()
const name = ref('')
const username = ref('')
const email = ref('')
const password = ref('')
const password_confirmation = ref('')
const loading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

async function submit() {
	loading.value = true
	errorMessage.value = ''
	successMessage.value = ''
	try {
		await api.post('/register', {
			nama: name.value,
			username: username.value,
			email: email.value,
			password: password.value,
		})
		successMessage.value = 'Registered successfully. Please login.'
		setTimeout(() => router.push({ name: 'login' }), 700)
	} catch (err) {
		errorMessage.value = err?.response?.data?.message || 'Register failed'
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
				<h1 class="text-2xl font-semibold text-white mb-8 text-center">Create Your Account</h1>

				<form @submit.prevent="submit" class="space-y-6">
					<div>
						<label class="block text-sm font-medium text-white mb-2">Username</label>
						<input v-model="username" type="text" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" />
					</div>
					<div>
						<label class="block text-sm font-medium text-white mb-2">E-mail</label>
						<input v-model="email" type="email" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" />
					</div>
					<div>
						<label class="block text-sm font-medium text-white mb-2">Name</label>
						<input v-model="name" type="text" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" />
					</div>
					<div>
						<label class="block text-sm font-medium text-white mb-2">Password</label>
						<input v-model="password" type="password" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" />
					</div>
					<div>
						<label class="block text-sm font-medium text-white mb-2">Confirm Password</label>
						<input v-model="password_confirmation" type="password" required class="w-full px-4 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" />
					</div>

					<button :disabled="loading" type="submit" class="w-full py-3 rounded-md bg-blue-600 text-white font-medium hover:bg-blue-700 transition-colors">
						{{ loading ? 'Creating account...' : 'Sign Up' }}
					</button>
				</form>

				<p v-if="errorMessage" class="text-red-200 text-sm mt-4 text-center">{{ errorMessage }}</p>
				<p v-if="successMessage" class="text-emerald-200 text-sm mt-4 text-center">{{ successMessage }}</p>

				<p class="text-sm text-white mt-6 text-center">
					I'm already a member!
					<router-link to="/login" class="text-blue-200 underline">Sign in</router-link>
				</p>
			</div>
		</div>
	</div>
</template>
