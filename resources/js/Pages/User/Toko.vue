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
  <div class="font-inter text-gray-900">
    <Header />

    <section class="mt-20 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <h1 class="text-lg font-semibold mb-4">Toko</h1>

      <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
        <div v-for="p in products.data" :key="p.id_produk"
             class="rounded-2xl bg-green-100 shadow-[8px_8px_0_0_rgba(0,0,0,0.08)] p-4">
          <div class="h-44 flex items-center justify-center">
            <img :src="p.gambar || '/assets/dashboard/profil.png'" alt="" class="h-40 object-contain" />
          </div>

          <p class="mt-2 text-[12px] text-center leading-4 line-clamp-2">
            {{ p.nama_produk }}
          </p>

          <p class="mt-2 text-center text-red-600 font-bold">
            {{ fmt(p.harga) }}
          </p>

          <button class="mt-3 mx-auto grid place-items-center w-40 h-9 rounded-full bg-green-300 hover:bg-green-400 text-sm">
            <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Pagination sederhana -->
      <div class="mt-8 flex justify-center gap-3">
        <button v-if="products.prev_page_url" @click="router.visit(products.prev_page_url)" class="px-3 py-1 rounded border">
          Sebelumnya
        </button>
        <button v-if="products.next_page_url" @click="router.visit(products.next_page_url)" class="px-3 py-1 rounded border">
          Berikutnya
        </button>
      </div>
    </section>

    <Footer />
  </div>
</template>