<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { Link } from '@inertiajs/vue3'
import NewsLeft from '*/dashboard/berita-kiri.png'
import NewsMid from '*/dashboard/berita-1.png'
import NewsRight from '*/dashboard/berita-kanan.png'

const cards = [
  { title: 'Indonesia Dorong Pertanian Ramah Lingkungan dengan Teknologi IoT', image: NewsLeft, href: '#' },
  { title: 'Petani Sayuran Mulai Terapkan Irigasi Otomatis untuk Hemat Air', image: NewsMid, href: '#' },
  { title: 'Tren Pertanian Urban: Berkebun di Lahan Sempit dengan Smart Garden', image: NewsRight, href: '#' },
]

const active = ref(0)
let timer
function next() { active.value = (active.value + 1) % cards.length }
function prev() { active.value = (active.value - 1 + cards.length) % cards.length }
function getCardStyle(i) {
  const k = i - active.value
  const t = `translate3d(${k * 320}px, ${Math.abs(k) * -12}px, ${-Math.abs(k) * 120}px)`
  const r = `rotateY(${k * -12}deg)`
  const z = 10 - Math.abs(k)
  const o = Math.max(0, 1 - Math.abs(k) * 0.25)
  return { transform: `${t} ${r} translate(-50%, -50%)`, zIndex: z, opacity: o }
}
onMounted(() => { timer = setInterval(next, 4000) })
onBeforeUnmount(() => { clearInterval(timer) })
</script>

<template>
  <div class="relative h-[420px] md:h-[460px]">
    <button class="absolute z-20 px-3 py-2 -translate-y-1/2 rounded-full shadow left-2 top-1/2 bg-white/80" @click="prev">‹</button>
    <button class="absolute z-20 px-3 py-2 -translate-y-1/2 rounded-full shadow right-2 top-1/2 bg-white/80" @click="next">›</button>

    <div class="relative flex items-center justify-center h-full select-none">
      <div class="relative w-full h-full [perspective:1200px]">
        <div v-for="(card,i) in cards" :key="card.title"
              class="absolute transition-transform duration-700 ease-out left-1/2 top-1/2 will-change-transform"
              :style="getCardStyle(i)">
          <Link :href="card.href" class="block w-[280px] md:w-[320px]">
            <img :src="card.image" :alt="card.title" class="object-cover w-full h-48 shadow-lg md:h-56 rounded-xl" />
            <p class="px-2 mt-3 text-sm font-semibold text-center text-gray-800 md:text-base">{{ card.title }}</p>
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>
