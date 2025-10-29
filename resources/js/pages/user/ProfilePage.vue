<template>
  <div class="min-h-screen flex flex-col bg-slate-50">
    <header class="h-16 flex items-center justify-between px-4 bg-white/80 backdrop-blur shadow-sm">
      <RouterLink to="/" class="flex items-center gap-2">
        <img :src="Logo" alt="NGUNDUR" class="h-14" />
      </RouterLink>
      <RouterLink to="/" class="px-4 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white text-sm">Kembali ke Home</RouterLink>
    </header>

    <main class="flex-1 max-w-3xl mx-auto w-full p-6">
      <h1 class="text-2xl font-semibold text-center mb-6">Profil Saya</h1>
      <div class="bg-green-50/70 rounded-xl border border-green-200 p-6 shadow">
        <div class="flex flex-col items-center mb-6">
          <div class="h-24 w-24 rounded-full bg-green-200 text-green-700 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-12 w-12">
              <path fill-rule="evenodd" d="M12 2a5 5 0 100 10 5 5 0 000-10zm-7 17a7 7 0 1114 0v1a1 1 0 01-1 1H6a1 1 0 01-1-1v-1z" clip-rule="evenodd" />
            </svg>
          </div>
          <p class="mt-2 text-gray-700 font-medium">{{ form.username }}</p>
        </div>

        <form @submit.prevent="save" class="grid grid-cols-1 gap-4">
          <div>
            <label class="block text-sm mb-1">Username</label>
            <input v-model="form.username" class="w-full rounded-md bg-white/80 border border-green-200 px-3 py-2" />
          </div>
          <div>
            <label class="block text-sm mb-1">Nama</label>
            <input v-model="form.fullname" class="w-full rounded-md bg-white/80 border border-green-200 px-3 py-2" placeholder="Opsional" />
          </div>
          <div>
            <label class="block text-sm mb-1">Email</label>
            <input v-model="form.email" type="email" class="w-full rounded-md bg-white/80 border border-green-200 px-3 py-2" />
          </div>
          <div>
            <label class="block text-sm mb-1">Nomor Telepon</label>
            <input v-model="form.phone" inputmode="numeric" pattern="[0-9]*" maxlength="20"
                   @input="onPhoneInput"
                   class="w-full rounded-md bg-white/80 border border-green-200 px-3 py-2" />
          </div>
          <div>
            <label class="block text-sm mb-1">Alamat</label>
            <input v-model="form.address" class="w-full rounded-md bg-white/80 border border-green-200 px-3 py-2" />
          </div>

          <div class="mt-2 flex gap-3">
            <button type="submit" class="px-5 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white">Selesai</button>
            <button type="button" @click="doLogout" class="px-5 py-2 rounded-lg bg-red-500 hover:bg-red-600 text-white">Logout</button>
          </div>
        </form>
      </div>
    </main>
  </div>
</template>

<script setup>
import { reactive } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import { useAuth } from '../../composables/useAuth'
import Logo from '../../assets/logo-ngundur.png'

const router = useRouter()
const { user, updateProfile, logout } = useAuth()
const form = reactive({
  username: user.username,
  fullname: user.fullname || '',
  email: user.email,
  phone: user.phone,
  address: user.address
})

function onPhoneInput(e) {
  form.phone = (e.target.value || '').replace(/\D+/g, '')
}

function save() {
  form.phone = (form.phone || '').replace(/\D+/g, '')
  updateProfile({
    username: form.username,
    email: form.email,
    phone: form.phone,
    address: form.address
  })
  router.push('/')
}

function doLogout() {
  logout()
  router.push('/')
}
</script>
