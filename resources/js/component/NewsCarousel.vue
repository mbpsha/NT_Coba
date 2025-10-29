<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { Link } from '@inertiajs/vue3'
import NewsLeft from '@/assets/berita-kiri.png'
import NewsMid from '@/assets/berita-1.png'
import NewsRight from '@/assets/berita-kanan.png'

const cards = [
    { title: 'Indonesia Dorong Pertanian Ramah Lingkungan dengan Teknologi IoT', image: NewsLeft, href: '/news/indonesia-dorong-pertanian-ramah-lingkungan-dengan-teknologi-iot' },
    { title: 'Petani Sayuran Mulai Terapkan Irigasi Otomatis untuk Hemat Air', image: NewsMid, href: '/news/petani-sayuran-mulai-terapkan-irigasi-otomatis-untuk-hemat-air' },
    { title: 'Tren Pertanian Urban: Berkebun di Lahan Sempit dengan Smart Garden', image: NewsRight, href: '/news/tren-pertanian-urban-berkebun-di-lahan-sempit-dengan-smart-garden' },
]

// … (fungsi slider tetap sama)
</script>

<template>
  <div class="relative h-[420px] md:h-[460px]">
    <!-- tombol prev/next -->
    <div class="relative h-full flex items-center justify-center select-none">
      <div class="relative w-full h-full [perspective:1200px]">
        <div v-for="(card,i) in cards" :key="card.title"
             class="absolute left-1/2 top-1/2 will-change-transform transition-transform duration-700 ease-out"
             :style="getCardStyle(i)">
          <Link :href="card.href" class="block w-[260px] sm:w-[300px] md:w-[340px] rounded-2xl overflow-hidden shadow-xl ring-1 ring-black/5 bg-white hover:shadow-2xl">
            <img :src="card.image" :alt="card.title" class="h-44 w-full object-cover">
            <div class="p-5"><h3 class="font-semibold text-[1.05rem] leading-snug">{{ card.title }}</h3></div>
          </Link>
        </div>
      </div>
      <!-- dots -->
    </div>
  </div>
</template>
