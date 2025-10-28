<template>
  <section class="py-10">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="space-y-12">
        <div v-for="(f,i) in features" :key="i" class="grid lg:grid-cols-2 gap-8 items-center">
          <div v-if="showRowCenteredTitle(i)" class="lg:col-span-2 text-center">
            <h3 class="text-2xl font-semibold text-gray-900">{{ f.title }}</h3>
          </div>

          <div :class="[ imageOnLeft(i) ? 'order-1' : 'order-2', 'flex', imageOnLeft(i) ? 'lg:justify-start' : 'lg:justify-end', 'justify-center' ]">
            <template v-if="isCircleImage(i)">
              <div class="w-56 h-56 md:w-64 md:h-64 rounded-full bg-white shadow-lg ring-1 ring-green-100 overflow-hidden flex items-center justify-center">
                <img :src="f.image" :alt="f.alt" class="max-w-[80%] max-h-[80%] object-contain" />
              </div>
            </template>
            <template v-else>
              <img :src="f.image" :alt="f.alt" class="rounded-2xl shadow-md border border-gray-100 w-full object-cover" />
            </template>
          </div>

          <div :class="[ imageOnLeft(i) ? 'order-2' : 'order-1', 'bg-white rounded-2xl shadow-md border border-gray-100 p-6' ]">
            <h3 v-if="!showRowCenteredTitle(i)" class="text-xl font-semibold text-gray-900">{{ f.title }}</h3>
            <p class="mt-3 text-gray-700">{{ f.text }}</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import F1 from '@/assets/keunggulan-alat-1.png'
import F2 from '../assets/keunggulan-alat-1.png'
import F3 from '../assets/keunggulan-alat-2.png'
const features = [
  {
    title: 'Modular & Scalable',
    text: 'Stech Smart Garden dirancang dengan konsep modular dan scalable, sehingga mudah ditambahkan sensor atau fitur lain sesuai kebutuhan pengguna. Selain sensor kelembaban tanah yang sudah menjadi inti sistem, pengguna dapat menambahkan sensor suhu, kelembaban udara, intensitas cahaya, hingga pH dan nutrisi tanah untuk pemantauan yang lebih komprehensif. Seluruh sensor ini dapat diintegrasikan dengan dashboard web yang sudah tersedia, sehingga data tambahan langsung bisa divisualisasikan secara real-time. Fleksibilitas ini membuat Stech Smart Garden tidak hanya berfungsi sebagai sistem irigasi otomatis, tetapi juga dapat berkembang menjadi solusi monitoring lingkungan tanaman yang lebih lengkap, baik untuk keperluan pertanian kecil, pembelajaran IoT, maupun penelitian, tanpa harus merombak perangkat utama yang sudah ada. Smart Garden dirancang modular dan scalable: mudah menambah sensor (kelembaban, suhu, intensitas cahaya, pH & nutrisi) dan integrasi ke dashboard web. Data tampil secara real-time sehingga mudah dianalisis dan divisualisasikan. Cocok untuk pertanian kecil, belajar IoT, atau riset.',
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
function isCircleImage(i){ return i === 0 }
function showRowCenteredTitle(i){ return i === 1 }
</script>
