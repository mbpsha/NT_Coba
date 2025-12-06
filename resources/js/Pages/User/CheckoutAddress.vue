<script setup>
import { ref, watch } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import Header from '@/Components/User/Header.vue'
import Footer from '@/Components/User/Footer.vue'

const props = defineProps({
  id_produk: { type: [Number, String], required: true },
  qty: { type: Number, default: 1 },
  prefill: { type: Object, default: () => ({}) },
  from_cart: { type: Boolean, default: false }
})

const provinces = ref([])
const cities = ref([])
const loadingProvinces = ref(false)
const loadingCities = ref(false)

const selectedProvinceId = ref(null)
const selectedCityId = ref(null)

const form = useForm({
  nama: props.prefill.nama || '',
  phone: props.prefill.phone || '',
  provinsi: '',
  kabupaten: '',
  kecamatan: '',
  kelurahan_desa: '',
  kode_pos: '',
  city_id: props.prefill.city_id || null,
  nama_jalan: '',
  no_rumah: '',
  detail: '',
  qty: props.qty,
  from_cart: props.from_cart
})

// Load provinces on mount
async function loadProvinces() {
  loadingProvinces.value = true
  try {
    const res = await fetch('/api/provinces')
    if (!res.ok) throw new Error('Failed to load provinces')
    const json = await res.json()
    const data = json.data || json
    provinces.value = data.map(p => ({ id: p.id, name: p.name }))
  } catch (err) {
    console.error('Error loading provinces:', err)
    alert('Gagal memuat provinsi: ' + err.message)
  } finally {
    loadingProvinces.value = false
  }
}

// Load cities when province selected
watch(selectedProvinceId, async (newVal) => {
  cities.value = []
  selectedCityId.value = null
  form.kabupaten = ''
  form.city_id = null

  if (!newVal) return

  loadingCities.value = true
  try {
    const res = await fetch(`/api/cities?province_id=${newVal}`)
    if (!res.ok) throw new Error('Failed to load cities')
    const json = await res.json()
    const data = json.data || json
    cities.value = data.map(c => ({ id: c.id, name: c.name }))
  } catch (err) {
    console.error('Error loading cities:', err)
    alert('Gagal memuat kota/kabupaten: ' + err.message)
  } finally {
    loadingCities.value = false
  }
})

function buildProvKab() {
  const prov = provinces.value.find(p => p.id === selectedProvinceId.value)
  const city = cities.value.find(c => c.id === selectedCityId.value)

  if (prov) form.provinsi = prov.name
  if (city) {
    form.kabupaten = city.name
    form.city_id = selectedCityId.value
  }
}

function submit() {
  buildProvKab()

  if (!form.provinsi || !form.kabupaten) {
    alert('Pilih provinsi dan kota/kabupaten terlebih dahulu')
    return
  }

  form.post(route('checkout.address.save', { id_produk: props.id_produk }), {
    preserveScroll: true,
    onError: (errors) => {
      console.error('Validation errors:', errors)
      alert('Gagal menyimpan alamat. Periksa data Anda.')
    }
  })
}

function backToCheckout() {
  router.visit(route('checkout.show', { id_produk: props.id_produk, qty: props.qty }))
}

// Load provinces on mount
loadProvinces()
</script>

