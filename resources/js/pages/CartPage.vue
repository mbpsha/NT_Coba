<template>
  <div class="min-h-screen flex flex-col">
    <header class="h-16 flex items-center justify-between px-4 bg-white/80 backdrop-blur shadow-sm">
      <RouterLink to="/" class="flex items-center gap-2">
        <img :src="Logo" alt="NGUNDUR" class="h-14" />
      </RouterLink>
      <RouterLink to="/" class="px-4 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white text-sm">Kembali ke Home</RouterLink>
    </header>

    <main class="flex-1 max-w-4xl mx-auto w-full p-6">
      <h1 class="text-2xl font-semibold mb-4">Keranjang</h1>
      <p class="text-gray-600">Keranjang masih kosong. Tambahkan produk dari halaman toko.</p>
    </main>
  </div>
</template>

<script setup>
import { RouterLink } from 'vue-router'
import Logo from '../assets/logo-ngundur.png'
</script>
