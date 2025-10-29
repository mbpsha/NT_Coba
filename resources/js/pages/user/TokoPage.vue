<template>
  <!-- Toko / Shop listing -->
  <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-gray-900">Toko</h1>

    <!-- Products grid -->
    <div class="mt-6 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <article v-for="p in products" :key="p.id" class="rounded-2xl bg-green-100/60 shadow-md p-4 relative">
        <div class="aspect-square rounded-xl overflow-hidden bg-white border border-green-100 flex items-center justify-center">
          <img v-if="p.image_url" :src="p.image_url" :alt="p.name" class="w-full h-full object-cover" />
          <div v-else class="text-gray-400 text-sm">Tidak ada gambar</div>
        </div>
        <div class="mt-3">
          <h3 class="font-semibold text-gray-900 leading-tight">{{ p.name }}</h3>
          <p v-if="p.description" class="text-sm text-gray-600 line-clamp-2 mt-1">{{ p.description }}</p>
          <div class="mt-2 font-bold text-red-600">Rp. {{ formatPrice(p.price) }}</div>
        </div>
        <button class="mt-3 w-full rounded-xl bg-green-300/80 hover:bg-green-300 py-2 flex items-center justify-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l3-7H6.4M7 13L5.4 5M7 13l-2 9m12-9l2 9"/></svg>
          Tambah ke keranjang
        </button>
      </article>
    </div>

    <!-- Empty state -->
    <div v-if="!loading && products.length === 0" class="mt-6 text-gray-500">Belum ada produk.</div>
  </section>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import axios from 'axios'

const products = ref([])
const loading = ref(false)

function formatPrice(n){
  try { return Number(n).toLocaleString('id-ID') } catch { return n }
}

async function fetchProducts(){
  try {
    loading.value = true
    const { data } = await axios.get('/api/products')
    products.value = Array.isArray(data) ? data : []
  } finally { loading.value = false }
}

onMounted(() => {
  fetchProducts()
  window.addEventListener('product:created', fetchProducts)
})

onBeforeUnmount(() => {
  window.removeEventListener('product:created', fetchProducts)
})
</script>
