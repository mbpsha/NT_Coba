<template>
  <!-- Reusable Admin Sidebar -->
  <aside class="w-64 bg-white border-r shadow-sm hidden md:flex md:flex-col">
    <!-- Brand -->
    <div class="h-16 px-4 flex items-center gap-2 border-b">
      <img :src="Logo" alt="NGUNDUR" class="h-12 w-auto" />
    </div>
    <!-- Nav -->
    <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
      <RouterLink to="/admin" :class="linkClass('/admin')">
        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 3h7v7H3V3zM14 3h7v4h-7V3zM14 10h7v11h-7V10zM3 13h7v8H3v-8z"/></svg>
        <span>Dasboard</span>
      </RouterLink>

      <RouterLink to="/admin/products" :class="linkClass('/admin/products')">
        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 16V8a2 2 0 0 0-1-1.732l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.732l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><path d="M3.3 7.3L12 12l8.7-4.7"/></svg>
        <span>Produk</span>
      </RouterLink>

      <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-50 text-gray-700">
        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13A4 4 0 0 1 20 7"/></svg>
        <span>User</span>
      </a>

      <RouterLink to="/admin/products" :class="linkClass('/admin/products')">
        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 3h10a2 2 0 0 1 2 2v15l-4-2-4 2-4-2-4 2V5a2 2 0 0 1 2-2z"/><path d="M8 7h8M8 11h8M8 15h5"/></svg>
        <span>Penjualan</span>
      </RouterLink>

      <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-50 text-gray-700">
        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3l2.5 5 5 .7-3.6 3.5.9 5.3L12 15.9 7.2 17.5l.9-5.3L4.5 8.7 9.5 8l2.5-5z"/></svg>
        <span>Verifikasi Pembayaran</span>
      </a>

      <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-50 text-gray-700">
        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18M3 12h18M3 18h10"/></svg>
        <span>Status Produk</span>
      </a>

      <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-50 text-gray-700">
        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 12H3"/><path d="M12 9l3 3-3 3"/><path d="M21 3h-6a2 2 0 0 0-2 2v2"/></svg>
        <span>Log-out</span>
      </a>
    </nav>
  </aside>
</template>

<script setup>
import { RouterLink, useRoute } from 'vue-router'
import Logo from '../assets/logo-ngundur.png'

const route = useRoute()
function linkClass(path) {
  const base = 'flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-50 text-gray-700'
  const active = 'bg-green-50 text-green-700 font-medium'
  return route.path === path ? `${base} ${active}` : base
}
</script>

<style scoped>
aside { scrollbar-width: thin; }
</style>
