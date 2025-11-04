<script setup>
import { router } from '@inertiajs/vue3'
import Header from '@/Components/User/Header.vue'
import Footer from '@/Components/User/Footer.vue'

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

          <button class="block w-40 mx-auto mt-3 text-sm bg-green-300 rounded-full h-9 hover:bg-green-400">
            Tambah ke Keranjang
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
