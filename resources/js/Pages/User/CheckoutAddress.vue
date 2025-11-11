<script setup>
import Header from '@/component/Header.vue'
import Footer from '@/component/Footer.vue'
import { Head, useForm, router } from '@inertiajs/vue3'

const props = defineProps({
  id_produk: { type: Number, required: true },
  qty: { type: Number, default: 1 },
  prefill: { type: Object, default: () => ({}) }
})

const form = useForm({
  nama: props.prefill.nama || '',
  phone: props.prefill.phone || '',
  prov_kab: props.prefill.prov_kab || '',
  street: props.prefill.street || '',
  detail: props.prefill.detail || '',
  qty: props.qty
})

function submit() {
  form.post(route('checkout.address.save', { id_produk: props.id_produk }), {
    preserveScroll: true
  })
}

function backToCheckout() {
  router.visit(route('checkout', { id_produk: props.id_produk }) + `?qty=${props.qty}`)
}
</script>

<template>
  <div class="min-h-screen bg-gray-50 text-gray-900">
    <Header />
    <Head title="Alamat Baru" />
    <main class="max-w-3xl mx-auto pt-24 pb-16 px-4">
      <h1 class="text-2xl font-semibold mb-6">Alamat Baru</h1>

      <div class="rounded-xl bg-gray-100 border p-5 shadow-sm">
        <form @submit.prevent="submit" class="space-y-4">
          <div>
            <label class="block text-sm mb-1">Nama lengkap</label>
            <input v-model="form.nama" class="w-full rounded-md bg-white border px-3 py-2" />
            <p v-if="form.errors.nama" class="text-xs text-red-600 mt-1">{{ form.errors.nama }}</p>
          </div>

          <div>
            <label class="block text-sm mb-1">Nomor Telepon</label>
            <input v-model="form.phone" class="w-full rounded-md bg-white border px-3 py-2" />
            <p v-if="form.errors.phone" class="text-xs text-red-600 mt-1">{{ form.errors.phone }}</p>
          </div>

          <div>
            <label class="block text-sm mb-1">Provinsi, Kota, Kecamatan, Kode pos</label>
            <input v-model="form.prov_kab" class="w-full rounded-md bg-white border px-3 py-2" />
            <p v-if="form.errors.prov_kab" class="text-xs text-red-600 mt-1">{{ form.errors.prov_kab }}</p>
          </div>

          <div>
            <label class="block text-sm mb-1">Nama jalan, Gedung, No. Rumah</label>
            <input v-model="form.street" class="w-full rounded-md bg-white border px-3 py-2" />
            <p v-if="form.errors.street" class="text-xs text-red-600 mt-1">{{ form.errors.street }}</p>
          </div>

          <div>
            <label class="block text-sm mb-1">Detail lainnya (Cth : Blok / Unit No, patokan)</label>
            <input v-model="form.detail" class="w-full rounded-md bg-white border px-3 py-2" />
            <p v-if="form.errors.detail" class="text-xs text-red-600 mt-1">{{ form.errors.detail }}</p>
          </div>

          <div class="pt-2 flex items-center gap-3">
            <button type="submit" :disabled="form.processing"
                    class="px-6 py-2 rounded-lg bg-green-500 hover:bg-green-600 text-white">
              {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
            </button>
            <button type="button" @click="backToCheckout"
                    class="px-6 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-700">
              Kembali ke Checkout
            </button>
          </div>
        </form>
      </div>
    </main>
    <Footer />
  </div>
</template>