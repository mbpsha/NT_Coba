<script setup>
import { Link, useForm, usePage, router } from '@inertiajs/vue3'
import { ref, watch, onMounted, computed } from 'vue'
import Logo from '*/dashboard/logo-ngundur.png'

const page = usePage()
const checkoutIntent = page.props.checkoutIntent
const needAddress = page.props.needAddress
const user = page.props.user ?? page.props.auth?.user ?? {}
const initialAddress = page.props.address_detail ?? {}

const initialAddressExists = Object.keys(initialAddress ?? {}).length > 0

const form = useForm({
  nama: user.nama || '',
  username: user.username || '',
  email: user.email || '',
  no_telp: user.no_telp || '',
  alamat: user.alamat || '',
  province_id: initialAddress.province_id ?? null,
  city_id: initialAddress.city_id ?? null,
  kecamatan: initialAddress.kecamatan ?? '',
  kelurahan: initialAddress.kelurahan ?? '',
  nama_jalan: initialAddress.nama_jalan ?? '',
  no_rumah: initialAddress.no_rumah ?? '',
  kode_pos: initialAddress.kode_pos ?? '',
  catatan: initialAddress.catatan ?? '',
  checkout_return: checkoutIntent ? 1 : 0,
  checkout_product_id: checkoutIntent?.id_produk || null,
  checkout_qty: checkoutIntent?.qty || 1,
})

const showSavedToast = ref(false)
const showAddressForm = ref(!initialAddressExists)

const provinces = ref(page.props.address_options?.provinces ?? [])
const cities = ref(page.props.address_options?.cities ?? [])
const loadingProvinces = ref(false)
const loadingCities = ref(false)
const selectedProvinceId = ref(form.province_id || null)
const selectedCityId = ref(form.city_id || null)

const fallbackProvinceName = () => {
  if (!initialAddress) return ''
  if (!form.province_id) return initialAddress.provinsi ?? initialAddress.province_name ?? ''
  if (initialAddress.province_id && initialAddress.province_id === form.province_id) {
    return initialAddress.provinsi ?? initialAddress.province_name ?? ''
  }
  return ''
}

const fallbackCityName = () => {
  if (!initialAddress) return ''
  if (!form.city_id) return initialAddress.kabupaten ?? initialAddress.city_name ?? initialAddress.city ?? ''
  if (initialAddress.city_id && initialAddress.city_id === form.city_id) {
    return initialAddress.kabupaten ?? initialAddress.city_name ?? initialAddress.city ?? ''
  }
  return ''
}

const formattedAddress = computed(() => {
  const provinceName =
    provinces.value.find(p => p.id === selectedProvinceId.value)?.name ||
    fallbackProvinceName()

  const cityName =
    cities.value.find(c => c.id === selectedCityId.value)?.name ||
    fallbackCityName()

  const parts = [
    form.nama_jalan,
    form.no_rumah,
    form.kelurahan,
    form.kecamatan,
    cityName,
    provinceName,
    form.kode_pos ? `Kode Pos ${form.kode_pos}` : '',
  ].filter(Boolean)

  return parts.join(', ')
})

const storedAddressSummary = computed(() => {
  if (!initialAddressExists) return ''
  const parts = [
    initialAddress.nama_jalan,
    initialAddress.no_rumah,
    initialAddress.kelurahan,
    initialAddress.kecamatan,
    initialAddress.kabupaten ?? initialAddress.city_name ?? initialAddress.city ?? '',
    initialAddress.provinsi ?? initialAddress.province_name ?? '',
    initialAddress.kode_pos ? `Kode Pos ${initialAddress.kode_pos}` : '',
    initialAddress.catatan,
  ].filter(Boolean)

  return parts.join(', ')
})

watch(selectedProvinceId, async (newVal) => {
  form.province_id = newVal || null
  cities.value = []
  selectedCityId.value = null
  form.city_id = null
  if (!newVal) return
  await loadCities(newVal)
})

watch(selectedCityId, (newVal) => {
  form.city_id = newVal || null
})

const onPhoneInput = (e) => {
  form.no_telp = (e.target.value || '').replace(/\D+/g, '')
}

const ensureListsLoaded = async () => {
  if (!provinces.value.length) {
    await loadProvinces()
  }
  if (selectedProvinceId.value && !cities.value.length) {
    await loadCities(selectedProvinceId.value)
  }
}

const openAddressForm = async () => {
  showAddressForm.value = true
  await ensureListsLoaded()
}

const save = () => {
  form.no_telp = (form.no_telp || '').replace(/\D+/g, '')
  const preservedAddress = storedAddressSummary.value || initialAddress.alamat_lengkap || initialAddress.alamat || form.alamat || ''
  form.alamat = showAddressForm.value ? formattedAddress.value : preservedAddress

  form.put(route('profile.update'), {
    preserveScroll: true,
    onSuccess: () => {
      showSavedToast.value = true
      setTimeout(() => { showSavedToast.value = false }, 2500)
    }
  })
}

