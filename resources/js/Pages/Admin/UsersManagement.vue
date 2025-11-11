<script setup>
import { ref } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import SidebarAdmin from '@/component/SidebarAdmin.vue'
import HeaderAdmin from '@/component/HeaderAdmin.vue'

const props = defineProps({
  users: { type: Object, required: true }
})

const page = usePage()

const showModal = ref(false)
const editMode = ref(false)
const selectedUser = ref(null)

const form = useForm({
  nama: '',
  username: '',
  email: '',
  no_telp: '',
  alamat: '',
  role: 'user',
  password: ''
})

function openCreateModal() {
  editMode.value = false
  selectedUser.value = null
  form.reset()
  form.role = 'user'
  showModal.value = true
}

function openEditModal(user) {
  editMode.value = true
  selectedUser.value = user
  form.nama = user.nama || ''
  form.username = user.username || ''
  form.email = user.email || ''
  form.no_telp = user.no_telp || ''
  form.alamat = user.alamat || ''
  form.role = user.role || 'user'
  form.password = ''
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  form.reset()
  selectedUser.value = null
}

function submitForm() {
  if (editMode.value && selectedUser.value) {
    form.put(route('admin.users.update', selectedUser.value.id_user), {
      preserveScroll: true,
      onSuccess: () => closeModal()
    })
  } else {
    form.post(route('admin.users.store'), {
      preserveScroll: true,
      onSuccess: () => closeModal()
    })
  }
}

function deleteUser(id) {
  if (confirm('Hapus pengguna ini?')) {
    form.delete(route('admin.users.destroy', id), { preserveScroll: true })
  }
}

const roleBadgeClass = (role) =>
  role === 'admin'
    ? 'bg-red-100 text-red-700'
    : 'bg-blue-100 text-blue-700'
</script>

<template>
  <div class="min-h-screen bg-gray-100">
    <SidebarAdmin />
    <HeaderAdmin />

    <main class="ml-64 pt-16 p-6">
      <!-- Banner judul -->
      <div class="rounded-2xl border border-green-200 bg-green-50 px-6 py-5 mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">Pengguna</h1>
        <div class="flex items-center gap-3">
          <button @click="openCreateModal"
                  class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-blue-600 text-white text-sm hover:bg-blue-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambahkan Pengguna
          </button>
        </div>
      </div>

      <!-- Tabel pengguna -->
      <div class="bg-white rounded-xl shadow border">
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="text-left bg-gray-50 text-gray-600">
                <th class="px-6 py-3 font-semibold">Nama</th>
                <th class="px-6 py-3 font-semibold">Email</th>
                <th class="px-6 py-3 font-semibold">Role</th>
                <th class="px-6 py-3 font-semibold text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="u in users.data" :key="u.id_user" class="border-t hover:bg-gray-50/60">
                <td class="px-6 py-4">{{ u.nama }}</td>
                <td class="px-6 py-4 text-gray-600">{{ u.email }}</td>
                <td class="px-6 py-4">
                  <span class="px-3 py-1 rounded-full text-xs font-semibold" :class="roleBadgeClass(u.role)">
                    {{ u.role === 'admin' ? 'Admin' : 'Pengguna' }}
                  </span>
                </td>
                <td class="px-6 py-3">
                  <div class="flex justify-center gap-2">
                    <button @click="openEditModal(u)" class="p-2 rounded-md border border-green-200 text-green-600 hover:bg-green-50"
                            title="Edit">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                    </button>
                    <button @click="deleteUser(u.id_user)" class="p-2 rounded-md border border-red-200 text-red-600 hover:bg-red-50"
                            title="Hapus">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>

              <tr v-if="!users.data?.length">
                <td colspan="4" class="px-6 py-10 text-center text-gray-500">Belum ada pengguna.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t flex justify-between items-center text-sm">
          <div class="text-gray-600">
            Menampilkan {{ users.from || 0 }}–{{ users.to || 0 }} dari {{ users.total || 0 }} data
          </div>
          <div class="flex gap-2">
            <button v-for="link in users.links" :key="link.label"
                    @click="link.url && $inertia.visit(link.url)"
                    :disabled="!link.url"
                    class="px-3 py-1 rounded"
                    :class="link.active ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'"
                    v-html="link.label" />
          </div>
        </div>
      </div>
    </main>

    <!-- Modal Tambah/Edit -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
      <div class="w-full max-w-lg bg-white rounded-xl shadow-lg">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <h2 class="text-lg font-semibold">
            {{ editMode ? 'Edit Pengguna' : 'Tambahkan Pengguna' }}
          </h2>
          <button @click="closeModal" class="p-2 rounded hover:bg-gray-100">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <form @submit.prevent="submitForm" class="px-6 py-5 space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs text-gray-600 mb-1">Nama</label>
              <input v-model="form.nama" type="text" required class="w-full rounded-md border px-3 py-2">
              <p v-if="form.errors.nama" class="text-xs text-red-600 mt-1">{{ form.errors.nama }}</p>
            </div>
            <div>
              <label class="block text-xs text-gray-600 mb-1">Username</label>
              <input v-model="form.username" type="text" required class="w-full rounded-md border px-3 py-2">
              <p v-if="form.errors.username" class="text-xs text-red-600 mt-1">{{ form.errors.username }}</p>
            </div>
            <div>
              <label class="block text-xs text-gray-600 mb-1">Email</label>
              <input v-model="form.email" type="email" required class="w-full rounded-md border px-3 py-2">
              <p v-if="form.errors.email" class="text-xs text-red-600 mt-1">{{ form.errors.email }}</p>
            </div>
            <div>
              <label class="block text-xs text-gray-600 mb-1">No. Telepon</label>
              <input v-model="form.no_telp" type="text" class="w-full rounded-md border px-3 py-2">
              <p v-if="form.errors.no_telp" class="text-xs text-red-600 mt-1">{{ form.errors.no_telp }}</p>
            </div>
            <div class="md:col-span-2">
              <label class="block text-xs text-gray-600 mb-1">Alamat</label>
              <input v-model="form.alamat" type="text" class="w-full rounded-md border px-3 py-2">
              <p v-if="form.errors.alamat" class="text-xs text-red-600 mt-1">{{ form.errors.alamat }}</p>
            </div>
            <div>
              <label class="block text-xs text-gray-600 mb-1">Role</label>
              <select v-model="form.role" class="w-full rounded-md border px-3 py-2">
                <option value="user">Pengguna</option>
                <option value="admin">Admin</option>
              </select>
              <p v-if="form.errors.role" class="text-xs text-red-600 mt-1">{{ form.errors.role }}</p>
            </div>
            <div>
              <label class="block text-xs text-gray-600 mb-1">
                Password {{ editMode ? '(kosongkan jika tidak diubah)' : '' }}
              </label>
              <input v-model="form.password" :required="!editMode" type="password" class="w-full rounded-md border px-3 py-2">
              <p v-if="form.errors.password" class="text-xs text-red-600 mt-1">{{ form.errors.password }}</p>
            </div>
          </div>

          <div class="mt-2 flex justify-end gap-3">
            <button type="button" @click="closeModal" class="px-4 py-2 rounded-md bg-gray-200 text-gray-700 hover:bg-gray-300">
              Batal
            </button>
            <button type="submit" :disabled="form.processing"
                    class="px-4 py-2 rounded-md bg-green-600 text-white hover:bg-green-700 disabled:opacity-60">
              {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
