<template>
  <div class="min-h-screen flex flex-col bg-white">
    <header class="h-16 flex items-center justify-between px-4 bg-white/90 backdrop-blur shadow-sm">
      <RouterLink to="/" class="flex items-center gap-2">
        <img :src="Logo" alt="NGUNDUR" class="h-14" />
      </RouterLink>
      <RouterLink to="/" class="px-4 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white text-sm">Kembali ke Home</RouterLink>
    </header>

    <main class="flex-1 max-w-3xl mx-auto w-full p-6">
      <h1 class="text-2xl font-bold mb-2 capitalize">{{ title }}</h1>
      <p class="text-gray-500 mb-6">Halaman berita masih kosong. Konten akan ditambahkan nanti.</p>
      <div class="rounded-xl border border-gray-200 p-6 text-gray-400 text-center">
        Placeholder content for "{{ slug }}".
      </div>
    </main>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute, RouterLink } from 'vue-router'
import Logo from '../assets/logo-ngundur.png'

const route = useRoute()
const slug = computed(() => route.params.slug)
const title = computed(() => decodeURIComponent(String(slug.value || '')).replace(/-/g, ' '))
</script>
