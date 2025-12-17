<script setup>
import { ref } from 'vue'
import { useForm, usePage, router } from '@inertiajs/vue3'

import Logo from '*/dashboard/logo-tandur.png'
import Background from '*/login/BackgroundWOverlay.png'

const page = usePage()

const form = useForm({
  login: '',
  password: '',
})

const showPassword = ref(false)
const togglePassword = () => {
  showPassword.value = !showPassword.value
}

function submit() {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  })
}

// Forgot Password
const showReset = ref(false)
const resetForm = useForm({
  email: '',
})

// Pop-up Alert state
const showAlert = ref(false)
const alertTitle = ref('')
const alertMessage = ref('')

function openAlert(title, message) {
  alertTitle.value = title
  alertMessage.value = message
  showAlert.value = true
}
function closeAlert() {
  showAlert.value = false
  alertTitle.value = ''
  alertMessage.value = ''
}

function sendResetLink() {
  resetForm.post(route('password.email'), {
    preserveScroll: true,
    onSuccess: () => {
      // Laravel biasanya mengembalikan status di flash('status')
      const status = page.props.flash?.status || 'Link reset password telah dikirim ke email Anda.'
      openAlert('Berhasil', status)
      showReset.value = false
      resetForm.reset()
    },
    onError: (errors) => {
      // Jika controller mengembalikan error JSON, tangkap flash('error') atau errors.email
      const err = page.props.flash?.error || errors.email || 'Gagal mengirim link reset. Periksa email Anda.'
      openAlert('Gagal', err)
    }
  })
}
</script>

<template>
  <div class="relative min-h-screen overflow-hidden">
    <!-- Background Image -->
    <div class="absolute inset-0" :style="{ backgroundImage: `url(${Background})`, backgroundSize: 'cover', backgroundPosition: 'center' }"></div>

    <!-- Logo -->
    <div class="relative z-10 flex justify-center pt-16 pb-8">
      <img :src="Logo" alt="NGUNDUR Logo" class="w-auto h-16" />
    </div>

    <!-- Form Card -->
    <div class="relative z-10 flex justify-center px-4">
      <div class="w-full max-w-md p-8 shadow-lg rounded-xl" style="background-color: #BCD6B1;">
        <h1 class="mb-8 text-2xl font-semibold text-center text-white">Sign In</h1>

        <form @submit.prevent="submit" class="space-y-6">
          <div>
            <label class="block mb-2 text-sm font-medium text-white">Email or Username</label>
            <input
              v-model="form.login"
              type="text"
              required
              placeholder="Enter your email or username"
              class="w-full px-4 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
              :class="{ 'border-red-500': form.errors.login }"
            />
            <div v-if="form.errors.login" class="mt-1 text-sm text-red-200">{{ form.errors.login }}</div>
          </div>

          <div>
            <label class="block mb-2 text-sm font-medium text-white">Password</label>
            <div class="relative">
              <input
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                required
                placeholder="Enter your password"
                class="w-full px-4 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                :class="{ 'border-red-500': form.errors.password }"
              />
              <button
                type="button"
                class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-gray-700"
                :aria-label="showPassword ? 'Hide password' : 'Show password'"
                @click="togglePassword"
              >
                <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.25 12s3.75-7.5 9.75-7.5 9.75 7.5 9.75 7.5-3.75 7.5-9.75 7.5S2.25 12 2.25 12z" />
                  <circle cx="12" cy="12" r="3" stroke-width="2" stroke="currentColor" />
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3l18 18M10.58 10.58A3 3 0 0113.5 13.5M6.1 6.1C3.9 7.9 2.25 12 2.25 12s3.75 7.5 9.75 7.5c2.02 0 3.82-.52 5.34-1.38M13.42 13.42C12.99 13.8 12.52 14 12 14a3 3 0 01-3-3c0-.52.2-.99.58-1.42M17.9 17.9C20.1 16.1 21.75 12 21.75 12s-3.75-7.5-9.75-7.5c-1.03 0-2.01.15-2.93.42" />
                </svg>
              </button>
            </div>
            <div v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</div>
            <!-- Lupa Password link aligned right under password -->
            <div class="flex justify-end mt-2">
              <button type="button" class="text-sm text-black underline" @click="showReset = !showReset">
                {{ showReset ? 'Tutup Reset Password' : 'Lupa Password?' }}
              </button>
            </div>
          </div>

          <button :disabled="form.processing" type="submit" class="w-full py-3 font-medium text-white transition-colors bg-blue-600 rounded-md hover:bg-blue-700 disabled:opacity-50">
            {{ form.processing ? 'Signing in...' : 'Sign In' }}
          </button>
        </form>

        <!-- Reset Password panel -->
        <div v-if="showReset" class="p-4 mt-4 rounded-md bg-white/80">
          <h2 class="mb-2 text-sm font-semibold text-gray-800">Reset Password</h2>
          <p class="mb-3 text-xs text-gray-600">Masukkan email Anda. Kami akan mengirimkan link untuk reset password.</p>
          <div>
            <label class="block mb-2 text-xs font-medium text-gray-700">Email</label>
            <input
              v-model="resetForm.email"
              type="email"
              required
              placeholder="your@email.com"
              class="w-full px-4 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
              :class="{ 'border-red-500': resetForm.errors.email }"
            />
            <div v-if="resetForm.errors.email" class="mt-1 text-xs text-red-600">{{ resetForm.errors.email }}</div>
          </div>
          <button
            :disabled="resetForm.processing"
            @click="sendResetLink"
            class="w-full py-2 mt-3 font-medium text-white bg-green-600 rounded-md hover:bg-green-700 disabled:opacity-50"
          >
            {{ resetForm.processing ? 'Mengirim...' : 'Kirim Link Reset' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Alert Pop-up -->
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="translate-y-4 opacity-0"
      enter-to-class="translate-y-0 opacity-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="translate-y-0 opacity-100"
      leave-to-class="translate-y-4 opacity-0"
    >
      <div v-if="showAlert" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" @click="closeAlert">
        <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-xl" @click.stop>
          <h3 class="mb-2 text-xl font-bold text-center text-gray-900">{{ alertTitle }}</h3>
          <p class="mb-6 text-sm text-center text-gray-600">{{ alertMessage }}</p>
          <button @click="closeAlert" class="w-full px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">
            Tutup
          </button>
        </div>
      </div>
    </Transition>
  </div>
</template>
