<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'

const form = useForm({
    nama: '',
    username: '',
    email: '',
    password: '',
    password_confirmation: '',
})

function submit() {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    })
}
</script>

<template>
    <div class="min-h-screen relative overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0" style="background-image: url('/assets/login/BackgroundWOverlay.png'); background-size: cover; background-position: center;"></div>

        <!-- Logo -->
        <div class="relative z-10 flex justify-center pt-16 pb-8">
            <img src="/assets/login/Logo.png" alt="NGUNDUR Logo" class="h-16 w-auto" />
        </div>

        <!-- Form Card -->
        <div class="relative z-10 flex justify-center px-4">
            <div class="w-full max-w-md rounded-xl shadow-lg p-8" style="background-color: #BCD6B1;">
                <h1 class="text-2xl font-semibold text-white mb-8 text-center">Create Your Account</h1>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-white mb-2">Username</label>
                        <input
                            v-model="form.username"
                            type="text"
                            required
                            class="w-full px-4 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                            :class="{ 'border-red-500': form.errors.username }"
                        />
                        <div v-if="form.errors.username" class="text-red-200 text-sm mt-1">{{ form.errors.username }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white mb-2">E-mail</label>
                        <input
                            v-model="form.email"
                            type="email"
                            required
                            class="w-full px-4 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                            :class="{ 'border-red-500': form.errors.email }"
                        />
                        <div v-if="form.errors.email" class="text-red-200 text-sm mt-1">{{ form.errors.email }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white mb-2">Name</label>
                        <input
                            v-model="form.nama"
                            type="text"
                            required
                            class="w-full px-4 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                            :class="{ 'border-red-500': form.errors.nama }"
                        />
                        <div v-if="form.errors.nama" class="text-red-200 text-sm mt-1">{{ form.errors.nama }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white mb-2">Password</label>
                        <input
                            v-model="form.password"
                            type="password"
                            required
                            class="w-full px-4 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                            :class="{ 'border-red-500': form.errors.password }"
                        />
                        <div v-if="form.errors.password" class="text-red-200 text-sm mt-1">{{ form.errors.password }}</div>
                    </div>

                    <button :disabled="form.processing" type="submit" class="w-full py-3 rounded-md bg-blue-600 text-white font-medium hover:bg-blue-700 transition-colors disabled:opacity-50">
                        {{ form.processing ? 'Creating account...' : 'Sign Up' }}
                    </button>
                </form>

                <p class="text-sm text-white mt-6 text-center">
                    I'm already a member!
                    <a :href="route('login')" class="text-blue-200 underline">Sign in</a>
                </p>
            </div>
        </div>
    </div>
</template>