const goBack = () => {
  if (window.history.length > 1) {
    window.history.back()
  } else {
    try {
      router.visit(route('berita'))
    } catch {
      router.visit('/berita')
    }
  }
}

const backToCheckout = () => {
  const addressString = formattedAddress.value || storedAddressSummary.value
  if (!addressString) {
    alert('Isi alamat terlebih dahulu.')
    return
  }
  const pid = checkoutIntent.id_produk
  const qty = checkoutIntent.qty || 1
  router.visit(`/checkout/${pid}?qty=${qty}`)
}

const loadProvinces = async () => {
  if (provinces.value.length) return
  loadingProvinces.value = true
  try {
    const res = await fetch('/api/provinces')
    const data = await res.json()
    provinces.value = (data.data || data).map(item => ({
      id: item.id ?? item.province_id ?? item.code,
      name: item.name ?? item.province ?? item.label,
    }))
  } catch (e) {
    console.error('Gagal memuat provinsi', e)
  } finally {
    loadingProvinces.value = false
  }
}

const loadCities = async (provinceId) => {
  if (!provinceId) return
  loadingCities.value = true
  try {
    const res = await fetch(`/api/cities?province_id=${provinceId}`)
    const data = await res.json()
    cities.value = (data.data || data).map(item => ({
      id: item.id ?? item.city_id ?? item.code,
      name: item.name ?? item.city ?? item.label,
    }))
  } catch (e) {
    console.error('Gagal memuat kota/kabupaten', e)
  } finally {
    loadingCities.value = false
  }
}

onMounted(async () => {
  if (showAddressForm.value) {
    await ensureListsLoaded()
  }
})
</script>

