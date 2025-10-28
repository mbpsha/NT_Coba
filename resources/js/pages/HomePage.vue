<template>
  <div class="font-inter text-gray-900">
    <!-- NAVBAR -->
    <header class="fixed inset-x-0 top-0 z-50 bg-white/90 backdrop-blur shadow-sm">
      <nav class="w-full px-0 sm:px-2 lg:px-4 h-16 flex items-center justify-between "> <!--</nav>max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">-->
        <div class="flex items-center gap-2">
          <div class="flex justify-start">
            <img :src="Logo" alt="NGUNDUR" class="h-14">
          </div>
        </div>
    

        <ul class="hidden md:flex items-center gap-6 text-sm">
          <li><a href="#" class="font-bold text-green-700">Home</a></li>
          <li><a href="#" class="hover:text-green-700">Toko</a></li>
          <li><a href="#" class="hover:text-green-700">Berita</a></li>
          <li><a href="#" class="hover:text-green-700">Tentang</a></li>
          <li><a href="#" class="hover:text-green-700">Blog</a></li>
        </ul>

        <div class="hidden sm:flex items-center gap-3">
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

            <div class="relative" @keydown.escape="profileMenuOpen = false">
              <button @click="toggleProfileMenu"
                class="h-10 w-10 rounded-full flex items-center justify-center bg-green-100 text-green-700 hover:bg-green-200 ring-1 ring-green-300">
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

    <HeroSection />

    <section class="py-12">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <NewsCarousel />
      </div>
    </section>

    <!-- TITLE CHIP -->
    <section class="mt-4">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="inline-block bg-green-100 text-green-800 px-5 py-2 rounded-xl shadow-sm border border-green-200">
          <span class="font-semibold">Keunggulan Alat</span>
        </div>
      </div>
    </section>

    <FeaturesSection />

    <section class="py-10">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 grid md:grid-cols-3 gap-4">
        <div class="relative rounded-xl overflow-hidden">
          <img :src="Band3" alt="" class="w-full h-48 md:h-56 object-cover">
          <div class="absolute inset-0 bg-black/35"></div>
          <div class="absolute inset-0 flex items-center justify-center">
            <span class="text-white text-2xl font-bold drop-shadow">Monitoring<br class="md:hidden"> Real time</span>
          </div>
        </div>
        <div class="relative rounded-xl overflow-hidden">
          <img :src="Band3" alt="" class="w-full h-48 md:h-56 object-cover">
          <div class="absolute inset-0 bg-black/35"></div>
          <div class="absolute inset-0 flex items-center justify-center">
            <span class="text-white text-2xl font-bold drop-shadow">Irigasi<br class="md:hidden"> Otomatis</span>
          </div>
        </div>
        <div class="relative rounded-xl overflow-hidden">
          <img :src="Profil" alt="" class="w-full h-48 md:h-56 object-cover">
          <div class="absolute inset-0 bg-black/35"></div>
          <div class="absolute inset-0 flex items-center justify-center">
            <span class="text-white text-2xl font-bold drop-shadow">Dasboard<br class="md:hidden"> Web</span>
          </div>
        </div>
      </div>
    </section>

    <!-- FOOTER -->
    <footer class="relative mt-10">
      <div class="relative bg-gradient-to-r from-green-800 to-green-700 text-white">
        <div class="absolute inset-y-0 right-0 w-1/3 opacity-30 hidden md:block">
          <img :src="FooterBg" alt="" class="w-full h-full object-cover">
        </div>

        <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 grid md:grid-cols-2 gap-8">
          <div>
            <h4 class="text-xl font-semibold">NGUNDUR</h4>
            <p class="mt-3 text-sm leading-7 text-white/90">
              Jl. Ringroad Barat, Dowangan, Banyuraden,<br>
              Gamping, Sleman, Daerah Istimewa Yogyakarta
            </p>
            <div class="mt-4 space-y-1 text-sm text-white/90">
              <p><span class="font-semibold">Phone</span> +62 000-000-000</p>
              <p><span class="font-semibold">WA</span> +62 000-000-000</p>
              <p><span class="font-semibold">Hours</span> Senin–Jum'at 09.00–16.00</p>
            </div>
            <div class="mt-6 h-px bg-white/30 max-w-sm"></div>
            <p class="mt-4 text-xs text-white/70">© 2025 NGUNDUR</p>
          </div>

          <div class="flex items-center justify-end">
            
          </div>
        </div>
      </div>
    </footer>

    <!-- No modals: pages handle auth routes -->
  </div>
</template>


<script setup>
import { ref } from 'vue'
import { RouterLink } from 'vue-router'
import { useAuth } from '../composables/useAuth'
import Logo from '../assets/logo-ngundur.png'
import Band3 from '../assets/foto-bawah-3.png'
import Profil from '../assets/profil.png'
import FooterBg from '../assets/footer-logo.png'
import HeroSection from '../components/HeroSection.vue'
import NewsCarousel from '../components/NewsCarousel.vue'
import FeaturesSection from '../components/FeaturesSection.vue'
import FooterSection from '../components/FooterSection.vue'

const profileMenuOpen = ref(false)
const { isAuthenticated, user, logout } = useAuth()
function toggleProfileMenu() { profileMenuOpen.value = !profileMenuOpen.value }
</script>
