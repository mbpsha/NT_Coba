<template>
  <!-- Admin Products Upload Page -->
  <div class="min-h-screen bg-gray-100 flex">
    <!-- Sidebar -->
    <AdminSidebar />

    <main class="flex-1 flex flex-col min-w-0">
      <AdminHeader />
      <section class="flex-1 p-4 sm:p-6 lg:p-8 bg-green-100/70">
      <!-- Title -->
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Tambah Produk</h1>
        <RouterLink to="/admin" class="text-sm text-green-700 hover:underline">Kembali ke Dashboard</RouterLink>
      </div>

      <!-- Form card -->
      <div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Image uploader -->
          <div class="flex flex-col items-center">
            <div class="w-48 h-48 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center overflow-hidden">
              <img v-if="preview" :src="preview" alt="Preview" class="object-cover w-full h-full" />
              <span v-else class="text-gray-400 text-sm">Gambar Produk</span>
            </div>
            <label class="mt-3 inline-flex items-center gap-2 px-3 py-2 text-sm rounded-md bg-green-100 text-green-700 hover:bg-green-200 cursor-pointer">
              <input type="file" class="hidden" accept="image/*" @change="onFile" />
              Upload foto
            </label>
          </div>

          <!-- Fields -->
          <div class="space-y-4">
            <div>
              <label class="block text-sm text-gray-600">Deskripsi</label>
              <textarea v-model="form.description" class="mt-1 w-full rounded-md border-gray-300 focus:ring-green-500 focus:border-green-500" rows="2"></textarea>
            </div>
            <div>
              <label class="block text-sm text-gray-600">Nama Alat</label>
              <input v-model="form.name" type="text" class="mt-1 w-full rounded-md border-gray-300 focus:ring-green-500 focus:border-green-500" required />
            </div>
            <div>
              <label class="block text-sm text-gray-600">Stok</label>
              <input v-model.number="form.stock" type="number" min="0" class="mt-1 w-full rounded-md border-gray-300 focus:ring-green-500 focus:border-green-500" />
            </div>
            <div>
              <label class="block text-sm text-gray-600">Harga</label>
              <input v-model.number="form.price" type="number" min="0" step="0.01" class="mt-1 w-full rounded-md border-gray-300 focus:ring-green-500 focus:border-green-500" required />
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm text-gray-600">Tanggal Launching</label>
                <input v-model="form.launch_date" type="date" class="mt-1 w-full rounded-md border-gray-300 focus:ring-green-500 focus:border-green-500" />
              </div>
              <div>
                <label class="block text-sm text-gray-600">Estimasi Tiba</label>
                <input v-model="form.eta_date" type="date" class="mt-1 w-full rounded-md border-gray-300 focus:ring-green-500 focus:border-green-500" />
              </div>
            </div>
            <div class="flex justify-end">
              <button :disabled="submitting" class="px-5 py-2 rounded-md bg-green-500 text-white hover:bg-green-600 disabled:opacity-50">{{ submitting ? 'Menyimpan...' : 'Upload' }}</button>
            </div>
          </div>
        </form>
      </div>

      <!-- Success banner -->
      <div v-if="success" class="mt-4 p-3 rounded-md bg-green-50 text-green-700 text-sm">
        Produk berhasil ditambahkan.
      </div>

      <div v-if="error" class="mt-4 p-3 rounded-md bg-red-50 text-red-700 text-sm">
        {{ error }}
      </div>
      </section>
    </main>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import axios from 'axios'
import AdminSidebar from '../../components/AdminSidebar.vue'
import AdminHeader from '../../components/AdminHeader.vue'

const router = useRouter()
const form = reactive({ name: '', description: '', stock: 0, price: 0, launch_date: '', eta_date: '' })
const file = ref(null)
const preview = ref('')
const submitting = ref(false)
const success = ref(false)
const error = ref('')

function onFile(e) {
  const f = e.target.files[0]
  file.value = f
  if (f) {
    const reader = new FileReader()
    reader.onload = () => { preview.value = reader.result }
    reader.readAsDataURL(f)
  } else {
    preview.value = ''
  }
}

async function submit() {
  try {
    submitting.value = true
    error.value = ''
    const fd = new FormData()
    Object.entries(form).forEach(([k,v]) => fd.append(k, v ?? ''))
    if (file.value) fd.append('image', file.value)
    await axios.post('/api/products', fd, { headers: { 'Content-Type': 'multipart/form-data' } })
    success.value = true
    // Reset
    Object.assign(form, { name: '', description: '', stock: 0, price: 0, launch_date: '', eta_date: '' })
    file.value = null
    preview.value = ''
    // Notify other pages
    window.dispatchEvent(new CustomEvent('product:created'))
  } catch (e) {
    error.value = e?.response?.data?.message || 'Gagal menyimpan produk'
  } finally {
    submitting.value = false
  }
}
</script>
