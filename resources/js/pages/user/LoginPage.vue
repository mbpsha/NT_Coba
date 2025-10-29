<template>
  <div class="min-h-screen bg-[url('/resources/js/assets/bg-depan01.png')] bg-cover bg-center flex flex-col">
    <header class="h-16 flex items-center justify-between px-4 bg-white/80 backdrop-blur shadow-sm">
      <div class="flex items-center gap-2">
        <img :src="Logo" alt="NGUNDUR" class="h-14" />
      </div>
      <RouterLink to="/" class="px-4 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white text-sm">Kembali ke Home</RouterLink>
    </header>

    <main class="flex-1 flex items-center justify-center p-4">
      <div class="w-full max-w-md bg-green-50/90 rounded-xl border border-green-200 shadow-2xl p-6">
        <div class="flex justify-center mb-2"><img :src="Logo" class="h-10"/></div>
        <h1 class="text-center text-lg font-semibold text-gray-700 mb-6">Masuk</h1>
        <form @submit.prevent="onSubmit" class="space-y-4">
          <div>
            <label class="block text-sm mb-1">Username/ E - mail</label>
            <input v-model="identifier" type="text" class="w-full rounded-md bg-white/70 border border-green-200 px-3 py-2 focus:ring-2 focus:ring-green-400" required />
          </div>
          <div>
            <label class="block text-sm mb-1">Kata sandi</label>
            <input v-model="password" type="password" class="w-full rounded-md bg-white/70 border border-green-200 px-3 py-2 focus:ring-2 focus:ring-green-400" required />
          </div>
          <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
          <button type="submit" class="w-full mt-2 px-4 py-2 rounded-full text-white bg-blue-500 hover:bg-blue-600">Masuk</button>
        </form>
        <div class="mt-3 text-xs text-gray-600 text-center">
          Belum memiliki akun?
          <RouterLink to="/register" class="text-green-700 hover:underline">Daftar</RouterLink>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import { useAuth } from '../../composables/useAuth'
import Logo from '../../assets/logo-ngundur.png'

const router = useRouter()
const { login, hasRole } = useAuth()

const identifier = ref('')
const password = ref('')
const error = ref('')

function onSubmit() {
  if (!identifier.value || !password.value) {
    error.value = 'Isi kredensial dengan benar.'
    return
  }
  const result = login(identifier.value, password.value)
  // Prefer role check for redirect; fall back to result.isAdmin
  if (hasRole('admin') || result?.isAdmin) {
    router.push('/admin')
  } else {
    router.push('/')
  }
}
</script>
