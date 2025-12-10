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
  <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
    <div class="w-full max-w-md p-6 bg-white rounded-lg shadow">
      <h1 class="text-xl font-semibold mb-4 text-center">Reset Password</h1>
      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="text-sm">Email</label>
          <input v-model="form.email" type="email" readonly class="w-full border rounded px-3 py-2 bg-gray-100" />
        </div>
        <div>
          <label class="text-sm">Password Baru</label>
          <div class="relative">
            <input :type="show ? 'text' : 'password'" v-model="form.password" class="w-full border rounded px-3 py-2" required />
            <button type="button" class="absolute inset-y-0 right-2 text-xs text-gray-600" @click="toggle">{{ show ? 'Hide' : 'Show' }}</button>
          </div>
          <p v-if="form.errors.password" class="mt-1 text-xs text-red-600">{{ form.errors.password }}</p>
        </div>
        <div>
          <label class="text-sm">Konfirmasi Password</label>
          <input type="password" v-model="form.password_confirmation" class="w-full border rounded px-3 py-2" required />
          <p v-if="form.errors.password_confirmation" class="mt-1 text-xs text-red-600">{{ form.errors.password_confirmation }}</p>
        </div>
        <button :disabled="form.processing" class="w-full py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
          {{ form.processing ? 'Memproses...' : 'Reset Password' }}
        </button>
        <p v-if="form.errors.email" class="mt-2 text-xs text-red-600">{{ form.errors.email }}</p>
      </form>
    </div>
  </div>
</template>