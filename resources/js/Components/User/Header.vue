<script setup>
import { ref, computed } from 'vue'
import { Link, useForm, usePage } from '@inertiajs/vue3'
import Logo from '*/dashboard/logo-ngundur.png'

// Ambil user dari props Inertia (diset di HandleInertiaRequests)
const page = usePage()
const user = computed(() => page.props?.auth?.user ?? null)
const isAuthenticated = computed(() => !!user.value)

// Logout via Inertia
const logoutForm = useForm({})
function logout() {
  logoutForm.post(route('logout'))
}

const isRoute = (name) => route().current(name)                  // untuk route bernama
const isUrl   = (prefix) => page.url.startsWith(prefix)          // untuk path biasa
const linkClass = (active) => [
  'hover:text-green-700',
  active ? 'text-green-700 font-semibold' : 'text-gray-800'
]

const profileMenuOpen = ref(false)
function toggleProfileMenu(){ profileMenuOpen.value = !profileMenuOpen.value }
</script>

<template>
  <header class="fixed inset-x-0 top-0 z-50 shadow-sm bg-white/90 backdrop-blur">
    <nav class="flex items-center justify-between w-full h-16 px-3 sm:px-4 lg:px-6">
      <div class="flex items-center gap-2">
        <img :src="Logo" alt="NGUNDUR" class="h-12" />
      </div>

      <ul class="items-center hidden gap-6 text-sm md:flex">
        <li><Link :href="route('dashboard')" :class="linkClass(isRoute('dashboard'))">Home</Link></li>
        <li><Link :href="route('shop')" :class="linkClass(isUrl('/shop'))">Toko</Link></li>
        <li><Link :href="route('berita')" :class="linkClass(isUrl('/berita'))">Berita</Link></li>
        <li><Link :href="route('about')" :class="linkClass(isRoute('about'))">Tentang</Link></li>
        <li><Link :href="route('blog')" :class="linkClass(isUrl('/blog'))">Blog</Link></li>
      </ul>

      <div class="items-center hidden gap-3 sm:flex">
        <!-- Belum login -->
        <template v-if="!isAuthenticated">
          <Link :href="route('login')" class="px-4 py-2 text-white bg-green-500 rounded-full hover:bg-green-600">Masuk</Link>
          <Link :href="route('register')" class="px-4 py-2 text-green-700 bg-green-100 rounded-full hover:bg-green-200">Daftar</Link>
        </template>

        <!-- Sudah login -->
        <template v-else>
          <Link href="/cart" class="p-2 rounded-full hover:bg-gray-100" aria-label="Keranjang">
            <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
          </Link>

          <div class="relative" @keydown.escape="profileMenuOpen = false">
            <button @click="toggleProfileMenu"
              class="flex items-center justify-center w-10 h-10 text-green-700 bg-green-100 rounded-full hover:bg-green-200 ring-1 ring-green-300">
              <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
              </svg>


            </button>
            <div v-if="profileMenuOpen" class="absolute right-0 z-50 w-48 mt-2 overflow-hidden bg-white rounded-md shadow-lg ring-1 ring-black/5">
              <div class="px-4 py-3 text-sm">
                <p class="font-medium text-gray-900">Halo, {{ user.username ?? user.name }}</p>
                <p v-if="user.email" class="text-xs text-gray-500 truncate">{{ user.email }}</p>
              </div>
              <Link @click="profileMenuOpen=false" href="/profile" class="block px-4 py-2 text-sm hover:bg-gray-50">Profil Saya</Link>
              <button :disabled="logoutForm.processing" @click="logout" class="w-full px-4 py-2 text-sm text-left text-red-600 hover:bg-gray-50">
                Keluar
              </button>
            </div>
          </div>
        </template>
      </div>
    </nav>
  </header>
</template>
