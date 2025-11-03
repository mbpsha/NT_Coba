<script setup>
import { computed } from 'vue'
import F1 from '*/dashboard/keunggulan-alat-0.png'
import F2 from '*/dashboard/keunggulan-alat-1.png'
import F3 from '*/dashboard/keunggulan-alat-2.png'

const props = defineProps({ items: { type: Array, default: null } })

const defaults = [
  {
    title: 'Modular & Scalable',
    text: 'Stech Smart Garden dirancang dengan konsep modular dan scalable, sehingga mudah ditambahkan sensor (kelembaban tanah, suhu, intensitas cahaya, pH & nutrisi) dan langsung terintegrasi ke dashboard web. Data real-time memudahkan analisis, cocok untuk pertanian kecil, belajar IoT, maupun riset.',
    image: F1, alt: 'Modular sprinkler'
  },
  {
    title: 'Hemat Energi',
    text: 'Varian panel surya memungkinkan operasi mandiri tanpa bergantung listrik PLN. Lebih hemat biaya operasional, ramah lingkungan, dan ideal pada area minim pasokan listrik.',
    image: F2, alt: 'Panel surya'
  },
  {
    title: 'Open Source & Edukatif',
    text: 'Dilengkapi buku panduan perakitan dan source code yang terbuka untuk dipelajari, dimodifikasi, dan dikembangkan. Bermanfaat secara praktis sekaligus edukatif untuk pelajar/mahasiswa yang ingin mendalami IoT.',
    image: F3, alt: 'Buku panduan'
  }
]

const list = computed(() => props.items ?? defaults)

// === Uniform helpers (seragam semua) ===
function imageOnLeft(i) { return i % 2 === 0 }         // semua: gambar kiri
function isCircleImage() { return false }      // tidak ada gambar bulat
function showRowCenteredTitle() { return false } // tidak ada judul di tengah
</script>

<template>
  <section class="py-10">
    <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
      <div class="space-y-12">
        <div v-for="(f,i) in list" :key="i" class="grid items-center gap-8 lg:grid-cols-2">
          <!-- IMAGE (seragam) -->
          <div :class="[ imageOnLeft(i) ? 'order-1' : 'order-2' ]">
            <div class="w-full h-56 overflow-hidden border border-gray-100 shadow-md md:h-64 rounded-2xl">
              <img :src="f.image" :alt="f.alt" class="object-cover w-full h-full" />
            </div>
          </div>

          <!-- TEXT (seragam) -->
          <div :class="[ imageOnLeft() ? 'order-2' : 'order-1' ]"
               class="p-6 bg-white border border-gray-100 shadow-md rounded-2xl">
            <h3 class="text-xl font-semibold text-gray-900">{{ f.title }}</h3>
            <p class="mt-3 text-gray-700">{{ f.text }}</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
