<script setup>
import Header from '@/component/Header.vue'
import Footer from '@/component/Footer.vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import Qris from '*/dashboard/qrisNGUNDUR.jpg'

const props = defineProps({
  user: { type: Object, required: true },
  shipping: { type: Object, required: true },
  product: { type: Object, default: null },
  qty: { type: Number, default: 1 },
  summary: { type: Object, required: true },
  items: { type: Array, default: () => [] }
})

const isBulk = computed(() => (props.items?.length || 0) > 0)
const fmt = (n) => new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',maximumFractionDigits:0}).format(n)

function openAddressForm() {
  if (isBulk.value) {
    const firstId = props.items[0]?.id_produk ?? props.items[0]?.product?.id_produk
    if (!firstId) return
    router.visit(route('checkout.address', { id_produk: firstId }) + '?qty=1&from=cart')
  } else {
    router.visit(route('checkout.address', { id_produk: props.product.id_produk }) + `?qty=${props.qty}`)
  }
}

const payForm = useForm({
  id_alamat: props.user?.id_alamat || null,
  metode_pembayaran: 'transfer_bank', // default, sesuaikan jika ada selector
  trx_id: '',
  bukti_transfer: null,
  agree: false
})
function onFileChange(e){ payForm.bukti_transfer = e.target.files?.[0] || null }

const canSubmit = computed(() => payForm.agree && payForm.bukti_transfer)

function submitPayment(){
  if(!payForm.agree) return
  const options = { forceFormData: true }
  if(isBulk.value){
    payForm.post(route('checkout.confirmCart'), options)
  } else {
    payForm.post(route('order.create', { id_produk: props.product.id_produk }), options)
  }
}
</script>

