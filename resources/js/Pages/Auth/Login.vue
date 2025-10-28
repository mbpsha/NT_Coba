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
    <div class="relative min-h-screen overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0" style="background-image: url('/assets/login/BackgroundWOverlay.png'); background-size: cover; background-position: center;"></div>

        <!-- Logo -->
        <div class="relative z-10 flex justify-center pt-16 pb-8">
            <img src="/assets/login/Logo.png" alt="NGUNDUR Logo" class="w-auto h-16" />
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
                        <input
                            v-model="form.password"
                            type="password"
                            required
                            placeholder="Enter your password"
                            class="w-full px-4 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                            :class="{ 'border-red-500': form.errors.password }"
                        />
                        <div v-if="form.errors.password" class="mt-1 text-sm text-red-200">{{ form.errors.password }}</div>
                    </div>

                    <button :disabled="form.processing" type="submit" class="w-full py-3 font-medium text-white transition-colors bg-blue-600 rounded-md hover:bg-blue-700 disabled:opacity-50">
                        {{ form.processing ? 'Signing in...' : 'Sign In' }}
                    </button>
                </form>

                <p class="mt-6 text-sm text-center text-white">
                    Don't Have an Account?
                    <a :href="route('register')" class="text-blue-200 underline">Sign Up</a>
                </p>
            </div>
        </div>
    </div>
</template>
