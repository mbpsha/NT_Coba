<template>
  <div class="font-inter text-gray-900">
    <!-- NAVBAR -->
    <header class="fixed inset-x-0 top-0 z-50 bg-white/90 backdrop-blur shadow-sm">
      <nav class="w-full px-0 sm:px-2 lg:px-4 h-16 flex items-center justify-between ">
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
            <RouterLink to="/cart" class="p-2 rounded-full hover:bg-gray-100" aria-label="Keranjang">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7 text-gray-800">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25h9.75m-12.75-9h15.213c.74 0 1.253.71 1.07 1.426l-1.5 6A1.125 1.125 0 0118.213 13.5H8.116a1.125 1.125 0 01-1.087-.835L5.106 5.272M7.5 19.5a.75.75 0 100-1.5.75.75 0 000 1.5zm9.75 0a.75.75 0 100-1.5.75.75 0 000 1.5z" />
              </svg>
            </RouterLink>
            <div class="relative" @keydown.escape="profileMenuOpen = false">
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

    <!-- HERO SECTION -->
    <section class="relative flex justify-center mt-16">
      <div class="relative w-[1250px] h-[800px] overflow-hidden rounded-2x1">
        <img :src="BgHero" alt="Leaves" class="absolute inset-0 w-full h-full object-cover">
        <div class="relative z-10 px-8 sm:px-40 lg:px-14 top-1/4">
          <h1 class="text-white text-5x1 sm:text-8xl font-Urbanist leading-tight">
            Stech Smart<br>Garden
          </h1>
          <p class="mt-4 text-white/90 font-inter max-w-xl">
            Stech Smart Garden adalah sistem irigasi otomatis berbasis IoT skala kecil yang dirancang untuk membantu petani dan masyarakat yang hobi berkebun dalam mengelola kebutuhan air secara efisien.
          </p>
          <div class="mt-6 flex flex-wrap gap-3">
            <a href="#" class="px-5 py-2 rounded-lg bg-white hover:bg-green-100 text-green-600 font-inter transition shadow">Beli Sekarang</a>
            <a href="#" class="px-5 py-2 rounded-lg border border-white/80 text-white hover:bg-white hover:text-green-700 font-inter transition">Baca Lengkap</a>
          </div>
        </div>
      </div>
    </section>

    <!-- NEWS CAROUSEL -->
    <section class="py-12">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative h-[420px] md:h-[460px]">
          <button @click="prevSlide" aria-label="Sebelumnya" class="hidden md:flex absolute left-0 top-1/2 -translate-y-1/2 h-12 w-12 rounded-full bg-white/90 shadow items-center justify-center text-gray-700 hover:bg-white z-20">‹</button>
          <button @click="nextSlide" aria-label="Selanjutnya" class="hidden md:flex absolute right-0 top-1/2 -translate-y-1/2 h-12 w-12 rounded-full bg-white/90 shadow items-center justify-center text-gray-700 hover:bg-white z-20">›</button>

          <div class="relative h-full flex items-center justify-center select-none">
            <div class="relative w-full h-full [perspective:1200px]">
              <div v-for="(card,i) in cards" :key="card.title" class="absolute left-1/2 top-1/2 will-change-transform transition-transform duration-700 ease-out" :style="getCardStyle(i)">
                <RouterLink :to="toNews(card)" class="block w-[260px] sm:w-[300px] md:w-[340px] rounded-2xl overflow-hidden shadow-xl ring-1 ring-black/5 bg-white hover:shadow-2xl">
                  <img :src="card.image" :alt="card.title" class="h-44 w-full object-cover">
                  <div class="p-5"><h3 class="font-semibold text-[1.15rem] leading-snug">{{ card.title }}</h3></div>
                </RouterLink>
              </div>
            </div>

            <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex items-center gap-2 z-20">
              <button v-for="(_,i) in cards" :key="i" @click="goTo(i)" class="h-2.5 rounded-full transition-all duration-300" :class="i===center ? 'w-6 bg-green-600' : 'w-2.5 bg-gray-300 hover:bg-gray-400'" />
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="mt-4">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="inline-block bg-green-100 text-green-800 px-5 py-2 rounded-xl shadow-sm border border-green-200">
          <span class="font-semibold">Keunggulan Alat</span>
        </div>
      </div>
    </section>

    <!-- FEATURES: KEUNGGULAN ALAT -->
    <section class="py-10">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="space-y-12">
          <div v-for="(f,i) in features" :key="i" class="grid lg:grid-cols-2 gap-8 items-center">
            <div :class="[ imageOnLeft(i) ? 'order-1' : 'order-2', 'flex', imageOnLeft(i) ? 'lg:justify-start' : 'lg:justify-end', 'justify-center' ]">
              <img :src="f.image" :alt="f.alt" class="rounded-2xl shadow-md border border-gray-100 w-full object-cover" />
            </div>
            <div :class="[ imageOnLeft(i) ? 'order-2' : 'order-1', 'bg-white rounded-2xl shadow-md border border-gray-100 p-6' ]">
              <h3 class="text-xl font-semibold text-gray-900">{{ f.title }}</h3>
              <p class="mt-3 text-gray-700">{{ f.text }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

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
            <p class="mt-3 text-sm leading-7 text-white/90">Jl. Ringroad Barat, Dowangan, Banyuraden,<br>Gamping, Sleman, Daerah Istimewa Yogyakarta</p>
            <div class="mt-4 space-y-1 text-sm text-white/90">
              <p><span class="font-semibold">Phone</span> +62 000-000-000</p>
              <p><span class="font-semibold">WA</span> +62 000-000-000</p>
              <p><span class="font-semibold">Hours</span> Senin–Jum'at 09.00–16.00</p>
            </div>
            <div class="mt-6 h-px bg-white/30 max-w-sm"></div>
            <p class="mt-4 text-xs text-white/70">© 2025 NGUNDUR</p>
          </div>
          <div class="flex items-center justify-end"></div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { RouterLink } from 'vue-router'
import { useAuth } from '../../composables/useAuth'
import Logo from '../../assets/logo-ngundur.png'
import Band3 from '../../assets/foto-bawah-3.png'
import Profil from '../../assets/profil.png'
import FooterBg from '../../assets/footer-logo.png'
import BgHero from '../../assets/bg-depan01.png'
import NewsLeft from '../../assets/berita-kiri.png'
import NewsMid from '../../assets/berita-1.png'
import NewsRight from '../../assets/berita-kanan.png'
import F1 from '../../assets/keunggulan-alat-0.jpg'
import F2 from '../../assets/keunggulan-alat-1.png'
import F3 from '../../assets/keunggulan-alat-2.png'

const profileMenuOpen = ref(false)
const { isAuthenticated, user, logout } = useAuth()
function toggleProfileMenu() { profileMenuOpen.value = !profileMenuOpen.value }

// NEWS CAROUSEL state & helpers
const cards = [
  { title: 'Indonesia Dorong Pertanian Ramah Lingkungan dengan Teknologi IoT', image: NewsLeft },
  { title: 'Petani Sayuran Mulai Terapkan Irigasi Otomatis untuk Hemat Air', image: NewsMid },
  { title: 'Tren Pertanian Urban: Berkebun di Lahan Sempit dengan Smart Garden', image: NewsRight },
]
const center = ref(0)
function mod(n,m){ return ((n % m) + m) % m }
function getCardOffset(i){ const total = cards.length; let d = i - center.value; if (d > total/2) d -= total; if (d < -total/2) d += total; return d }
function getCardStyle(i){ const d = getCardOffset(i); const baseX = 220; const x = d * baseX; const z = -Math.abs(d) * 120; const rot = d * -10; const scale = d === 0 ? 1 : 0.9; return { transform: `translate3d(calc(-50% + ${x}px), calc(-50%), ${z}px) rotateY(${rot}deg) scale(${scale})`, zIndex: String(100 - Math.abs(d)) } }
function nextSlide(){ center.value = mod(center.value + 1, cards.length) }
function prevSlide(){ center.value = mod(center.value - 1, cards.length) }
function goTo(i){ center.value = mod(i, cards.length) }
function toNews(card){ const slug = card.title.toLowerCase().replace(/[^a-z0-9\s-]/g,'').trim().replace(/\s+/g,'-'); return `/news/${slug}` }
let timer
onMounted(()=>{ timer = setInterval(()=> nextSlide(), 3500) })
onBeforeUnmount(()=> clearInterval(timer))

// FEATURES data & helpers
const features = [
  { title: 'Modular & Scalable', text: 'Stech Smart Garden dirancang dengan konsep modular dan scalable, sehingga mudah ditambahkan sensor atau fitur lain sesuai kebutuhan pengguna. Selain sensor kelembaban tanah yang sudah menjadi inti sistem, pengguna dapat menambahkan sensor suhu, kelembaban udara, intensitas cahaya, hingga pH dan nutrisi tanah untuk pemantauan yang lebih komprehensif. Seluruh sensor ini dapat diintegrasikan dengan dashboard web yang sudah tersedia, sehingga data tambahan langsung bisa divisualisasikan secara real-time.', image: F1, alt: 'Modular sprinkler' },
  { title: 'Hemat Energi', text: 'Varian Stech Smart Garden dengan panel surya mendukung operasi mandiri karena memanfaatkan energi matahari sebagai sumber daya utama. Dengan sistem ini, perangkat dapat berfungsi secara berkelanjutan tanpa bergantung pada listrik PLN, sehingga sangat cocok digunakan di daerah yang minim pasokan listrik atau di lahan terbuka yang jauh dari jaringan listrik. Selain lebih hemat biaya operasional, penggunaan panel surya juga menjadikan sistem ini ramah lingkungan karena mengurangi jejak karbon dan memaksimalkan pemanfaatan energi terbarukan.', image: F2, alt: 'Panel surya' },
  { title: 'Open Source & Edukatif', text: 'Stech Smart Garden dilengkapi dengan buku panduan perakitan dan source code yang dapat digunakan sebagai media pembelajaran IoT. Dengan adanya panduan ini, pengguna tidak hanya mendapatkan produk siap pakai, tetapi juga bisa memahami alur kerja sistem mulai dari instalasi komponen, integrasi sensor, hingga pengelolaan data pada dashboard. Source code yang disertakan bersifat terbuka sehingga dapat dipelajari, dimodifikasi, dan dikembangkan lebih lanjut sesuai kebutuhan, menjadikan produk ini tidak hanya bermanfaat secara praktis, tetapi juga edukatif bagi pelajar, mahasiswa, maupun siapa saja yang ingin mendalami teknologi IoT di bidang pertanian.', image: F3, alt: 'Buku panduan' },
]
function imageOnLeft(i){ return i % 2 === 0 }
</script>

<style scoped>
.rounded-2x1 { border-radius: 1rem; }
</style>
