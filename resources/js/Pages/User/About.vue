<script setup>
import Header from '@/component/Header.vue'    
import Footer from '@/component/Footer.vue'
import { Head } from '@inertiajs/vue3'

// gunakan aset yang sama seperti sebelumnya
import Logo from '*/dashboard/logo-ngundur.png'
import Profil from '*/dashboard/profil.png'
</script>

<template>
  <div class="text-gray-900 font-inter">
    <Header />
    <Head title="Profil" />

    <!-- HERO (gaya seperti Blog.vue) -->
    <section
      class="mt-16 h-[260px] md:h-[320px] relative flex items-center bg-cover bg-right"
      style="background-image: linear-gradient(to bottom, rgba(128,128,128,0.65), rgba(128,128,128,0.65)), url('/assets/dashboard/bg-profil.jpg'); background-size: cover; background-position: center;"
    >
      <!-- jika belum punya gambar, cukup hapus url(...) di atas, gradient tetap tampil -->
      <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
        <h1 class="text-4xl font-extrabold text-white md:text-5xl drop-shadow">Profil</h1>
      </div>
    </section>

    <!-- TENTANG -->
    <section class="py-10">
      <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
        <h2 class="inline-block px-5 py-2 text-3xl font-extrabold text-green-700 bg-green-100 rounded-full md:text-4xl">
          Tentang NGUNDUR
        </h2>

        <p class="mt-4 text-sm text-gray-600">~IoT : Stech Smart Garden</p>

        <h1 class="px-5 py-2 text-2xl font-semibold text-green-700 md:text-4xl">
          NGUNDUR
        </h1>

        <div class="grid gap-10 mt-6 md:grid-cols-3">
          <div class="space-y-4 leading-7 text-gray-700 md:col-span-2">
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
            <img :src="Logo" alt="NGUNDUR" class="object-contain w-56 md:w-64" />
          </div>
        </div>

        <!-- 3 KARTU -->
        <div class="grid gap-6 mt-10 md:grid-cols-3">
          <div class="flex items-center justify-center h-56 shadow bg-green-50 rounded-xl">
            <img :src="Profil" alt="" class="object-contain h-28 w-28" />
          </div>
          <div class="flex items-center justify-center h-56 shadow bg-green-50 rounded-xl">
            <img :src="Profil" alt="" class="object-contain h-28 w-28" />
          </div>
          <div class="flex items-center justify-center h-56 shadow bg-green-50 rounded-xl">
            <img :src="Profil" alt="" class="object-contain h-28 w-28" />
          </div>
        </div>
      </div>
    </section>

    <!-- VISI & MISI -->
    <section class="py-12 bg-green-50">
      <div class="max-w-4xl px-4 mx-auto text-center sm:px-6 lg:px-8">
        <h3 class="text-3xl font-extrabold text-green-700 md:text-4xl">VISI & MISI NGUNDUR</h3>

              <h4 class="text-xl font-semibold">VISI NGUNDUR</h4>
              <p class="text-green-100/90">
                Menjadi pelopor teknologi penyiraman otomatis yang mendukung pertanian berkelanjutan
                dan mudah diakses oleh semua kalangan.
              </p>

        <h4 class="mt-10 text-xl font-semibold text-gray-800">MISI NGUNDUR</h4>

        <ul class="max-w-2xl mx-auto mt-3 space-y-2 text-justify text-gray-700 list-decimal list-inside" style="text-align:justify;">
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
