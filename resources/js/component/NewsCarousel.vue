<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import DefaultImg from '*/dashboard/berita-1.png'

const props = defineProps({
  news: { type: [Object, Array], required: false }
})

const page = usePage()

const newsItem = computed(() => {
  const n = props.news || page.props?.latestNews || []
  const arr = Array.isArray(n) ? n : (n?.data ?? n ?? [])
  return arr.slice(0, 5)
})

const cards = computed(() => {
  return newsItem.value.map(item => ({
    title: item.title ?? item.nama ?? 'Berita',
    image: item.image ? `/storage/${item.image}` : DefaultImg,
    href: (typeof route === 'function') ? route('berita.show', item.id ?? item.id_news ?? item.id) : (`/berita/${item.id ?? item.id_news ?? item.id}`)
  }))
})

const active = ref(0)
let timer

function next() { active.value = (active.value + 1) % cards.value.length }
function prev() { active.value = (active.value - 1 + cards.value.length) % cards.value.length }

function getCardStyle(i) {
  const n = cards.value.length || 1
  let k = i - active.value
  if (k > n / 2) k -= n
  if (k < -n / 2) k += n
  const t = `translate3d(${k * 320}px, ${Math.abs(k) * -12}px, ${-Math.abs(k) * 120}px)`
  const r = `rotateY(${k * 12}deg)`
  const z = 10 - Math.abs(k)
  const o = Math.max(0, 1 - Math.abs(k) * 0.25)
  return {transform: `${t} ${r} translate(-50%, -50%)`, zIndex: z, opacity: o}
}

onMounted(() => {
  if (cards.value.length > 1) timer = setInterval(next, 10000)
})
onBeforeUnmount(() => {
  if (timer) clearInterval(timer)
})
</script>

<template>
  <div class="relative h-[420px] md:h-[460px]" v-if="cards.length">
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

  <div v-else class="text-center text-gray-500">Belum ada berita</div>
</template>