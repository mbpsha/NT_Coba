<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../api'

const router = useRouter()
const user = ref(null)
const loading = ref(true)
const errorMessage = ref('')

async function loadMe() {
	loading.value = true
	try {
		const { data } = await api.get('/me')
		user.value = data.user
	} catch (err) {
		errorMessage.value = err?.response?.data?.message || 'Failed to load profile'
		if (err?.response?.status === 401) {
			localStorage.removeItem('token')
			router.replace({ name: 'login' })
		}
	} finally {
		loading.value = false
	}
}

async function logout() {
	try {
		await api.post('/logout')
	} catch (_) {
		// ignore
	} finally {
		localStorage.removeItem('token')
		router.replace({ name: 'login' })
	}
}

onMounted(loadMe)
</script>

<template>
	<div style="max-width:720px;margin:32px auto;padding:16px;">
		<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
			<h1>Dashboard</h1>
			<button @click="logout" style="padding:8px 12px;border:1px solid #111827;background:white;border-radius:6px;">Logout</button>
		</div>

		<div v-if="loading">Loading...</div>
		<p v-if="errorMessage" style="color:#b91c1c;">{{ errorMessage }}</p>

		<div v-if="user" style="border:1px solid #e5e7eb;border-radius:8px;padding:16px;">
			<p><strong>Name:</strong> {{ user.name }}</p>
			<p><strong>Username:</strong> {{ user.username }}</p>
			<p><strong>Email:</strong> {{ user.email }}</p>
			<p><strong>Role:</strong> {{ user.role }}</p>
		</div>
	</div>
</template> 