<script setup>
import Header from '@/Components/User/Header.vue'
import Footer from '@/Components/User/Footer.vue'
import { Head, router, useForm, usePage } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'
import Qris from '*/dashboard/qrisNGUNDUR.jpg'

const props = defineProps({
  order_id: { type: Number, default: null },
  user: { type: Object, required: true },
  shipping: { type: Object, required: true },
  products: { type: Array, required: true },
  cart_id: { type: Number, required: true },
  summary: { type: Object, required: true }
})

const page = usePage()
const fmt = (n) => new Intl.NumberFormat('id-ID', { style:'currency', currency:'IDR', maximumFractionDigits:0 }).format(n)
const formatWeight = (weight) => {
  if (!weight) return ''
  const kg = weight / 1000
  return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(kg)
}
const shippingQuoteText = computed(() => {
  const quote = props.shipping?.quote
  if (!quote) return 'Estimasi ongkir sementara'
  const courier = quote.courier ? `${quote.courier}${quote.service ? ' ' + quote.service : ''}` : null
  const etd = quote.etd ? `ETD ${quote.etd} hari` : null
  const suffix = quote.is_estimated ? ' (estimasi)' : ''
  const base = [courier, etd].filter(Boolean)
  return (base.length ? base.join(' • ') : 'Estimasi ongkir') + suffix
})

const orderId = computed(() => props.order_id ?? page.props.flash?.order_id ?? null)

const showNotification = ref(false)
const notification = ref(null)
watch(() => page.props.flash?.notification, (newNotif) => {
  if (newNotif) {
    notification.value = newNotif
    showNotification.value = true
  }
}, { immediate: true })

function closeNotification() {
  showNotification.value = false
  notification.value = null
}

function goToMyOrders() {
  router.visit(route('orders.my'))
}

function openAddressForm() {
  router.visit(route('checkout.cart.address'))
}

// Update qty di cart
function updateQty(productItem, change) {
  const newQty = productItem.qty + change
  if (newQty < 1) return
  if (newQty > productItem.stok) {
    alert(`Stok maksimal: ${productItem.stok}`)
    return
  }

  router.post(route('cart.update.qty'), {
    id_detail: productItem.id_detail_keranjang,
    qty: newQty
  }, {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      router.reload({ only: ['products', 'summary'] })
    }
  })
}

// CREATE ORDER FROM CART
const orderForm = useForm({
  cart_id: props.cart_id,
})
function createOrderFromCart() {
  orderForm.post(route('order.cart.create'), {
    preserveScroll: false,
    preserveState: false,
    onError: (errors) => {
      console.error('Order creation failed:', errors)
      alert('Gagal membuat pesanan. Periksa data Anda.')
    }
  })
}

// UPLOAD BUKTI
const payForm = useForm({
  trx_id: '',
  bukti_transfer: null,
  agree: false,
})
function onFileChange(e) {
  payForm.bukti_transfer = e.target.files?.[0] || null
}
function submitPayment() {
  if (!orderId.value) {
    alert('Silakan buat pesanan terlebih dahulu.')
    return
  }
  payForm.post(route('payment.confirm', { id_order: orderId.value }), {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      router.visit(route('orders.my'), { preserveScroll: true })
    }
  })
}
</script>

