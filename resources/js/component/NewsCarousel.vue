<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { Link } from '@inertiajs/vue3'
import NewsLeft from '*/dashboard/berita-kiri.png'
import NewsMid from '*/dashboard/berita-1.png'
import NewsRight from '*/dashboard/berita-kanan.png'
import NewsExtra from '*/dashboard/berita-4.jpg' 

const cards = [
  { title: 'Indonesia Dorong Pertanian Ramah Lingkungan dengan Teknologi IoT', image: NewsLeft, href: '#' },
  { title: 'Petani Sayuran Mulai Terapkan Irigasi Otomatis untuk Hemat Air', image: NewsMid, href: '#' },
  { title: 'Tren Pertanian Urban: Berkebun di Lahan Sempit dengan Smart Garden', image: NewsRight, href: '#' },
  { title: 'Petani Sayuran Mulai Terapkan Irigasi Otomatis untuk Hemat Air', image: NewsExtra, href: '#' },
]

const active = ref(0)
// Hapus rAF dan gunakan timer setInterval
let timer

function next() { active.value = (active.value + 1) % cards.length }
function prev() { active.value = (active.value - 1 + cards.length) % cards.length }

function getCardStyle(i) {
  const n = cards.length
  let k = i - active.value
  if (k > n / 2) k -= n
  if (k < -n / 2) k += n
  const t = `translate3d(${k * 320}px, ${Math.abs(k) * -12}px, ${-Math.abs(k) * 120}px)`
  const r = `rotateY(${k * -12}deg)`
  const z = 10 - Math.abs(k)
  const o = Math.max(0, 1 - Math.abs(k) * 0.25)
  return { transform: `${t} ${r} translate(-50%, -50%)`, zIndex: z, opacity: o }
}

onMounted(() => { timer = setInterval(next, 3000) })
onBeforeUnmount(() => { clearInterval(timer) })
</script>

<template>
  <div class="relative h-[420px] md:h-[460px]">
    <button class="absolute left-2 top-1/2 -translate-y-1/2 z-20 px-3 py-2 bg-white/80 rounded-full shadow" @click="prev">‹</button>
    <button class="absolute right-2 top-1/2 -translate-y-1/2 z-20 px-3 py-2 bg-white/80 rounded-full shadow" @click="next">›</button>

    <div class="relative h-full flex items-center justify-center select-none">
      <div class="relative w-full h-full [perspective:1200px]">
        <div v-for="(card,i) in cards" :key="i"
             class="absolute left-1/2 top-1/2 will-change-transform transition-transform duration-700 ease-out"
             :style="getCardStyle(i)">
          <Link :href="card.href" class="block w-[280px] md:w-[320px]">
            <img :src="card.image" :alt="card.title" class="w-full h-48 md:h-56 object-cover rounded-xl shadow-lg" />
            <p class="mt-3 text-sm md:text-base font-semibold text-gray-800 text-center px-2">{{ card.title }}</p>
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>
