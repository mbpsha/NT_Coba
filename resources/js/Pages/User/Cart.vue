<script setup>
import Header from '@/Components/User/Header.vue'
import Footer from '@/Components/User/Footer.vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import axios from 'axios'

const props = defineProps({
  user: { type: Object, default: () => ({}) },
  shipping: { type: Object, default: () => ({ name:'', text:'', phone:'', is_temp:false }) },
  product: { type: Object, default: null },
  qty: { type: Number, default: 1 },
  summary: { type: Object, default: () => ({ admin:0, ongkir:0, subtotal:0, total:0 }) },
  items: { type: Array, default: () => [] }
})

const FALLBACK = '/assets/dashboard/profil.png'

// normalisasi path gambar
const imgUrl = (g) => {
  if (!g) return FALLBACK
  let s = String(g).replaceAll('\\','/')
  if (/^https?:\/\//i.test(s)) return s
  if (s.startsWith('/storage/')) return s
  if (s.startsWith('storage/')) return '/'+s
  if (s.startsWith('/')) return s
  return '/storage/' + s
}

const fmt = (n) => new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',maximumFractionDigits:0}).format(n)

// Fallback hitung subtotal jika backend kirim 0
const itemsList = ref((props.items || []).map(i => ({ ...i })))

const subtotalCalc = computed(() =>
  itemsList.value.reduce((s,it)=> s + ((it.product?.harga||0) * (it.qty || it.jumlah || 0)), 0)
)

const summaryView = computed(() => {
  const sub = props.summary.subtotal || subtotalCalc.value
  const admin = props.summary.admin || (sub ? 5000 : 0)
  const ongkir = props.summary.ongkir || (sub ? 20000 : 0)
  const total = props.summary.total || (sub + admin + ongkir)
  return { subtotal: sub, admin, ongkir, total }
})

// Checkout single item
function checkoutSingle(it){
  const productId = it?.product?.id_produk || it?.id_produk
  if(!productId) {
    console.error('Product ID not found:', it)
    return
  }
  router.visit(route('checkout.show', { id_produk: productId }) + '?qty=' + (it.qty || 1))
}

// Checkout semua item (bulk)
function checkoutAll(){
  if(!props.items.length) return
  router.visit(route('checkout.cart'))
}

// (Jika tetap diperlukan oleh fitur lain)
const payForm = useForm({
  qty: props.qty,
  trx_id: '',
  bukti_transfer: null,
  agree: false,
})

function onFileChange(e){
  payForm.bukti_transfer = e.target.files?.[0] || null
}

// Address form handler (perbaiki firstId)
function openAddressForm(){
  const firstId = props.items[0]?.id_produk ?? props.items[0]?.product?.id_produk
  if(!firstId) return
  router.visit(route('checkout.address',{ id_produk: firstId }) + '?qty=1&from=cart')
}

// Update qty cart item
function updateQty(cartDetailId, newQty) {
  // allow 0 to remove item
  if (newQty < 0) return

  // optimistic UI update: update local itemsList immediately
  const idx = itemsList.value.findIndex(i => i.id_detail_keranjang === cartDetailId)
  if (idx !== -1) {
    if (newQty <= 0) {
      // remove locally
      itemsList.value.splice(idx, 1)
    } else {
      itemsList.value[idx].qty = newQty
    }
  }

  // send request to server; if it fails, reload page to reflect server state
  axios.post(route('cart.update.qty'), {
    id_detail: cartDetailId,
    qty: newQty
  }).catch(() => {
    // fallback: reload to sync
    window.location.reload()
  })
}

function incrementQty(item) {
  updateQty(item.id_detail_keranjang, (item.qty || 1) + 1)
}

function decrementQty(item) {
  // if currently 1, decrementing will remove the item
  updateQty(item.id_detail_keranjang, (item.qty || 1) - 1)
}

console.log('Render Cart.vue')
</script>

<template>
  <div class="font-inter text-gray-900 bg-gray-100 min-h-screen">
    <Header />
    <Head title="Keranjang" />
    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-16 space-y-6">
      <div class="flex justify-between items-center">
        <h1 class="text-lg font-semibold">Keranjang</h1>
        <button @click="router.visit('/toko')" class="px-4 py-2 text-sm rounded-md bg-gray-200 hover:bg-gray-300">Kembali</button>
      </div>

      <!-- Empty -->
      <div v-if="!items.length" class="p-6 bg-white rounded-xl border text-sm text-gray-600">
        Keranjang kosong.
      </div>

      <!-- Items + Summary -->
      <div v-else class="grid md:grid-cols-[1fr_280px] gap-6">
        <div class="space-y-3">
            <div v-for="it in itemsList" :key="it.id_detail_keranjang"
              class="flex items-center gap-4 bg-white rounded-xl border p-3">
              <img
                :src="imgUrl(it.product.gambar)"
                alt=""
                class="w-16 h-16 object-contain rounded border"
                @error="$event.target.src = FALLBACK"
              />

            <div class="flex-1">
              <p class="text-sm font-medium">{{ it.product?.nama_produk || 'Produk' }}</p>
              <div class="flex items-center gap-2 mt-2">
                <button @click="decrementQty(it)"
                  class="w-6 h-6 flex items-center justify-center rounded border border-gray-300 hover:bg-gray-100">
                  -
                </button>
                <span class="text-sm font-medium w-8 text-center">{{ it.qty || 1 }}</span>
                <button @click="incrementQty(it)"
                        class="w-6 h-6 flex items-center justify-center rounded border border-gray-300 hover:bg-gray-100">
                  +
                </button>
              </div>
              <p class="text-[11px] text-gray-500 mt-1">{{ fmt(it.product?.harga || 0) }} / pcs</p>
            </div>

            <div class="flex flex-col items-end gap-2">
              <div class="text-sm font-semibold text-green-700">
                {{ fmt((it.product?.harga || 0) * (it.qty || 0)) }}
              </div>
              <button @click="checkoutSingle(it)"
                      class="text-[11px] px-3 py-1.5 rounded bg-green-600 text-white hover:bg-green-700">
                Checkout
              </button>
            </div>
          </div>
        </div>

        <aside class="bg-white rounded-xl border p-4 space-y-3 text-sm">
          <h2 class="font-semibold">Ringkasan</h2>
          <div class="flex justify-between"><span>Subtotal</span><span>{{ fmt(summaryView.subtotal) }}</span></div>
          <div class="flex justify-between"><span>Biaya Admin</span><span>{{ fmt(summaryView.admin) }}</span></div>
          <div class="flex justify-between"><span>Biaya Pengiriman</span><span>{{ fmt(summaryView.ongkir) }}</span></div>
          <div class="border-t my-2"></div>
          <div class="flex justify-between font-semibold"><span>Total</span><span>{{ fmt(summaryView.total) }}</span></div>
          <button @click="checkoutAll"
                  class="w-full h-10 rounded-md bg-green-600 hover:bg-green-700 text-white text-sm">
            Checkout All
          </button>
        </aside>
      </div>
    </main>
    <Footer />
  </div>
</template>