<template>
  <div class="min-h-screen text-gray-900 bg-gray-100 font-inter">
    <Header />
    <Head title="Checkout Cart" />

    <main class="max-w-5xl px-4 pt-24 pb-16 mx-auto sm:px-6 lg:px-8">
      <div class="flex items-center justify-between mb-4">
        <h1 class="text-lg font-semibold">Checkout - Keranjang</h1>
        <button @click="router.visit('/cart')" class="px-4 py-2 text-sm text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
          Kembali ke Keranjang
        </button>
      </div>

      <!-- Flash Messages -->
      <div v-if="$page.props.flash?.message" class="px-4 py-2 mb-4 text-blue-800 bg-blue-100 rounded">
        {{ $page.props.flash.message }}
      </div>
      <div v-if="$page.props.flash?.error" class="px-4 py-2 mb-4 text-red-800 bg-red-100 rounded">
        {{ $page.props.flash.error }}
      </div>

      <div class="p-5 bg-white border shadow-sm rounded-2xl md:p-6">
        <div class="grid md:grid-cols-[1fr_300px] gap-6">
          <!-- kiri: produk + alamat + ringkasan + konfirmasi -->
          <div class="space-y-4">
            <!-- Products List -->
            <div>
              <h2 class="mb-3 text-sm font-semibold text-gray-700">Produk yang Dipesan</h2>
              <div class="space-y-3">
                <div v-for="product in products" :key="product.id_produk"
                     class="flex gap-4 pb-3 border-b last:border-0 last:pb-0">
                  <img :src="product.gambar_url || '/assets/dashboard/profil.png'"
                       :alt="product.nama_produk"
                       class="object-contain w-20 h-20 border rounded-md"
                       @error="$event.target.src='/assets/dashboard/profil.png'" />
                  <div class="flex-1">
                    <p class="text-sm font-semibold leading-snug">{{ product.nama_produk }}</p>
                    <div class="mt-2 text-sm font-bold text-red-600">{{ fmt(product.harga) }}</div>
                    <div class="flex items-center gap-2 mt-2">
                      <span class="text-xs text-gray-600">Jumlah:</span>
                      <div class="flex items-center gap-1">
                        <button @click="updateQty(product, -1)"
                                :disabled="product.qty <= 1"
                                class="w-6 h-6 flex items-center justify-center bg-gray-200 rounded hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
                          -
                        </button>
                        <span class="w-8 text-center text-sm font-medium">{{ product.qty }}</span>
                        <button @click="updateQty(product, 1)"
                                :disabled="product.qty >= product.stok"
                                class="w-6 h-6 flex items-center justify-center bg-gray-200 rounded hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
                          +
                        </button>
                      </div>
                      <span class="text-[10px] text-gray-500">(Stok: {{ product.stok }})</span>
                    </div>
                  </div>
                  <div class="text-right">
                    <p class="text-sm font-semibold text-gray-800">{{ fmt(product.harga * product.qty) }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Alamat -->
            <div class="p-4 border border-green-100 rounded-lg bg-green-50">
              <div class="flex items-start justify-between gap-3">
                <div class="flex-1">
                  <p class="text-sm font-semibold">
                    {{ shipping.is_temp ? 'Alamat Pengiriman (Sementara)' : 'Alamat Pengiriman' }}
                  </p>
                  <p class="mt-1 text-xs text-gray-700 whitespace-pre-line">
                    {{ shipping.name }}<br>
                    {{ shipping.text }}
                  </p>
                  <p class="text-[11px] text-gray-500 mt-1">
                    Telp: {{ shipping.phone || '-' }}
                  </p>
                  <div class="pt-3 mt-3 text-xs text-gray-600 border-t border-green-100">
                    <p class="font-semibold text-gray-700">Info Pengiriman</p>
                    <p>{{ shippingQuoteText }}</p>
                    <p v-if="summary.weight" class="text-[11px] text-gray-500">
                      Berat dihitung: {{ formatWeight(summary.weight) }} kg
                    </p>
                  </div>
                </div>
                <button class="text-xs px-3 py-1.5 rounded-md bg-green-100 text-green-700 hover:bg-green-200"
                        @click="openAddressForm">
                  + Tambah Alamat Baru
                </button>
              </div>
            </div>

            <!-- Ringkasan -->
            <div class="p-4 text-sm border rounded-lg bg-gray-50">
              <div class="flex justify-between"><span>Subtotal Produk</span><span>{{ fmt(summary.subtotal) }}</span></div>
              <div class="flex justify-between"><span>Biaya Admin</span><span>{{ fmt(summary.admin) }}</span></div>
              <div class="flex justify-between"><span>Biaya Pengiriman</span><span>{{ fmt(summary.ongkir) }}</span></div>
              <p v-if="summary.courier || summary.service || summary.etd" class="mt-1 text-[11px] text-right text-gray-500">
                {{ [summary.courier, summary.service].filter(Boolean).join(' ') }}
                <span v-if="summary.etd">• ETD {{ summary.etd }} hari</span>
                <span v-if="summary.is_shipping_estimated" class="text-orange-600"> (estimasi)</span>
              </p>
              <div class="my-2 border-t"></div>
              <div class="flex justify-between font-semibold"><span>Total Harga</span><span>{{ fmt(summary.total) }}</span></div>
            </div>

            <!-- Tombol Buat Pesanan -->
            <button @click="createOrderFromCart"
                    :disabled="orderForm.processing || !!orderId"
                    class="w-full h-10 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed">
              {{ orderForm.processing ? 'Memproses...' : (orderId ? '✓ Pesanan Dibuat - Silakan Upload Bukti Pembayaran' : 'Buat Pesanan') }}
            </button>

            <!-- Info Order -->
            <div v-if="orderId || $page.props.flash?.order_created" class="px-4 py-3 mt-2 border-l-4 border-red-500 rounded-md bg-red-50">
              <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                  <p class="text-sm font-semibold text-red-800">⚠️ Selesaikan Pembayaran</p>
                  <p class="text-xs text-red-700">Status: <strong>Menunggu Pembayaran</strong></p>
                </div>
              </div>
            </div>

            <!-- Konfirmasi Pembayaran -->
            <div class="mt-6">
              <div class="px-4 py-2 text-sm font-semibold text-white bg-green-800 rounded-t-lg">
                Konfirmasi Pembayaranmu
              </div>
              <div class="p-4 space-y-4 border border-t-0 rounded-b-lg">
                <div>
                  <label class="block mb-1 text-sm font-medium">ID Transaksional (Opsional)</label>
                  <input v-model="payForm.trx_id" type="text"
                         class="w-full px-3 py-2 border rounded-md bg-gray-50 focus:bg-white" placeholder="Nomor referensi transfer" />
                  <p v-if="payForm.errors.trx_id" class="mt-1 text-xs text-red-600">{{ payForm.errors.trx_id }}</p>
                </div>

                <div>
                  <label class="block mb-1 text-sm font-medium">Bukti Pembayaran <span class="text-red-500">*</span></label>
                  <input type="file" accept=".jpg,.jpeg,.png,.pdf,.webp" @change="onFileChange"
                         class="block w-full text-sm file:mr-3 file:px-4 file:py-2 file:rounded-md file:bg-green-600 file:text-white file:hover:bg-green-700 file:border-0" />
                  <ul class="mt-2 text-[11px] text-gray-600 list-disc ml-4">
                    <li>Format: JPG, JPEG, PNG, PDF, WEBP</li>
                    <li>Maksimal ukuran: 5 MB</li>
                    <li>Pastikan bukti pembayaran jelas dan dapat dibaca</li>
                  </ul>
                  <p v-if="payForm.errors.bukti_transfer" class="mt-1 text-xs text-red-600">{{ payForm.errors.bukti_transfer }}</p>
                </div>

                <label class="flex items-start gap-2 text-xs text-gray-700">
                  <input type="checkbox" v-model="payForm.agree" class="mt-0.5">
                  <span>Saya menyetujui bahwa informasi yang saya berikan adalah benar dan saya telah melakukan pembayaran sesuai dengan jumlah yang tertera</span>
                </label>
                <p v-if="payForm.errors.agree" class="-mt-2 text-xs text-red-600">{{ payForm.errors.agree }}</p>

                <div class="flex gap-3">
                  <button type="button" @click="submitPayment"
                          :disabled="payForm.processing || !payForm.bukti_transfer || !payForm.agree"
                          class="flex-1 h-10 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700 disabled:opacity-60">
                    {{ payForm.processing ? 'Mengirim...' : 'Konfirmasi Pembayaran' }}
                  </button>
                  <button type="button" @click="router.reload({ only: ['shipping','summary'] })"
                          class="flex-1 h-10 text-sm text-white rounded-md bg-red-500/90 hover:bg-red-600">
                    Reset Form
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- kanan: QRIS -->
          <aside class="p-4 border rounded-xl">
            <p class="mb-2 text-sm font-semibold text-center">QRIS</p>
            <img :src="Qris" alt="QRIS" class="object-contain w-full rounded-md" />
            <p class="text-[11px] text-gray-500 text-center mt-2">Scan QRIS untuk melakukan pembayaran</p>
          </aside>
        </div>
      </div>
    </main>

    <Footer />
  </div>
</template>
