<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'

const form = useForm({
    login: '',
    password: '',
})

function submit() {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
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
                <h1 class="text-2xl font-semibold text-white mb-8 text-center">Sign In</h1>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-white mb-2">Email or Username</label>
                        <input
                            v-model="form.login"
                            type="text"
                            required
                            placeholder="Enter your email or username"
                            class="w-full px-4 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                            :class="{ 'border-red-500': form.errors.login }"
                        />
                        <div v-if="form.errors.login" class="text-red-200 text-sm mt-1">{{ form.errors.login }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white mb-2">Password</label>
                        <input
                            v-model="form.password"
                            type="password"
                            required
                            placeholder="Enter your password"
                            class="w-full px-4 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                            :class="{ 'border-red-500': form.errors.password }"
                        />
                        <div v-if="form.errors.password" class="text-red-200 text-sm mt-1">{{ form.errors.password }}</div>
                    </div>

                    <button :disabled="form.processing" type="submit" class="w-full py-3 rounded-md bg-blue-600 text-white font-medium hover:bg-blue-700 transition-colors disabled:opacity-50">
                        {{ form.processing ? 'Signing in...' : 'Sign In' }}
                    </button>
                </form>

                <p class="text-sm text-white mt-6 text-center">
                    Don't Have an Account?
                    <a :href="route('register')" class="text-blue-200 underline">Sign Up</a>
                </p>
            </div>
        </div>
    </div>
</template>
