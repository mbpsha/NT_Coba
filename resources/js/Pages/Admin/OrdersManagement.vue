<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import SidebarAdmin from '@/component/SidebarAdmin.vue'
import HeaderAdmin from '@/component/HeaderAdmin.vue'

const props = defineProps({ orders: Object })

const showDetailModal = ref(false)
const selectedOrder = ref(null)

const form = useForm({ status: '' })

const statusOptions = [
  { value: 'diproses', label: 'Dalam Produksi' },
  { value: 'dikirim',  label: 'Dalam Pengiriman' },
  { value: 'selesai',  label: 'Selesai' },
]

function openDropdown(order) {
  selectedOrder.value = order
  form.status = order.status || 'production'
  showDetailModal.value = true
}

function closeModal() {
  showDetailModal.value = false
  selectedOrder.value = null
}

function saveStatus() {
  form.put(route('admin.orders.updateStatus', selectedOrder.value.id_order), {
    preserveScroll: true,
    onSuccess: () => closeModal()
  })
}
</script>

<template>
  <div class="min-h-screen bg-gray-100">
    <SidebarAdmin />
    <HeaderAdmin />

    <main class="ml-64 pt-16 p-8">
      <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50 text-left">
            <tr>
              <th class="px-6 py-3">Nama</th>
              <th class="px-6 py-3">Email</th>
              <th class="px-6 py-3">Alamat</th>
              <th class="px-6 py-3">Tanggal</th>
              <th class="px-6 py-3">Status</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="o in props.orders.data" :key="o.id_order" class="border-t">
              <td class="px-6 py-3">{{ o.user?.name }}</td>
              <td class="px-6 py-3">{{ o.user?.email }}</td>
              <td class="px-6 py-3">{{ o.address_text }}</td>
              <td class="px-6 py-3">{{ o.created_at }}</td>
              <td class="px-6 py-3">
                <button @click="openDropdown(o)" class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-700">
                  {{ (statusOptions.find(s=>s.value===o.status)?.label) || 'Dalam Produksi' }}
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </main>

    <!-- Modal -->
    <div v-if="showDetailModal" class="fixed inset-0 bg-black/40 grid place-items-center z-50">
      <div class="bg-white rounded-xl w-full max-w-md p-6">
        <h3 class="text-lg font-semibold mb-4">Ubah Status</h3>
        <select v-model="form.status" class="w-full border rounded-md p-2">
          <option v-for="opt in statusOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
        </select>
        <div class="mt-6 flex justify-end gap-2">
          <button @click="closeModal" class="px-4 py-2 border rounded-md">Batal</button>
          <button @click="saveStatus" class="px-4 py-2 rounded-md bg-green-600 text-white">Simpan</button>
        </div>
      </div>
    </div>
  </div>
</template>
