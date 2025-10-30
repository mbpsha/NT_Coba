<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import SidebarAdmin from '@/Components/Admin/SidebarAdmin.vue'
import HeaderAdmin from '@/Components/Admin/HeaderAdmin.vue'

const props = defineProps({
    users: Object
})

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
    form.reset()
    showModal.value = true
}

function openEditModal(user) {
    editMode.value = true
    selectedUser.value = user
    form.nama = user.nama
    form.username = user.username
    form.email = user.email
    form.no_telp = user.no_telp
    form.alamat = user.alamat
    form.role = user.role
    form.password = ''
    showModal.value = true
}

function closeModal() {
    showModal.value = false
    form.reset()
}

function submitForm() {
    if (editMode.value) {
        form.put(route('admin.users.update', selectedUser.value.id_user), {
            onSuccess: () => closeModal()
        })
    } else {
        form.post(route('admin.users.store'), {
            onSuccess: () => closeModal()
        })
    }
}

function deleteUser(id) {
    if (confirm('Are you sure you want to delete this user?')) {
        form.delete(route('admin.users.destroy', id))
    }
}
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <SidebarAdmin />
        <HeaderAdmin />

        <!-- Main Content -->
        <main class="ml-64 pt-16 p-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Users Management</h1>
                <button @click="openCreateModal" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add New User
                </button>
            </div>

            <!-- Users Table -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-4 border-b">
                    <h2 class="text-xl font-semibold text-gray-800">All Users</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="user in users.data" :key="user.id_user" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ user.id_user }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ user.nama }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.no_telp || '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-semibold"
                                        :class="user.role === 'admin' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'"
                                    >
                                        {{ user.role }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button @click="openEditModal(user)" class="text-green-600 hover:text-green-900 mr-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button @click="deleteUser(user.id_user)" class="text-red-600 hover:text-red-900">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t flex justify-between items-center">
                    <div class="text-sm text-gray-700">
                        Showing {{ users.from }} to {{ users.to }} of {{ users.total }} entries
                    </div>
                    <div class="flex gap-2">
                        <button
                            v-for="link in users.links"
                            :key="link.label"
                            @click="$inertia.visit(link.url)"
                            :disabled="!link.url"
                            class="px-3 py-1 rounded"
                            :class="link.active ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'"
                            v-html="link.label"
                        ></button>
                    </div>
                </div>
            </div>
        </main>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
                <h2 class="text-2xl font-bold mb-4 text-gray-800">{{ editMode ? 'Edit User' : 'Add New User' }}</h2>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input v-model="form.nama" type="text" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                        <input v-model="form.username" type="text" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input v-model="form.email" type="email" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input v-model="form.no_telp" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <select v-model="form.role" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password {{ editMode ? '(Leave blank to keep current)' : '' }}</label>
                        <input v-model="form.password" type="password" :required="!editMode" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>

                    <div class="flex gap-3 mt-6">
                        <button type="submit" :disabled="form.processing" class="flex-1 bg-green-500 text-white py-2 rounded-md hover:bg-green-600">
                            {{ form.processing ? 'Saving...' : 'Save' }}
                        </button>
                        <button type="button" @click="closeModal" class="flex-1 bg-gray-300 text-gray-700 py-2 rounded-md hover:bg-gray-400">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
