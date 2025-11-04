<script setup>
import Header from '@/component/Header.vue'     // per file yang Anda punya: header.vue (lowercase)
import Footer from '@/component/Footer.vue'

import { computed } from 'vue'
import Logo from '*/dashboard/logo-ngundur.png'
import Profil from '*/dashboard/profil.png'

// hero background with a soft gray overlay using public asset
const heroStyle = computed(() => ({
  backgroundImage: `linear-gradient(to bottom, rgba(128,128,128,0.65), rgba(128,128,128,0.65)), url('/assets/dashboard/bg-profil.jpg')`,
  backgroundSize: 'cover',
  backgroundPosition: 'center',
}))
</script>

<template>
  <div class="font-inter text-gray-900">
    <Header />

    <!-- HERO -->
    <section
      class="mt-16 h-[260px] md:h-[320px] relative flex items-center"
      :style="heroStyle"
    >
      <!-- jika belum punya gambar, cukup hapus url(...) di atas, gradient tetap tampil -->
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl md:text-5xl font-extrabold text-white drop-shadow">Profil</h1>
      </div>
    </section>

    <!-- TENTANG -->
    <section class="py-10">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="inline-block text-3xl md:text-4xl font-extrabold text-green-700 bg-green-100 rounded-full px-5 py-2">
          Tentang NGUNDUR
        </h2>

        <p class="mt-4 text-sm text-gray-600">~IoT : Stech Smart Garden</p>

        <h1 class="text-2xl md:text-4xl font-semibold text-green-700 px-5 py-2">
          NGUNDUR
        </h1>

        <div class="mt-6 grid md:grid-cols-3 gap-10">
          <div class="md:col-span-2 space-y-4 leading-7 text-gray-700">
            <p>
              Ngundur Nguthik Tandur adalah sebuah inisiatif inovasi teknologi pertanian berbasis IoT yang berfokus pada penyiraman tanaman otomatis. Berlokasi di Jl. Ringroad Barat, Dowangan, Banyuraden, Gamping, Sleman, Daerah Istimewa Yogyakarta, brand ini hadir untuk mendukung pertanian modern yang lebih efisien, hemat air, dan ramah lingkungan.
            </p>
            <p>
              Proyek ini lahir dari ide mahasiswa Universitas Sebelas Maret (UNS) yang menerima hibah untuk mengembangkan solusi nyata bagi kebutuhan pertanian. Melihat tantangan petani dan masyarakat perkotaan dalam mengatur kebutuhan air tanaman, tim mengembangkan perangkat IoT yang mampu mengotomatisasi proses penyiraman secara cerdas dan real-time.
            </p>
            <p>
              Nama <b>Ngundur Nguthik Tandur</b> berasal dari bahasa Jawa yang berarti “mengairi tanaman”. Filosofi ini mencerminkan semangat untuk selalu dekat dengan alam, menjaga keberlanjutan, sekaligus menghadirkan sentuhan teknologi yang sederhana namun berdampak besar bagi kehidupan.
            </p>
          </div>

          <div class="flex items-start justify-center">
            <img :src="Logo" alt="NGUNDUR" class="w-56 md:w-64 object-contain" />
          </div>
        </div>

        <!-- 3 KARTU -->
        <div class="mt-10 grid md:grid-cols-3 gap-6">
          <div class="bg-green-50 rounded-xl h-56 flex items-center justify-center shadow">
            <img :src="Profil" alt="" class="h-28 w-28 object-contain" />
          </div>
          <div class="bg-green-50 rounded-xl h-56 flex items-center justify-center shadow">
            <img :src="Profil" alt="" class="h-28 w-28 object-contain" />
          </div>
          <div class="bg-green-50 rounded-xl h-56 flex items-center justify-center shadow">
            <img :src="Profil" alt="" class="h-28 w-28 object-contain" />
          </div>
        </div>
      </div>
    </section>

    <!-- VISI & MISI -->
    <section class="py-12 bg-green-50">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h3 class="text-3xl md:text-4xl font-extrabold text-green-700">VISI & MISI NGUNDUR</h3>

        <h4 class="mt-8 text-xl font-semibold text-gray-800">VISI NGUNDUR</h4>
        <p class="mt-2 text-gray-700">
          Menjadi pelopor teknologi penyiraman otomatis yang mendukung pertanian berkelanjutan
          dan mudah diakses oleh semua kalangan.
        </p>

        <h4 class="mt-10 text-xl font-semibold text-gray-800">MISI NGUNDUR</h4>
  <ul class="mt-3 space-y-2 text-gray-700 text-justify max-w-2xl mx-auto list-decimal list-inside" style="text-align:justify;">
          <li>Menghadirkan solusi IoT yang efisien dan hemat sumber daya.</li>
          <li>Memberikan manfaat nyata bagi petani maupun penghobi berkebun.</li>
          <li>Mendorong teknologi ramah lingkungan untuk keberlanjutan pangan.</li>
          <li>Membangun kolaborasi antara akademik, industri, dan masyarakat.</li>
        </ul>
      </div>
    </section>

    <Footer />
  </div>
</template>