<template>
  <div class="min-h-screen text-gray-900 bg-gray-100 font-inter">
    <Header />
    <Head title="Tambah Alamat" />

    <main class="max-w-2xl px-4 pt-24 pb-16 mx-auto">
      <div class="flex items-center justify-between mb-4">
        <h1 class="text-lg font-semibold">Tambah Alamat Pengiriman</h1>
        <button @click="backToCheckout"
                class="px-4 py-2 text-sm text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
          Kembali
        </button>
      </div>

      <div class="p-6 bg-white rounded-lg shadow">
        <form @submit.prevent="submit" class="space-y-4">
          <!-- Nama Penerima -->
          <div>
            <label class="block mb-1 text-sm font-medium">Nama Penerima <span class="text-red-500">*</span></label>
            <input v-model="form.nama" type="text" required
                   class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-green-500"
                   placeholder="Nama lengkap penerima" />
            <p v-if="form.errors.nama" class="mt-1 text-xs text-red-600">{{ form.errors.nama }}</p>
          </div>

          <!-- No Telepon -->
          <div>
            <label class="block mb-1 text-sm font-medium">No. Telepon <span class="text-red-500">*</span></label>
            <input v-model="form.phone" type="tel" required
                   class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-green-500"
                   placeholder="08xxxxxxxxxx" />
            <p v-if="form.errors.phone" class="mt-1 text-xs text-red-600">{{ form.errors.phone }}</p>
          </div>

          <!-- Provinsi -->
          <div>
            <label class="block mb-1 text-sm font-medium">Provinsi <span class="text-red-500">*</span></label>
            <select v-model="selectedProvinceId" required :disabled="loadingProvinces"
                    class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-green-500">
              <option :value="null">{{ loadingProvinces ? 'Memuat...' : 'Pilih Provinsi' }}</option>
              <option v-for="prov in provinces" :key="prov.id" :value="prov.id">{{ prov.name }}</option>
            </select>
            <p v-if="form.errors.provinsi" class="mt-1 text-xs text-red-600">{{ form.errors.provinsi }}</p>
          </div>

          <!-- Kota/Kabupaten -->
          <div>
            <label class="block mb-1 text-sm font-medium">Kota/Kabupaten <span class="text-red-500">*</span></label>
            <select v-model="selectedCityId" required :disabled="!selectedProvinceId || loadingCities"
                    class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-green-500">
              <option :value="null">{{ loadingCities ? 'Memuat...' : 'Pilih Kota/Kabupaten' }}</option>
              <option v-for="city in cities" :key="city.id" :value="city.id">{{ city.name }}</option>
            </select>
            <p v-if="form.errors.kabupaten" class="mt-1 text-xs text-red-600">{{ form.errors.kabupaten }}</p>
          </div>

          <!-- Kecamatan -->
          <div>
            <label class="block mb-1 text-sm font-medium">Kecamatan <span class="text-red-500">*</span></label>
            <input v-model="form.kecamatan" type="text" required
                   class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-green-500"
                   placeholder="Nama kecamatan" />
            <p v-if="form.errors.kecamatan" class="mt-1 text-xs text-red-600">{{ form.errors.kecamatan }}</p>
          </div>

          <!-- Kelurahan/Desa -->
          <div>
            <label class="block mb-1 text-sm font-medium">Kelurahan/Desa</label>
            <input v-model="form.kelurahan_desa" type="text"
                   class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-green-500"
                   placeholder="Nama kelurahan/desa" />
          </div>

          <!-- Nama Jalan -->
          <div>
            <label class="block mb-1 text-sm font-medium">Nama Jalan</label>
            <input v-model="form.nama_jalan" type="text"
                   class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-green-500"
                   placeholder="Contoh: Jl. Sudirman" />
          </div>

          <!-- No Rumah -->
          <div>
            <label class="block mb-1 text-sm font-medium">No. Rumah/Gedung</label>
            <input v-model="form.no_rumah" type="text"
                   class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-green-500"
                   placeholder="Contoh: No. 123, Blok A" />
          </div>

          <!-- Kode Pos -->
          <div>
            <label class="block mb-1 text-sm font-medium">Kode Pos <span class="text-red-500">*</span></label>
            <input v-model="form.kode_pos" type="text" required pattern="[0-9]{5}"
                   class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-green-500"
                   placeholder="5 digit kode pos" maxlength="5" />
            <p v-if="form.errors.kode_pos" class="mt-1 text-xs text-red-600">{{ form.errors.kode_pos }}</p>
          </div>

          <!-- Detail/Catatan -->
          <div>
            <label class="block mb-1 text-sm font-medium">Catatan Tambahan</label>
            <textarea v-model="form.detail" rows="3"
                      class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-green-500"
                      placeholder="Patokan/petunjuk alamat (opsional)"></textarea>
          </div>

          <!-- Submit Button -->
          <div class="flex gap-3 pt-4">
            <button type="button" @click="backToCheckout"
                    class="flex-1 px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
              Batal
            </button>
            <button type="submit" :disabled="form.processing"
                    class="flex-1 px-4 py-2 text-white bg-green-600 rounded-md hover:bg-green-700 disabled:opacity-50">
              {{ form.processing ? 'Menyimpan...' : 'Simpan Alamat' }}
            </button>
          </div>
        </form>
      </div>
    </main>

    <Footer />
  </div>
</template>