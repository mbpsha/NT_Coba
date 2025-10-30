<script setup>
import { computed } from 'vue'
import { usePage, useForm } from '@inertiajs/vue3'

const page = usePage()
const user = computed(() => page.props.auth.user)

const logoutForm = useForm({})

function logout() {
    logoutForm.post(route('logout'))
}

function goToLogin() {
    window.location.href = route('login')
}

function goToRegister() {
    window.location.href = route('register')
}

</script>

<template>
    <!-- NAVBAR -->
    <header class="fixed inset-x-0 top-0 z-50 shadow-sm bg-white/90 backdrop-blur">
        <nav class="flex items-center justify-between w-full h-16 px-0 sm:px-2 lg:px-4 ">
            <div class="flex items-center gap-2">
                <div class="flex justify-start">
                    <img src="/assets/dashboard/logo-ngundur.png" alt="NGUNDUR" class="h-20 w-50 ">
                </div>
            </div>
            <ul class="items-center hidden gap-6 text-sm md:flex">
                <li><a :href="route('dashboard')" class="font-bold text-green-700">Home</a></li>
                <li><a href="#" class="hover:text-green-700">Toko</a></li>
                <li><a href="#" class="hover:text-green-700">Berita</a></li>
                <li><a :href="route('about')" class="hover:text-green-700">Tentang</a></li>
                <li><a href="#" class="hover:text-green-700">Blog</a></li>
            </ul>
            <div class="items-center hidden gap-2 sm:flex">
                <div v-if="!user">
                    <button class="px-4 py-2 text-white bg-green-500 rounded-full hover:bg-green-600 font-inter" @click="goToLogin">Masuk</button>
                    <button class="px-4 py-2 text-green-700 bg-green-100 rounded-full hover:bg-green-200 font-inter" @click="goToRegister">Daftar</button>
                </div>
                <div v-else class="flex items-center gap-4">
                    <!-- Shopping Cart Icon -->
                    <svg class="w-6 h-6 text-gray-700 cursor-pointer hover:text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.6 8M7 13L5.4 5M7 13l2.25 2.25M16 16a2 2 0 104 0 2 2 0 00-4 0zM9 16a2 2 0 104 0 2 2 0 00-4 0z"></path>
                    </svg>

                    <!-- Profile Section -->
                    <div class="flex items-center gap-2">
                        <img src="/assets/dashboard/profil.png" alt="Profil" class="w-8 h-8 border border-green-700 rounded-full">
                        <span class="font-semibold text-green-700">{{ user.nama }}</span>
                        <button @click="logout" :disabled="logoutForm.processing" class="px-3 py-1 text-red-700 bg-red-100 rounded-full hover:bg-red-200 font-inter">
                            {{ logoutForm.processing ? 'Logging out...' : 'Logout' }}
                        </button>
                    </div>
                </div>
            </div>
        </nav>
    </header>
</template>
