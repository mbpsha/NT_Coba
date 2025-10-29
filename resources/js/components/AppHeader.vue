<template>
  <!-- Header/Navbar -->
  <header class="fixed inset-x-0 top-0 z-50 bg-white/90 backdrop-blur shadow-sm">
    <nav class="w-full px-0 sm:px-2 lg:px-4 h-16 flex items-center justify-between">
      <!-- Logo links to Home -->
      <RouterLink to="/" class="flex items-center gap-2">
        <img :src="Logo" alt="NGUNDUR" class="h-14" />
      </RouterLink>

      <!-- Desktop nav links (placeholder) -->
      <ul class="hidden md:flex items-center gap-6 text-sm">
        <li><RouterLink to="/" class="font-bold text-green-700">Home</RouterLink></li>
  <li><RouterLink to="/toko" class="hover:text-green-700">Toko</RouterLink></li>
        <li><a href="#" class="hover:text-green-700">Berita</a></li>
        <li><a href="#" class="hover:text-green-700">Tentang</a></li>
        <li><a href="#" class="hover:text-green-700">Blog</a></li>
        <li v-if="hasRole('admin')"><RouterLink to="/admin" class="text-green-700 hover:underline">Admin</RouterLink></li>
      </ul>

      <!-- Auth / Profile -->
      <div class="hidden sm:flex items-center gap-3" @keydown.escape="profileMenuOpen = false">
        <template v-if="!isAuthenticated">
          <RouterLink to="/login" class="px-4 py-2 rounded-full text-white bg-green-500 hover:bg-green-600 font-inter">Masuk</RouterLink>
          <RouterLink to="/register" class="px-4 py-2 rounded-full bg-green-100 text-green-700 hover:bg-green-200 font-inter">Daftar</RouterLink>
        </template>
        <template v-else>
          <!-- Cart icon -->
          <RouterLink to="/cart" class="p-2 rounded-full hover:bg-gray-100" aria-label="Keranjang">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7 text-gray-800">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25h9.75m-12.75-9h15.213c.74 0 1.253.71 1.07 1.426l-1.5 6A1.125 1.125 0 0118.213 13.5H8.116a1.125 1.125 0 01-1.087-.835L5.106 5.272M7.5 19.5a.75.75 0 100-1.5.75.75 0 000 1.5zm9.75 0a.75.75 0 100-1.5.75.75 0 000 1.5z" />
            </svg>
          </RouterLink>

          <!-- Profile dropdown -->
          <div class="relative">
            <button @click="toggleProfileMenu" class="h-10 w-10 rounded-full flex items-center justify-center bg-green-100 text-green-700 hover:bg-green-200 ring-1 ring-green-300">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6">
                <path fill-rule="evenodd" d="M12 2a5 5 0 100 10 5 5 0 000-10zm-7 17a7 7 0 1114 0v1a1 1 0 01-1 1H6a1 1 0 01-1-1v-1z" clip-rule="evenodd" />
              </svg>
            </button>
            <div v-if="profileMenuOpen" class="absolute right-0 mt-2 w-48 rounded-md bg-white shadow-lg ring-1 ring-black/5 overflow-hidden z-50">
              <div class="px-4 py-3 text-sm">
                <p class="font-medium text-gray-900">Halo, {{ user.username || 'Pengguna' }}</p>
                <p v-if="user.email" class="text-gray-500 text-xs truncate">{{ user.email }}</p>
              </div>
              <RouterLink @click="profileMenuOpen=false" to="/profile" class="block px-4 py-2 text-sm hover:bg-gray-50">Profil Saya</RouterLink>
              <button @click="logout" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-50">Keluar</button>
            </div>
          </div>
        </template>
      </div>
    </nav>
  </header>
</template>

<script setup>
import { ref } from 'vue'
import { RouterLink } from 'vue-router'
import { useAuth } from '../composables/useAuth'
import Logo from '../assets/logo-ngundur.png'

const profileMenuOpen = ref(false)
const { isAuthenticated, user, logout, hasRole } = useAuth()
function toggleProfileMenu() { profileMenuOpen.value = !profileMenuOpen.value }
</script>
