<template>
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
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { RouterLink } from 'vue-router'
import NewsLeft from '../assets/berita-kiri.png'
import NewsMid from '../assets/berita-1.png'
import NewsRight from '../assets/berita-kanan.png'

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
</script>
