<script setup>
import Header from '@/component/Header.vue'
import Footer from '@/component/Footer.vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  items: { type: Array, default: () => [] }
})

const currency = (n) => new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',maximumFractionDigits:0}).format(n)

function starArray(avg) {
  const a = Math.round(avg || 0)
  return Array.from({ length: 5 }, (_, i) => i < a)
}

function changeQty(item, delta) {
  const newQty = item.qty + delta
  if (newQty < 1) return
  router.put(route('cart.update', item.id_detail_keranjang), { qty: newQty }, { preserveScroll: true })
}

function removeItem(item) {
  router.delete(route('cart.remove', item.id_detail_keranjang), { preserveScroll: true })
}

// === NEW: badge jumlah & checkout all ===
const totalItems = computed(() => props.items.reduce((s, it) => s + (it.qty || 0), 0))
const totalPrice = computed(() => props.items.reduce((s, it) => s + ((it.product?.harga || 0) * (it.qty || 0)), 0))

const checkoutForm = useForm({
  items: []
})

function checkoutAll() {
  if (!props.items.length) return
  if (!confirm('Checkout semua produk di keranjang?')) return

  // persiapkan payload sederhana: id_produk + qty
  checkoutForm.items = props.items.map(it => ({
    id_produk: it.product.id_produk,
    qty: it.qty
  }))

  // POST ke route backend (sesuaikan nama route jika berbeda)
  checkoutForm.post(route('cart.checkoutAll'), {
    preserveScroll: true,
    onSuccess: () => {
      // contoh redirect setelah sukses: kembali ke toko
      router.visit(route('toko'))
    },
    onError: () => {
      // fallback: tetap di halaman dan tampilkan error via backend
    }
  })
}
// === END NEW ===
</script>

<template>
  <div class="font-inter text-gray-900 bg-gray-100 min-h-screen">
    <Header />
    <Head title="Keranjang" />

    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-20">
      <div class="flex items-center gap-4 mb-6">
        <h1 class="text-xl font-semibold">Keranjang</h1>

        <!-- lencana jumlah produk -->
        <div v-if="totalItems > 0" class="ml-2">
          <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-semibold leading-none text-white bg-red-500 rounded-full">
            {{ totalItems }}
          </span>
        </div>

        <!-- tombol Checkout All -->
        <div class="ml-auto">
          <button v-if="items.length"
                  @click="checkoutAll"
                  :disabled="checkoutForm.processing"
                  class="text-sm px-4 py-2 rounded-md bg-green-600 text-white hover:bg-green-700 disabled:opacity-60">
            {{ checkoutForm.processing ? 'Memproses...' : 'Checkout All' }}
          </button>
        </div>
      </div>

      <div v-if="!items.length" class="bg-white rounded-xl p-8 text-center shadow">
        <p class="text-gray-600">Keranjang Anda masih kosong.</p>
      </div>

      <div v-else class="space-y-6">
        <div
          v-for="it in items"
          :key="it.id_detail_keranjang"
          class="rounded-xl bg-green-50/70 border border-green-100 shadow-sm p-5 flex gap-5"
        >
          <img
            :src="it.product.gambar ? (/^https?:/.test(it.product.gambar)? it.product.gambar : `/storage/${it.product.gambar}`) : '/assets/dashboard/profil.png'"
            alt=""
            class="w-28 h-28 object-contain flex-shrink-0"
          />

          <div class="flex flex-col justify-between flex-1">
            <div>
              <h2 class="text-sm font-bold leading-snug">
                {{ it.product.nama_produk }}
              </h2>
              <p class="text-xs text-gray-600 mt-1 line-clamp-2">
                {{ it.product.deskripsi || '(Include panel surya) + Buku panduan + Source Code' }}
              </p>

              <!-- Stars -->
              <div class="flex items-center gap-1 mt-3">
                <span v-for="(filled,i) in starArray(it.avg_rating)" :key="i" class="inline-flex">
                  <svg
                    :class="filled ? 'text-yellow-500' : 'text-gray-300'"
                    class="w-5 h-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.175c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.287 3.966c.3.922-.755 1.688-1.54 1.118l-3.38-2.454a1 1 0 00-1.175 0l-3.38 2.454c-.785.57-1.84-.196-1.54-1.118l1.287-3.966a1 1 0 00-.364-1.118L2.05 9.394c-.783-.57-.38-1.81.588-1.81h4.175a1 1 0 00.95-.69l1.286-3.967z" />
                  </svg>
                </span>
                <span class="text-xs text-gray-500 ml-1">
                  {{ it.avg_rating ? (it.avg_rating.toFixed(1)+' / 5') : 'Menunggu ulasan' }}
                </span>
              </div>

              <p class="text-[11px] text-gray-500 mt-2">
                {{ it.added_at || '—' }}
              </p>
            </div>

            <!-- Controls -->
            <div class="flex items-center gap-3 mt-4">
              <div class="flex items-center bg-green-600 text-white rounded-md overflow-hidden">
                <button @click="changeQty(it,-1)" class="px-3 py-2 hover:bg-green-700">-</button>
                <span class="px-4 select-none">{{ it.qty }}</span>
                <button @click="changeQty(it,1)" class="px-3 py-2 hover:bg-green-700">+</button>
              </div>
              <button
                @click="removeItem(it)"
                class="text-xs px-3 py-2 rounded-md border border-red-200 text-red-600 hover:bg-red-50"
              >
                Hapus
              </button>
              <button
                @click="router.visit(`/checkout/${it.product.id_produk}?qty=${it.qty}`)"
                class="text-xs px-3 py-2 rounded-md bg-green-600 text-white hover:bg-green-700"
              >
                Beli Sekarang
              </button>
              <span class="ml-auto text-sm font-medium text-green-700">
                {{ currency(it.product.harga * it.qty) }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </main>

    <Footer />
  </div>
</template>