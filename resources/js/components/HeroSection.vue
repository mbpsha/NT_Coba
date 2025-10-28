<template>
  <section class="relative flex justify-center">
    <div class="relative w-[1250px] h-[800px] overflow-hidden rounded-2x1">
      <img :src="BgHero" alt="Leaves" class="absolute inset-0 w-full h-full object-cover">
      <div class="relative z-10 px-8 sm:px-40 lg:px-14 top-1/4">
        <h1 class="text-white text-5x1 sm:text-8xl font-Urbanist leading-tight">
          Stech Smart<br>Garden
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
</template>

<script setup>
import BgHero from '../assets/bg-depan01.png'
</script>

<style scoped>
.rounded-2x1 { border-radius: 1rem; }
</style>
