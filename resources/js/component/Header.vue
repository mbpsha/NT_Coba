<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import Logo from '@/assets/logo-ngundur.png'

const isAuthenticated = ref(false)
const user = ref({ username: 'Pengguna', email: '' })
function logout(){ isAuthenticated.value = false }
const profileMenuOpen = ref(false)
function toggleProfileMenu(){ profileMenuOpen.value = !profileMenuOpen.value }
</script>

<template>
  <header class="fixed inset-x-0 top-0 z-50 bg-white/90 backdrop-blur shadow-sm">
    <nav class="w-full h-16 flex items-center justify-between px-3 sm:px-4 lg:px-6">
      <div class="flex items-center gap-2">
        <img :src="Logo" alt="NGUNDUR" class="h-12" />
      </div>

      <ul class="hidden md:flex items-center gap-6 text-sm">
        <li><Link href="/" class="font-bold text-green-700">Home</Link></li>
        <li><Link href="/shop" class="hover:text-green-700">Toko</Link></li>
        <li><Link href="/news" class="hover:text-green-700">Berita</Link></li>
        <li><Link href="/about" class="hover:text-green-700">Tentang</Link></li>
        <li><Link href="/blog" class="hover:text-green-700">Blog</Link></li>
      </ul>

      <div class="hidden sm:flex items-center gap-3">
        <template v-if="!isAuthenticated">
          <Link href="/login" class="px-4 py-2 rounded-full text-white bg-green-500 hover:bg-green-600">Masuk</Link>
          <Link href="/register" class="px-4 py-2 rounded-full bg-green-100 text-green-700 hover:bg-green-200">Daftar</Link>
        </template>
        <template v-else>
          <Link href="/cart" class="p-2 rounded-full hover:bg-gray-100" aria-label="Keranjang">
            <!-- svg ikon -->
          </Link>

          <div class="relative" @keydown.escape="profileMenuOpen = false">
            <button @click="toggleProfileMenu"
              class="h-10 w-10 rounded-full flex items-center justify-center bg-green-100 text-green-700 hover:bg-green-200 ring-1 ring-green-300">
              <!-- svg avatar -->
            </button>
            <div v-if="profileMenuOpen" class="absolute right-0 mt-2 w-48 rounded-md bg-white shadow-lg ring-1 ring-black/5 overflow-hidden z-50">
              <div class="px-4 py-3 text-sm">
                <p class="font-medium text-gray-900">Halo, {{ user.username || 'Pengguna' }}</p>
                <p v-if="user.email" class="text-gray-500 text-xs truncate">{{ user.email }}</p>
              </div>
              <Link @click="profileMenuOpen=false" href="/profile" class="block px-4 py-2 text-sm hover:bg-gray-50">Profil Saya</Link>
              <button @click="logout" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-50">Keluar</button>
            </div>
          </div>
        </template>
      </div>
    </nav>
  </header>
</template>
