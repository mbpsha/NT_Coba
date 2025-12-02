<script setup>
import Header from '@/Components/User/Header.vue'
import Footer from '@/Components/User/Footer.vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref, onMounted, watch } from 'vue'

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

// state untuk dropdown lokasi
const provinces = ref([])
const cities = ref([])

const selectedProvinceId = ref('')
const selectedCityId = ref('')

const kecamatan = ref('')
const kelurahan = ref('')
const kodePos = ref('')

const loadingProvinces = ref(false)
const loadingCities = ref(false)

// Ambil provinsi dari backend → RajaOngkir
async function loadProvinces () {
  try {
    loadingProvinces.value = true
    const res = await fetch(route('api.provinces'))
    const data = await res.json()
    provinces.value = data.data.map(province => ({
      province_id: province.id,
      province_name: province.name
    }))
  } catch (e) {
    console.error('Gagal load provinsi', e)
  } finally {
    loadingProvinces.value = false
  }
}

// Ambil kota berdasarkan province_id
async function loadCities () {
  if (!selectedProvinceId.value) {
    cities.value = []
    selectedCityId.value = ''
    return
  }
  try {
    loadingCities.value = true
    // Ambil nama provinsi untuk dijadikan kata kunci (atau pakai input pengguna)
    const provName = provinces.value.find(p => String(p.province_id) === String(selectedProvinceId.value))?.province_name ?? ''
    const url = `/api/cities?search=${encodeURIComponent(provName)}`
    const res = await fetch(url)
    if (!res.ok) throw new Error(`HTTP ${res.status}`)
    const { data } = await res.json()
    // Sesuaikan shape item dari API (pakai id dan city_name)
    cities.value = (data ?? []).map(c => ({
      city_id: c.id,
      city_name: c.city_name,
      type: '' // jika tidak ada
    }))
  } catch (e) {
    console.error('Gagal load kota', e)
    cities.value = []
  } finally {
    loadingCities.value = false
  }
}

// Kalau provinsi berubah → reload kota
watch(selectedProvinceId, () => {
  loadCities();  // Memuat kota berdasarkan provinsi yang dipilih
})

function submit() {
  buildProvKab()

  form.post(route('checkout.address.save', { id_produk: props.id_produk }), {
    preserveScroll: true
  })
}

function buildProvKab () {
  const provinceName = provinces.value.find(p => String(p.province_id) === String(selectedProvinceId.value))?.province_name || ''
  const cityName = cities.value.find(c => String(c.city_id) === String(selectedCityId.value))?.city_name || ''

  const parts = [
    provinceName,
    cityName,
    kecamatan.value || null,
    kelurahan.value || null,
    kodePos.value || null
  ].filter(Boolean)

  form.prov_kab = parts.join(', ')
}

onMounted(() => {
  loadProvinces()
})
</script>

<template>
  <div class="min-h-screen text-gray-900 bg-gray-50">
    <Header />
    <Head title="Alamat Baru" />
    <main class="max-w-3xl px-4 pt-24 pb-16 mx-auto">
      <h1 class="mb-6 text-2xl font-semibold">Alamat Baru</h1>

      <div class="p-5 bg-gray-100 border shadow-sm rounded-xl">
        <form @submit.prevent="submit" class="space-y-4">
          <div>
            <label class="block mb-1 text-sm">Nama lengkap</label>
            <input v-model="form.nama" class="w-full px-3 py-2 bg-white border rounded-md" />
            <p v-if="form.errors.nama" class="mt-1 text-xs text-red-600">{{ form.errors.nama }}</p>
          </div>

          <div>
            <label class="block mb-1 text-sm">Nomor Telepon</label>
            <input v-model="form.phone" class="w-full px-3 py-2 bg-white border rounded-md" />
            <p v-if="form.errors.phone" class="mt-1 text-xs text-red-600">{{ form.errors.phone }}</p>
          </div>

          <!-- Dropdown untuk Provinsi -->
          <div>
            <label class="block mb-1 text-sm">Provinsi</label>
            <select
              v-model="selectedProvinceId"
              class="w-full px-3 py-2 bg-white border rounded-md"
            >
              <option value="" disabled>Pilih Provinsi</option>
              <option v-for="prov in provinces" :key="prov.province_id" :value="prov.province_id">
                {{ prov.province_name }}
              </option>
            </select>
            <p v-if="loadingProvinces" class="mt-1 text-[11px] text-gray-500">Memuat provinsi...</p>
          </div>

          <!-- KOTA / KABUPATEN -->
          <div>
            <label class="block mb-1 text-sm">Kota / Kabupaten</label>
            <select
              v-model="selectedCityId"
              :disabled="!selectedProvinceId || loadingCities"
              class="w-full px-3 py-2 bg-white border rounded-md disabled:bg-gray-100"
            >
              <option value="" disabled>
                {{ selectedProvinceId ? (loadingCities ? 'Memuat kota...' : 'Pilih Kota/Kabupaten') : 'Pilih provinsi terlebih dahulu' }}
              </option>
              <option v-for="city in cities" :key="city.city_id" :value="city.city_id">
                {{ city.type ? city.type + ' ' : '' }}{{ city.city_name }}
              </option>
            </select>
          </div>

          <!-- KECAMATAN -->
          <div>
            <label class="block mb-1 text-sm">Kecamatan</label>
            <input v-model="kecamatan" class="w-full px-3 py-2 bg-white border rounded-md" />
          </div>

          <!-- KELURAHAN / DESA -->
          <div>
            <label class="block mb-1 text-sm">Kelurahan / Desa</label>
            <input v-model="kelurahan" class="w-full px-3 py-2 bg-white border rounded-md" />
          </div>

          <!-- KODE POS -->
          <div>
            <label class="block mb-1 text-sm">Kode Pos</label>
            <input v-model="kodePos" class="w-full px-3 py-2 bg-white border rounded-md" />
          </div>

          <!-- Error dari backend untuk prov_kab (gabungan) -->
          <p v-if="form.errors.prov_kab" class="mt-1 text-xs text-red-600">{{ form.errors.prov_kab }}</p>

          <!-- JALAN -->
          <div>
            <label class="block mb-1 text-sm">Nama jalan, Gedung, No. Rumah</label>
            <input v-model="form.street" class="w-full px-3 py-2 bg-white border rounded-md" />
            <p v-if="form.errors.street" class="mt-1 text-xs text-red-600">{{ form.errors.street }}</p>
          </div>

          <div>
            <label class="block mb-1 text-sm">Detail lainnya (Cth : Blok / Unit No, patokan)</label>
            <input v-model="form.detail" class="w-full px-3 py-2 bg-white border rounded-md" />
            <p v-if="form.errors.detail" class="mt-1 text-xs text-red-600">{{ form.errors.detail }}</p>
          </div>

          <div class="flex items-center gap-3 pt-2">
            <button type="submit" :disabled="form.processing"
                    class="px-6 py-2 text-white bg-green-500 rounded-lg hover:bg-green-600 disabled:bg-gray-400">
              {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
            </button>
            <button type="button" @click="backToCheckout"
                    class="px-6 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
              Kembali ke Checkout
            </button>
          </div>
        </form>
      </div>
    </main>
    <Footer />
  </div>
</template>
