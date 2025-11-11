<script setup>
import Header from '@/component/Header.vue'
import Footer from '@/component/Footer.vue'
import { Head, router } from '@inertiajs/vue3'
import Qris from '*/dashboard/qrisNGUNDUR.jpg'

const props = defineProps({
  user: { type: Object, required: true },        // nama, email, no_telp, alamat
  product: { type: Object, required: true },
  qty: { type: Number, default: 1 },
  summary: { type: Object, required: true }
})

const fmt = (n) => new Intl.NumberFormat('id-ID', { style:'currency', currency:'IDR', maximumFractionDigits:0 }).format(n)
</script>

<template>
  <div class="font-inter text-gray-900 bg-gray-100 min-h-screen">
    <Header />
    <Head title="Check Out" />

    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-16">
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-lg font-semibold">Check Out</h1>
        <button @click="router.visit('/toko')" class="px-4 py-2 text-sm rounded-md bg-gray-200 hover:bg-gray-300 text-gray-700">
          Kembali
        </button>
      </div>

      <div class="bg-white rounded-2xl border shadow-sm p-5 md:p-6">
        <div class="grid md:grid-cols-[1fr_300px] gap-6">

          <!-- Produk -->
          <div class="space-y-4">
            <div class="flex gap-4">
              <img :src="product.gambar ? (/^https?:/.test(product.gambar)? product.gambar : `/storage/${product.gambar}`) : '/assets/dashboard/profil.png'"
                   class="w-24 h-24 object-contain rounded-md border" alt="produk" />
              <div class="flex-1">
                <p class="font-semibold leading-snug">{{ product.nama_produk }}</p>
                <p class="text-xs text-gray-600 mt-1 line-clamp-2">{{ product.deskripsi }}</p>
                <div class="mt-2 text-red-600 font-bold">{{ fmt(product.harga) }}</div>
                <div class="text-xs text-gray-600 mt-1">Jumlah: <span class="font-medium">{{ qty }}</span></div>
                <div class="text-xs text-gray-600">Estimasi Tiba: <span class="font-medium">± 15 hari</span></div>
              </div>
            </div>

            <!-- Alamat dari Profil -->
            <div class="rounded-lg bg-green-50 border border-green-100 p-4">
              <div class="flex items-start justify-between gap-3">
                <div>
                  <p class="text-sm font-semibold">Alamat Saya</p>
                  <p class="text-xs text-gray-700 mt-1 whitespace-pre-line">
                    {{ props.user.nama }}<br>
                    {{ props.user.alamat }}
                  </p>
                  <p class="text-[11px] text-gray-500 mt-1">
                    Email: {{ props.user.email }} • Telp: {{ props.user.no_telp || '-' }}
                  </p>
                </div>
                <button class="text-xs px-3 py-1.5 rounded-md bg-green-100 text-green-700 hover:bg-green-200"
                        @click="router.visit(route('profile.show'))">
                  + Tambah Alamat Baru
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

            <button class="w-full h-10 rounded-md bg-green-600 hover:bg-green-700 text-white text-sm font-medium">
              Selesaikan Pembayaranmu
            </button>
          </div>

          <!-- QRIS -->
          <aside class="rounded-xl border p-4">
            <p class="text-sm font-semibold text-center mb-2">QRIS</p>
            <img :src="Qris" alt="QRIS" class="w-full rounded-md object-contain" />
            <p class="text-[11px] text-gray-500 text-center mt-2">Scan QRIS untuk melakukan pembayaran</p>
          </aside>
        </div>
      </div>
    </main>

    <Footer />
  </div>
</template>