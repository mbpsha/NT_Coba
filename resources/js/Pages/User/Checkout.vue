<script setup>
import Header from '@/component/Header.vue'
import Footer from '@/component/Footer.vue'
import { Head, router, useForm, usePage } from '@inertiajs/vue3'
import Qris from '*/dashboard/qrisNGUNDUR.jpg'

const props = defineProps({
  user: { type: Object, required: true },
  shipping: { type: Object, required: true },
  product: { type: Object, required: true },
  qty: { type: Number, default: 1 },
  summary: { type: Object, required: true }
})

const page = usePage()
const fmt = (n) => new Intl.NumberFormat('id-ID', { style:'currency', currency:'IDR', maximumFractionDigits:0 }).format(n)

function openAddressForm() {
  router.visit(route('checkout.address', { id_produk: props.product.id_produk }) + `?qty=${props.qty}`)
}

// FORM KONFIRMASI
const payForm = useForm({
  qty: props.qty,
  trx_id: '',
  bukti_transfer: null,
  agree: false,
})
function onFileChange(e) {
  payForm.bukti_transfer = e.target.files?.[0] || null
}
function submitPayment() {
  payForm.post(route('checkout.confirm', { id_produk: props.product.id_produk }), {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      payForm.reset('trx_id', 'bukti_transfer', 'agree')
    }
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
        <button @click="router.visit('/toko')" class="px-4 py-2 text-sm rounded-md bg-gray-200 hover:bg-gray-300 text-gray-700">
          Kembali
        </button>
      </div>

      <div v-if="$page.props.flash?.payment_submitted" class="mb-4 rounded bg-green-100 text-green-800 px-4 py-2">
        {{ $page.props.flash.payment_submitted }}
      </div>

      <div class="bg-white rounded-2xl border shadow-sm p-5 md:p-6">
        <div class="grid md:grid-cols-[1fr_300px] gap-6">

          <!-- kiri: produk + alamat + ringkasan + konfirmasi -->
          <div class="space-y-4">
            <!-- Produk -->
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

            <!-- Alamat untuk pesanan ini -->
            <div class="rounded-lg bg-green-50 border border-green-100 p-4">
              <div class="flex items-start justify-between gap-3">
                <div>
                  <p class="text-sm font-semibold">
                    {{ shipping.is_temp ? 'Alamat Pengiriman (Sementara)' : 'Alamat Saya' }}
                  </p>
                  <p class="text-xs text-gray-700 mt-1 whitespace-pre-line">
                    {{ shipping.name }}<br>
                    {{ shipping.text }}
                  </p>
                  <p class="text-[11px] text-gray-500 mt-1">
                    Telp: {{ shipping.phone || '-' }}
                  </p>
                </div>
                <button class="text-xs px-3 py-1.5 rounded-md bg-green-100 text-green-700 hover:bg-green-200"
                        @click="openAddressForm">
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

            <!-- Konfirmasi Pembayaran -->
            <div class="mt-6">
              <div class="rounded-t-lg bg-green-800 text-white px-4 py-2 text-sm font-semibold">
                Konfirmasi Pembayaranmu
              </div>
              <div class="rounded-b-lg border border-t-0 p-4 space-y-4">
                <div>
                  <label class="block text-sm font-medium mb-1">ID Transaksional (Opsional)</label>
                  <input v-model="payForm.trx_id" type="text"
                         class="w-full rounded-md border px-3 py-2 bg-gray-50 focus:bg-white" placeholder="Nomor referensi transfer" />
                  <p v-if="payForm.errors.trx_id" class="text-xs text-red-600 mt-1">{{ payForm.errors.trx_id }}</p>
                </div>

                <div>
                  <label class="block text-sm font-medium mb-1">Bukti Pembayaran <span class="text-red-500">*</span></label>
                  <input type="file" accept=".jpg,.jpeg,.png" @change="onFileChange"
                         class="block w-full text-sm file:mr-3 file:px-4 file:py-2 file:rounded-md file:bg-green-600 file:text-white file:hover:bg-green-700 file:border-0" />
                  <ul class="mt-2 text-[11px] text-gray-600 list-disc ml-4">
                    <li>Format: JPG, JPEG, PNG</li>
                    <li>Maksimal ukuran: 5 MB</li>
                    <li>Pastikan bukti pembayaran jelas dan dapat dibaca</li>
                  </ul>
                  <p v-if="payForm.errors.bukti_transfer" class="text-xs text-red-600 mt-1">{{ payForm.errors.bukti_transfer }}</p>
                </div>

                <label class="flex items-start gap-2 text-xs text-gray-700">
                  <input type="checkbox" v-model="payForm.agree" class="mt-0.5">
                  <span>Saya menyetujui bahwa informasi yang saya berikan adalah benar dan saya telah melakukan pembayaran sesuai dengan jumlah yang tertera</span>
                </label>
                <p v-if="payForm.errors.agree" class="text-xs text-red-600 -mt-2">{{ payForm.errors.agree }}</p>

                <div class="flex gap-3">
                  <button type="button" @click="submitPayment"
                          :disabled="payForm.processing || !payForm.bukti_transfer || !payForm.agree"
                          class="flex-1 h-10 rounded-md bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white text-sm">
                    {{ payForm.processing ? 'Mengirim...' : 'Konfirmasi Pembayaran' }}
                  </button>
                  <button type="button" @click="router.reload({ only: ['shipping','summary'] })"
                          class="flex-1 h-10 rounded-md bg-red-500/90 hover:bg-red-600 text-white text-sm">
                    Reset Form
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- kanan: QRIS -->
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