<template>
  <div class="font-inter text-gray-900 bg-white min-h-screen"> 
    <Header />
    <Head :title="isBulk ? 'Checkout Keranjang' : 'Checkout Produk'" />
    <!-- AREA UTAMA DIBERI WARNA #DBF8D1 -->
    <main class="pt-24 pb-16">
      <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="rounded-3xl p-6 md:p-7" style="background:#DBF8D1;">
          <!-- card konten tetap putih -->
          <div class="bg-green-50 rounded-2xl border shadow-sm p-5 md:p-6">
            <div class="flex justify-between items-center mb-4">
              <h1 class="text-lg font-semibold">
                {{ isBulk ? 'Checkout Semua Barang' : 'Check Out' }}
              </h1>
              <button @click="router.visit('/toko')" class="px-4 py-2 text-sm rounded-md bg-gray-200 hover:bg-gray-300 text-gray-700">
                Kembali
              </button>
            </div>

            <div class="grid md:grid-cols-[1fr_300px] gap-6">
              <!-- KIRI -->
              <div class="space-y-5">
                <!-- Single -->
                <div v-if="!isBulk" class="flex gap-4">
                  <img :src="product.gambar ? (/^https?:/.test(product.gambar)? product.gambar : `/storage/${product.gambar}`) : '/assets/dashboard/profil.png'"
                       class="w-24 h-24 object-contain rounded-md border" />
                  <div class="flex-1">
                    <p class="font-semibold text-sm">{{ product.nama_produk }}</p>
                    <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ product.deskripsi }}</p>
                    <p class="text-xs mt-2">
                      Jumlah: <span class="font-medium">{{ qty }}</span> |
                      Harga Satuan: {{ fmt(product.harga) }}
                    </p>
                    <p class="text-sm font-semibold text-green-700 mt-1">
                      Total Produk: {{ fmt(product.harga * qty) }}
                    </p>
                  </div>
                </div>

                <!-- Bulk -->
                <div v-else class="space-y-3">
                  <h2 class="text-sm font-semibold flex items-center gap-2">
                    Produk dalam Keranjang
                    <span class="px-2 py-0.5 bg-green-600 text-white rounded text-xs">{{ items.length }} item</span>
                  </h2>
                  <div v-for="it in items" :key="it.id_detail_keranjang"
                       class="flex gap-3 p-3 rounded-lg border bg-white">
                    <img :src="it.product.gambar ? (/^https?:/.test(it.product.gambar)? it.product.gambar : `/storage/${it.product.gambar}`) : '/assets/dashboard/profil.png'"
                         class="w-16 h-16 object-contain rounded border" />
                    <div class="flex-1">
                      <p class="text-xs font-semibold">{{ it.product.nama_produk }}</p>
                      <p class="text-[11px] text-gray-500 line-clamp-2">{{ it.product.deskripsi }}</p>
                      <p class="text-[11px] mt-1">
                        {{ it.qty }} x {{ fmt(it.product.harga) }}
                        = <span class="font-medium text-green-700">{{ fmt(it.product.harga * it.qty) }}</span>
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Alamat -->
                <div class="rounded-lg bg-green-50 border border-green-100 p-4 text-xs">
                  <div class="flex justify-between items-start mb-1">
                    <p class="font-semibold">Alamat Pengiriman<span v-if="shipping.is_temp"> (Sementara)</span></p>
                    <button @click="openAddressForm"
                            class="px-3 py-1 text-[11px] rounded-md bg-green-600 text-white hover:bg-green-700">
                      + Tambah Alamat Baru
                    </button>
                  </div>
                  <pre class="whitespace-pre-line leading-relaxed text-[11px] font-medium text-gray-700">{{ shipping.text }}</pre>
                  <p class="mt-1 text-[11px] text-gray-500">Telp: {{ shipping.phone || '-' }}</p>
                </div>

                <!-- Ringkasan -->
                <div class="rounded-lg bg-gray-50 border p-4 text-xs space-y-1">
                  <div class="flex justify-between" v-if="isBulk">
                    <span>Subtotal Semua Produk</span><span>{{ fmt(summary.subtotal) }}</span>
                  </div>
                  <div class="flex justify-between" v-else>
                    <span>Subtotal Produk</span><span>{{ fmt(product.harga * qty) }}</span>
                  </div>
                  <div class="flex justify-between"><span>Biaya Admin</span><span>{{ fmt(summary.admin) }}</span></div>
                  <div class="flex justify-between"><span>Biaya Pengiriman</span><span>{{ fmt(summary.ongkir) }}</span></div>
                  <div class="border-t pt-1 flex justify-between font-semibold">
                    <span>Total Harga</span><span>{{ fmt(summary.total) }}</span>
                  </div>
                </div>

                <!-- CTA (placeholder) -->
                <button class="w-full h-10 rounded-md bg-green-600 hover:bg-green-700 text-white text-sm font-medium">
                  Selesaikan Pembayaranmu
                </button>
              </div>

              <!-- KANAN: QRIS -->
              <aside class="rounded-xl border p-4">
                <p class="text-sm font-semibold text-center mb-2">QRIS</p>
                <img :src="Qris" alt="QRIS" class="w-full rounded-md object-contain" />
                <p class="text-[11px] text-gray-500 text-center mt-2">Scan QRIS untuk melakukan pembayaran</p>
              </aside>
            </div>
          </div>

          <section class="mt-6 rounded-xl border bg-green-50 p-5">
            <h3 class="text-sm font-semibold mb-4">Konfirmasi Pembayaran</h3>

            <div class="space-y-4">

              <!-- ID Transaksional (Opsional) -->
              <div>
                <label class="block text-xs font-semibold mb-1">ID Transaksional (Opsional)</label>
                <input
                  v-model="payForm.trx_id"
                  type="text"
                  placeholder="Nomor referensi membantu verifikasi pembayaran lebih cepat"
                  class="w-full rounded-md border border-green-200 bg-green-100/60 px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-green-400"
                />
                <p class="mt-1 text-[11px] text-green-700">
                  Nomor referensi membantu verifikasi pembayaran lebih cepat
                </p>
              </div>

              <!-- Bukti Pembayaran -->
              <div class="space-y-1">
                <label class="block text-xs font-semibold mb-1">
                  Bukti Pembayaran <span class="text-red-600">*</span>
                </label>
                <label class="flex items-center gap-3 w-full rounded-md border border-green-200 bg-green-100/60 px-3 py-2 text-xs cursor-pointer">
                  <input ref="proof" type="file" class="hidden" accept="image/*" @change="onFileChange" />
                  <button type="button"
                          @click="$refs.proof.click()"
                          class="px-3 py-1.5 rounded border border-green-300 bg-white text-green-700 text-[11px] font-medium">
                    Choose File
                  </button>
                  <span class="truncate text-[11px] text-gray-700" v-if="payForm.bukti_transfer">
                    {{ payForm.bukti_transfer.name }}
                  </span>
                  <span v-else class="text-[11px] text-gray-500">Belum ada file</span>
                </label>
                <ul class="mt-2 ml-1 space-y-1 text-[10px] text-gray-600">
                  <li>Format JPG, PNG, JPEG</li>
                  <li>Maksimal ukuran: 5 MB</li>
                  <li>Pastikan bukti pembayaran jelas dan dapat dibaca</li>
                </ul>
              </div>

              <!-- Checkbox Persetujuan -->
              <label class="flex items-start gap-2 text-[11px]">
                <input type="checkbox" v-model="payForm.agree" class="mt-[2px]" />
                <span class="text-red-600 font-medium leading-tight">
                  Saya menyetujui bahwa informasi yang saya berikan adalah benar dan saya
                  telah melakukan pembayaran sesuai dengan jumlah yang tertera
                </span>
              </label>

              <!-- Tombol Aksi -->
              <div class="space-y-3">
                <button
                  @click="submitPayment"
                  :disabled="payForm.processing || !canSubmit"
                  class="w-full h-10 rounded-md bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  Konfirmasi Pembayaran
                </button>

                <button
                  type="button"
                  @click="() => { payForm.reset(); }"
                  class="w-full h-10 rounded-md bg-red-600 hover:bg-red-700 text-white text-sm font-medium"
                >
                  Konfirmasi Pembayaran
                </button>
              </div>
            </div>
          </section>

        </div>
      </div>
    </main>
    <Footer />
  </div>
</template>