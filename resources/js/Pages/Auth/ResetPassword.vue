<script setup>
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  token: { type: String, required: true },
  email: { type: String, required: true },
})

const form = useForm({
  token: props.token,
  email: props.email,
  password: '',
  password_confirmation: '',
})

const show = ref(false)
const toggle = () => { show.value = !show.value }

const submit = () => {
  form.post(route('password.update'), {
    preserveScroll: true,
  })
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-b from-green-50 to-green-100 px-4 font-inter text-gray-900">
    <div class="w-full max-w-md p-6 md:p-7 bg-white rounded-2xl shadow-xl border border-green-100">
      <h1 class="text-2xl md:text-3xl font-extrabold mb-2 text-center text-[#285F3C]">Reset Password</h1>
      <p class="text-center text-sm text-gray-600 mb-5">Silakan masukkan password baru untuk akun Anda.</p>
      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="text-xs font-medium text-gray-700">Email</label>
          <input v-model="form.email" type="email" readonly class="w-full border border-green-200 rounded-md px-3 py-2 bg-green-50 text-gray-700" />
        </div>
        <div>
          <label class="text-xs font-medium text-gray-700">Password Baru</label>
          <div class="relative">
            <input :type="show ? 'text' : 'password'" v-model="form.password" class="w-full border border-green-200 rounded-md px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500" required />
            <button type="button" class="absolute inset-y-0 right-2 text-xs text-green-700" @click="toggle">{{ show ? 'Sembunyikan' : 'Tampilkan' }}</button>
          </div>
          <p v-if="form.errors.password" class="mt-1 text-xs text-red-600">{{ form.errors.password }}</p>
        </div>
        <div>
          <label class="text-xs font-medium text-gray-700">Konfirmasi Password</label>
          <input :type="show ? 'text' : 'password'" v-model="form.password_confirmation" class="w-full border border-green-200 rounded-md px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500" required />
          <p v-if="form.errors.password_confirmation" class="mt-1 text-xs text-red-600">{{ form.errors.password_confirmation }}</p>
        </div>
        <button :disabled="form.processing" class="w-full h-10 md:h-11 rounded-md bg-green-600 hover:bg-green-700 text-white text-sm font-medium disabled:opacity-50">
          {{ form.processing ? 'Memproses...' : 'Reset Password' }}
        </button>
        <p v-if="form.errors.email" class="mt-2 text-xs text-red-600">{{ form.errors.email }}</p>
      </form>
    </div>
  </div>
</template>