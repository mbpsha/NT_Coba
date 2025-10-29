<template>
  <div class="font-inter text-gray-900">
    <!-- Header component (navbar) -->
    <AppHeader />

    <!-- Hero section -->
    <section class="relative flex justify-center">
      <div class="relative w-[1250px] h-[800px] overflow-hidden rounded-2x1">
        <img :src="BgHero" alt="Leaves" class="absolute inset-0 w-full h-full object-cover" />
        <div class="relative z-10 px-8 sm:px-40 lg:px-14 top-1/4">
          <h1 class="text-white text-5x1 sm:text-8xl font-Urbanist leading-tight">
            Stech Smart<br />Garden
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

    <!-- News carousel -->
    <section class="py-12">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative h-[420px] md:h-[460px]">
          <button @click="prevSlide" aria-label="Sebelumnya" class="hidden md:flex absolute left-0 top-1/2 -translate-y-1/2 h-12 w-12 rounded-full bg-white/90 shadow items-center justify-center text-gray-700 hover:bg-white z-20">‹</button>
          <button @click="nextSlide" aria-label="Selanjutnya" class="hidden md:flex absolute right-0 top-1/2 -translate-y-1/2 h-12 w-12 rounded-full bg-white/90 shadow items-center justify-center text-gray-700 hover:bg-white z-20">›</button>

          <div class="relative h-full flex items-center justify-center select-none">
            <div class="relative w-full h-full [perspective:1200px]">
              <div v-for="(card,i) in cards" :key="card.title" class="absolute left-1/2 top-1/2 will-change-transform transition-transform duration-700 ease-out" :style="getCardStyle(i)">
                <RouterLink :to="toNews(card)" class="block w-[260px] sm:w-[300px] md:w-[340px] rounded-2xl overflow-hidden shadow-xl ring-1 ring-black/5 bg-white hover:shadow-2xl">
                  <img :src="card.image" :alt="card.title" class="h-44 w-full object-cover" />
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

    <!-- Title chip: Keunggulan Alat -->
    <section class="mt-4">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="inline-block bg-green-100 text-green-800 px-5 py-2 rounded-xl shadow-sm border border-green-200">
          <span class="font-semibold">Keunggulan Alat</span>
        </div>
      </div>
    </section>

    <!-- Features section -->
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

    <!-- Image band highlights -->
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

    <!-- Footer component -->
    <AppFooter />

    <!-- No modals: pages handle auth routes -->
  </div>
</template>


<script setup>
// Core
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { RouterLink } from 'vue-router'

// Layout components
import AppHeader from '../components/AppHeader.vue'
import AppFooter from '../components/AppFooter.vue'

// Assets used in this page
import BgHero from '../assets/bg-depan01.png'
import NewsLeft from '../assets/berita-kiri.png'
import NewsMid from '../assets/berita-1.png'
import NewsRight from '../assets/berita-kanan.png'
import Band3 from '../assets/foto-bawah-3.png'
import Profil from '../assets/profil.png'
import F1 from '../assets/keunggulan-alat-0.jpg'
import F2 from '../assets/keunggulan-alat-1.png'
import F3 from '../assets/keunggulan-alat-2.png'

// News carousel state & helpers
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

// Features content & helpers (uniform rectangular images)
const features = [
  {
    title: 'Modular & Scalable',
    text: 'Stech Smart Garden dirancang dengan konsep modular dan scalable, sehingga mudah ditambahkan sensor atau fitur lain sesuai kebutuhan pengguna. Selain sensor kelembaban tanah yang sudah menjadi inti sistem, pengguna dapat menambahkan sensor suhu, kelembaban udara, intensitas cahaya, hingga pH dan nutrisi tanah untuk pemantauan yang lebih komprehensif. Seluruh sensor ini dapat diintegrasikan dengan dashboard web yang sudah tersedia, sehingga data tambahan langsung bisa divisualisasikan secara real-time.',
    image: F1,
    alt: 'Modular sprinkler'
  },
  {
    title: 'Hemat Energi',
    text: 'Varian Stech Smart Garden dengan panel surya mendukung operasi mandiri karena memanfaatkan energi matahari sebagai sumber daya utama. Dengan sistem ini, perangkat dapat berfungsi secara berkelanjutan tanpa bergantung pada listrik PLN, sehingga sangat cocok digunakan di daerah yang minim pasokan listrik atau di lahan terbuka yang jauh dari jaringan listrik. Selain lebih hemat biaya operasional, penggunaan panel surya juga menjadikan sistem ini ramah lingkungan karena mengurangi jejak karbon dan memaksimalkan pemanfaatan energi terbarukan.',
    image: F2,
    alt: 'Panel surya'
  },
  {
    title: 'Open Source & Edukatif',
    text: 'Stech Smart Garden dilengkapi dengan buku panduan perakitan dan source code yang dapat digunakan sebagai media pembelajaran IoT. Dengan adanya panduan ini, pengguna tidak hanya mendapatkan produk siap pakai, tetapi juga bisa memahami alur kerja sistem mulai dari instalasi komponen, integrasi sensor, hingga pengelolaan data pada dashboard. Source code yang disertakan bersifat terbuka sehingga dapat dipelajari, dimodifikasi, dan dikembangkan lebih lanjut sesuai kebutuhan, menjadikan produk ini tidak hanya bermanfaat secara praktis, tetapi juga edukatif bagi pelajar, mahasiswa, maupun siapa saja yang ingin mendalami teknologi IoT di bidang pertanian.',
    image: F3,
    alt: 'Buku panduan'
  }
]
function imageOnLeft(i){ return i % 2 === 0 }
</script>

<style scoped>
.rounded-2x1 { border-radius: 1rem; }
</style>