<template>
  <div class="flex flex-col min-h-screen bg-slate-50">
    <header class="flex items-center justify-between h-16 px-4 shadow-sm bg-white/80 backdrop-blur">
      <Link :href="route('dashboard')" class="flex items-center gap-2">
        <img :src="Logo" alt="NGUNDUR" class="h-14" />
      </Link>
    </header>

    <main class="flex-1 w-full max-w-3xl p-6 mx-auto space-y-4">
      <h1 class="text-2xl font-semibold text-center">Profil Saya</h1>

      <div
        v-if="needAddress && checkoutIntent"
        class="px-4 py-3 text-sm text-red-700 border border-red-300 rounded bg-red-50"
      >
        *Isikan alamat anda sebelum melakukan Checkout Produk
      </div>

      <div v-if="$page.props.flash?.success" class="px-4 py-2 text-green-800 bg-green-100 rounded">
        {{ $page.props.flash.success }}
      </div>

      <section class="p-6 border border-green-200 shadow bg-green-50/70 rounded-xl space-y-4">
        <form @submit.prevent="save" class="grid grid-cols-1 gap-4">
          <div>
            <label class="block mb-1 text-sm">Username</label>
            <input
              v-model="form.username"
              class="w-full px-3 py-2 border border-green-200 rounded-md bg-white/80"
            />
            <p v-if="form.errors.username" class="mt-1 text-sm text-red-600">{{ form.errors.username }}</p>
          </div>

          <div>
            <label class="block mb-1 text-sm">Nama</label>
            <input
              v-model="form.nama"
              class="w-full px-3 py-2 border border-green-200 rounded-md bg-white/80"
              placeholder="Opsional"
            />
            <p v-if="form.errors.nama" class="mt-1 text-sm text-red-600">{{ form.errors.nama }}</p>
          </div>

          <div>
            <label class="block mb-1 text-sm">Email</label>
            <input
              v-model="form.email"
              type="email"
              class="w-full px-3 py-2 border border-green-200 rounded-md bg-white/80"
            />
            <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
          </div>

          <div>
            <label class="block mb-1 text-sm">Nomor Telepon</label>
            <input
              v-model="form.no_telp"
              inputmode="numeric"
              pattern="[0-9]*"
              maxlength="20"
              @input="onPhoneInput"
              class="w-full px-3 py-2 border border-green-200 rounded-md bg-white/80"
            />
            <p v-if="form.errors.no_telp" class="mt-1 text-sm text-red-600">{{ form.errors.no_telp }}</p>
          </div>

          <div class="space-y-3">
            <div class="flex items-center justify-between">
              <label class="block text-sm">Alamat Lengkap</label>
              <button
                v-if="!showAddressForm"
                type="button"
                class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-medium text-white bg-green-600 rounded hover:bg-green-700"
                @click="openAddressForm"
              >
                <span class="text-lg leading-none">＋</span>
                Ubah / Tambah Alamat
              </button>
            </div>

            <div
              v-if="!showAddressForm && storedAddressSummary"
              class="p-4 bg-white border border-green-200 rounded-lg text-sm text-gray-700 leading-relaxed"
            >
              {{ storedAddressSummary }}
            </div>

            <button
              v-else-if="!showAddressForm"
              type="button"
              class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-green-600 rounded hover:bg-green-700"
              @click="openAddressForm"
            >
              <span class="text-lg leading-none">＋</span>
              Tambah Alamat Baru
            </button>

            <div
              v-if="showAddressForm"
              class="grid gap-3 p-4 bg-white/80 border border-green-200 rounded-lg"
            >
              <div>
                <label class="block mb-1 text-xs text-gray-600">Provinsi *</label>
                <select
                  v-model="selectedProvinceId"
                  :disabled="loadingProvinces"
                  class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-green-500"
                >
                  <option :value="null">{{ loadingProvinces ? 'Memuat...' : 'Pilih Provinsi' }}</option>
                  <option v-for="prov in provinces" :key="prov.id" :value="prov.id">
                    {{ prov.name }}
                  </option>
                </select>
                <p v-if="form.errors.province_id" class="mt-1 text-xs text-red-600">
                  {{ form.errors.province_id }}
                </p>
              </div>

              <div>
                <label class="block mb-1 text-xs text-gray-600">Kota / Kabupaten *</label>
                <select
                  v-model="selectedCityId"
                  :disabled="!selectedProvinceId || loadingCities"
                  class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-green-500"
                >
                  <option :value="null">{{ loadingCities ? 'Memuat...' : 'Pilih Kota/Kabupaten' }}</option>
                  <option v-for="city in cities" :key="city.id" :value="city.id">
                    {{ city.name }}
                  </option>
                </select>
                <p v-if="form.errors.city_id" class="mt-1 text-xs text-red-600">{{ form.errors.city_id }}</p>
              </div>

              <div>
                <label class="block mb-1 text-xs text-gray-600">Kecamatan *</label>
                <input
                  v-model="form.kecamatan"
                  required
                  class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-green-500"
                />
                <p v-if="form.errors.kecamatan" class="mt-1 text-xs text-red-600">{{ form.errors.kecamatan }}</p>
              </div>

              <div>
                <label class="block mb-1 text-xs text-gray-600">Kelurahan / Desa</label>
                <input
                  v-model="form.kelurahan"
                  class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-green-500"
                />
                <p v-if="form.errors.kelurahan" class="mt-1 text-xs text-red-600">{{ form.errors.kelurahan }}</p>
              </div>

              <div class="grid gap-3 sm:grid-cols-2">
                <div>
                  <label class="block mb-1 text-xs text-gray-600">Nama Jalan *</label>
                  <input
                    v-model="form.nama_jalan"
                    required
                    class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-green-500"
                  />
                  <p v-if="form.errors.nama_jalan" class="mt-1 text-xs text-red-600">{{ form.errors.nama_jalan }}</p>
                </div>
                <div>
                  <label class="block mb-1 text-xs text-gray-600">No. Rumah / Gedung</label>
                  <input
                    v-model="form.no_rumah"
                    class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-green-500"
                  />
                  <p v-if="form.errors.no_rumah" class="mt-1 text-xs text-red-600">{{ form.errors.no_rumah }}</p>
                </div>
              </div>

              <div>
                <label class="block mb-1 text-xs text-gray-600">Kode Pos *</label>
                <input
                  v-model="form.kode_pos"
                  maxlength="5"
                  pattern="[0-9]{5}"
                  required
                  class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-green-500"
                />
                <p v-if="form.errors.kode_pos" class="mt-1 text-xs text-red-600">{{ form.errors.kode_pos }}</p>
              </div>

              <div>
                <label class="block mb-1 text-xs text-gray-600">Catatan Tambahan</label>
                <textarea
                  v-model="form.catatan"
                  rows="2"
                  class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-green-500"
                ></textarea>
                <p v-if="form.errors.catatan" class="mt-1 text-xs text-red-600">{{ form.errors.catatan }}</p>
              </div>

              <div class="text-xs text-gray-500 border-t pt-2">
                <p><strong>Alamat Preview:</strong></p>
                <p>{{ formattedAddress || 'Lengkapi alamat untuk menampilkan preview.' }}</p>
              </div>
            </div>

            <p v-if="form.errors.alamat" class="mt-1 text-sm text-red-600">{{ form.errors.alamat }}</p>
          </div>

          <div class="flex flex-wrap gap-3 mt-2">
            <button
              type="submit"
              :disabled="form.processing"
              class="px-5 py-2 text-white bg-green-600 rounded-lg hover:bg-green-700 disabled:opacity-60"
            >
              {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
            </button>

            <button
              type="button"
              @click="goBack"
              class="px-4 py-2 text-sm text-white bg-red-600 rounded-lg hover:bg-red-700"
            >
              Kembali
            </button>

            <button
              v-if="checkoutIntent"
              type="button"
              @click="backToCheckout"
              class="px-5 py-2 text-green-700 bg-green-100 rounded-lg hover:bg-green-200 disabled:opacity-60"
              :disabled="!(formattedAddress || storedAddressSummary)"
            >
              Kembali ke Checkout
            </button>
          </div>
        </form>
      </section>
    </main>

    <transition name="fade">
      <div
        v-if="showSavedToast"
        class="fixed bottom-6 left-1/2 -translate-x-1/2 px-4 py-2 text-sm text-white bg-green-600 rounded-md shadow"
        role="alert"
      >
        Informasi profil berhasil disimpan.
      </div>
    </transition>
  </div>
</template>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>