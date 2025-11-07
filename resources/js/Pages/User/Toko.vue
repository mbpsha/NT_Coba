<script setup>
import { router } from '@inertiajs/vue3'
import Header from '@/component/Header.vue'    
import Footer from '@/component/Footer.vue'

const props = defineProps({
  products: { type: Object, required: true } // paginated
})

const fmt = (n) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(n)
</script>

<template>
  <div class="text-gray-900 font-inter">
    <Header />

    <section class="max-w-6xl px-4 mx-auto mt-20 sm:px-6 lg:px-8">
      <h1 class="mb-4 text-lg font-semibold">Toko</h1>

      <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
        <div v-for="p in products.data" :key="p.id_produk"
             class="rounded-2xl bg-green-100 shadow-[8px_8px_0_0_rgba(0,0,0,0.08)] p-4">
          <div class="flex items-center justify-center h-44">
            <img :src="p.gambar || '/assets/dashboard/profil.png'" alt="" class="object-contain h-40" />
          </div>

          <p class="mt-2 text-[12px] text-center leading-4 line-clamp-2">
            {{ p.nama_produk }}
          </p>

          <p class="mt-2 font-bold text-center text-red-600">
            {{ fmt(p.harga) }}
          </p>

          <button class="grid w-40 mx-auto mt-3 text-sm bg-green-300 rounded-full place-items-center h-9 hover:bg-green-400">
            <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Pagination sederhana -->
      <div class="flex justify-center gap-3 mt-8">
        <button v-if="products.prev_page_url" @click="router.visit(products.prev_page_url)" class="px-3 py-1 border rounded">
          Sebelumnya
        </button>
        <button v-if="products.next_page_url" @click="router.visit(products.next_page_url)" class="px-3 py-1 border rounded">
          Berikutnya
        </button>
      </div>
    </section>

    <Footer />
  </div>
</template>
