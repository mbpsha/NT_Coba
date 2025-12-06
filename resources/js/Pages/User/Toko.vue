<script setup>
import { router } from '@inertiajs/vue3'
import Header from '@/Components/User/Header.vue'
import Footer from '@/Components/User/Footer.vue'

const props = defineProps({
  products: { type: Object, required: true },
  orderStatus: { type: String, default: null } // contoh: 'verified' | 'production' | 'shipping' | 'delivered'
})

const fmt = (n) => new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',maximumFractionDigits:0}).format(n)

function addToCart(p){
  router.post(route('cart.add'), { id_produk: p.id_produk }, { preserveScroll: true })
}
</script>

<template>
  <div class="font-inter text-gray-900 min-h-screen flex flex-col bg-white">
    <Header />

    <main class="flex-1">
      <section class="mt-20 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-lg font-semibold mb-4 text-center">Toko</h1>

        <div v-if="products.data?.length" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
          <div v-for="p in products.data" :key="p.id_produk"
               class="rounded-2xl bg-green-100 shadow-[8px_8px_0_0_rgba(0,0,0,0.08)] p-4">
            <button
              @click="router.visit(route('produk.show', p.id_produk))"
              class="h-44 w-full flex items-center justify-center"
            >
              <img :src="p.gambar || '/assets/dashboard/profil.png'" alt="" class="h-40 object-contain" />
            </button>
            <p class="mt-2 text-[12px] text-center leading-4 line-clamp-2">
              {{ p.nama_produk }}
            </p>
            <p class="mt-2 text-center text-red-600 font-bold">
              {{ fmt(p.harga) }}
            </p>
            <button
              @click="router.post(route('cart.add'), { id_produk: p.id_produk }, { preserveScroll: true })"
              class="mt-3 w-full h-9 rounded-md bg-green-600 hover:bg-green-700 text-white text-sm">
              Tambah ke Keranjang
            </button>
          </div>
        </div>

        <div v-else class="min-h-[40vh] flex items-center justify-center">
          <div class="text-gray-500 text-sm">Belum ada produk tersedia.</div>
        </div>

        <div class="mt-8 flex justify-center gap-3">
          <button v-if="products.prev_page_url" @click="router.visit(products.prev_page_url)" class="px-3 py-1 rounded border">
            Sebelumnya
          </button>
          <button v-if="products.next_page_url" @click="router.visit(products.next_page_url)" class="px-3 py-1 rounded border">
            Berikutnya
          </button>
        </div>
      </section>
    </main>

    <Footer />
  </div>
</template>