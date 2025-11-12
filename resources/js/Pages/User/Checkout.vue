<script setup>
import Header from '@/component/Header.vue'
import Footer from '@/component/Footer.vue'
import { Head, router } from '@inertiajs/vue3'
import Qris from '*/dashboard/qrisNGUNDUR.jpg'

const props = defineProps({
  user: { type: Object, required: true },        // { id_user, id_alamat, nama, email, no_telp, alamat }
  product: { type: Object, required: true },     // { id_produk, nama_produk, deskripsi, harga, gambar_url }
  qty: { type: Number, default: 1 },
  summary: { type: Object, required: true }      // { admin, ongkir, subtotal, total }
})

const fmt = (n) => new Intl.NumberFormat('id-ID', {
  style:'currency', currency:'IDR', maximumFractionDigits:0
}).format(n)

function goBack() {
  try { router.visit(route('toko')) } catch { router.visit('/toko') }
}

function pay() {
  if (!props.user.id_user || !props.user.id_alamat) return
  router.post(route('checkout.process'), {
    id_user: props.user.id_user,
    id_alamat: props.user.id_alamat,
    metode_pembayaran: 'ewallet'
  })
}
</script>

<template>
  <div class="font-inter text-gray-900 bg-gray-100 min-h-screen">
    <Header />
    <Head title="Check Out" />

    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-16">
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-lg font-semibold">Check Out</h1>
        <button @click="goBack" class="px-4 py-2 text-sm rounded-md bg-gray-200 hover:bg-gray-300 text-gray-700">
          Kembali
        </button>
      </div>

      <div class="bg-white rounded-2xl border shadow-sm p-5 md:p-6">
        <div class="grid md:grid-cols-[1fr_300px] gap-6">
          <!-- Detail Produk -->
          <div class="space-y-4">
            <div class="flex gap-4">
              <img :src="product.gambar_url"
                    class="w-24 h-24 object-contain rounded-md border"
                    alt="produk"
                    @error="$event.target.src='/assets/dashboard/profil.png'"/>
              <div class="flex-1">
                <p class="font-semibold leading-snug">{{ product.nama_produk }}</p>
                <p class="text-xs text-gray-600 mt-1 line-clamp-2">{{ product.deskripsi }}</p>
                <div class="mt-2 text-red-600 font-bold">{{ fmt(product.harga) }}</div>
                <div class="text-xs text-gray-600 mt-1">Jumlah: <span class="font-medium">{{ qty }}</span></div>
                <div class="text-xs text-gray-600">Estimasi Tiba: <span class="font-medium">± 15 hari</span></div>
              </div>
            </div>

            <!-- Alamat -->
            <div class="rounded-lg bg-green-50 border border-green-100 p-4">
              <div class="flex items-start justify-between gap-3">
                <div>
                  <p class="text-sm font-semibold">Alamat Saya</p>
                  <p class="text-xs text-gray-700 mt-1 whitespace-pre-line">
                    {{ user.nama }}<br>
                    {{ user.alamat }}
                  </p>
                  <p class="text-[11px] text-gray-500 mt-1">
                    Email: {{ user.email }} • Telp: {{ user.no_telp || '-' }}
                  </p>
                </div>
                <button class="text-xs px-3 py-1.5 rounded-md bg-green-100 text-green-700 hover:bg-green-200"
                        @click="router.visit(route('profile.show'))">
                  + Ubah Alamat
                </button>
              </div>
            </div>

            <!-- Ringkasan -->
            <div class="rounded-lg bg-gray-50 border p-4 text-sm">
              <div class="flex justify-between"><span>Harga Produk</span><span>{{ fmt(product.harga * qty) }}</span></div>
              <div class="flex justify-between"><span>Biaya Admin</span><span>{{ fmt(summary.admin) }}</span></div>
              <div class="flex justify-between"><span>Biaya Pengiriman</span><span>{{ fmt(summary.ongkir) }}</span></div>
              <div class="border-t my-2"></div>
              <div class="flex justify-between font-semibold"><span>Total Harga</span><span>{{ fmt(summary.total) }}</span></div>
            </div>

            <button
              class="w-full h-10 rounded-md text-white text-sm font-medium"
              :class="user.id_user && user.id_alamat ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-300 cursor-not-allowed'"
              :disabled="!user.id_user || !user.id_alamat"
              @click="pay"
            >
              {{ user.id_user && user.id_alamat ? 'Selesaikan Pembayaran' : 'Lengkapi Alamat Terlebih Dahulu' }}
            </button>
          </div>

          <!-- QRIS -->
          <aside class="rounded-xl border p-4">
            <p class="text-sm font-semibold text-center mb-2">QRIS</p>
            <img :src="Qris" alt="QRIS" class="w-full rounded-md object-contain" />
            <p class="text-[11px] text-gray-500 text-center mt-2">Scan QRIS untuk pembayaran</p>
          </aside>
        </div>
      </div>
    </main>

    <Footer />
  </div>
</template>