<template>
  <div class="min-h-screen bg-cover bg-center flex flex-col" :style="{ backgroundImage: `url(${BgHero})` }">
    <header class="h-16 flex items-center justify-between px-4 bg-white/80 backdrop-blur shadow-sm">
      <div class="flex items-center gap-2">
        <img :src="Logo" alt="NGUNDUR" class="h-14" />
      </div>
      <RouterLink to="/" class="px-4 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white text-sm">Kembali ke Home</RouterLink>
    </header>

    <main class="flex-1 flex items-center justify-center p-4">
      <div class="w-full max-w-2xl bg-green-50/90 rounded-xl border border-green-200 shadow-2xl p-6">
        <div class="flex justify-center mb-2"><img :src="Logo" class="h-10"/></div>
        <h1 class="text-center text-lg font-semibold text-gray-700 mb-6">Create Your Account</h1>
        <form @submit.prevent="onSubmit" class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="md:col-span-2">
            <label class="block text-sm mb-1">Username</label>
            <input v-model="form.username" type="text" class="w-full rounded-md bg-white/70 border border-green-200 px-3 py-2 focus:ring-2 focus:ring-green-400" required />
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm mb-1">Nama</label>
            <input v-model="form.fullname" type="text" class="w-full rounded-md bg-white/70 border border-green-200 px-3 py-2 focus:ring-2 focus:ring-green-400" placeholder="Opsional" />
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm mb-1">E- mail</label>
            <input v-model="form.email" type="email" class="w-full rounded-md bg-white/70 border border-green-200 px-3 py-2 focus:ring-2 focus:ring-green-400" required />
          </div>
          <div>
            <label class="block text-sm mb-1">Phone</label>
            <input v-model="form.phone" type="tel" inputmode="numeric" pattern="[0-9]*" maxlength="20"
                   @input="onPhoneInput"
                   class="w-full rounded-md bg-white/70 border border-green-200 px-3 py-2 focus:ring-2 focus:ring-green-400" />
          </div>
          <div>
            <label class="block text-sm mb-1">Address</label>
            <input v-model="form.address" type="text" class="w-full rounded-md bg-white/70 border border-green-200 px-3 py-2 focus:ring-2 focus:ring-green-400" />
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm mb-1">Password</label>
            <input v-model="form.password" type="password" class="w-full rounded-md bg-white/70 border border-green-200 px-3 py-2 focus:ring-2 focus:ring-green-400" minlength="6" required />
          </div>
          <div class="md:col-span-2 mt-2">
            <button type="submit" class="w-full px-4 py-2 rounded-full text-white bg-blue-500 hover:bg-blue-600">Sign Up</button>
          </div>
        </form>
        <div class="mt-3 text-xs text-gray-600 text-center">
          Sudah punya akun?
          <RouterLink to="/login" class="text-green-700 hover:underline">Sign in</RouterLink>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { reactive } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import { useAuth } from '../composables/useAuth'
import Logo from '../assets/logo-ngundur.png'
import BgHero from '../assets/bg-depan01.png'

const router = useRouter()
const { register } = useAuth()
const form = reactive({ username: '', fullname: '', email: '', phone: '', address: '', password: '' })

function onPhoneInput(e) {
  // enforce digits only
  form.phone = (e.target.value || '').replace(/\D+/g, '')
}

function onSubmit() {
  // final sanitize before sending
  form.phone = (form.phone || '').replace(/\D+/g, '')
  register(form)
  router.push('/login')
}
</script>
