<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import SidebarAdmin from '@/Components/Admin/SidebarAdmin.vue'
import HeaderAdmin from '@/Components/Admin/HeaderAdmin.vue'

const props = defineProps({
    products: Object
})

const showModal = ref(false)
const editMode = ref(false)
const selectedProduct = ref(null)

const form = useForm({
    nama_produk: '',
    deskripsi: '',
    harga: '',
    stok: '',
    kategori: '',
    gambar: null
})

function openCreateModal() {
    editMode.value = false
    form.reset()
    showModal.value = true
}

function openEditModal(product) {
    editMode.value = true
    selectedProduct.value = product
    form.nama_produk = product.nama_produk
    form.deskripsi = product.deskripsi
    form.harga = product.harga
    form.stok = product.stok
    form.kategori = product.kategori
    showModal.value = true
}

function closeModal() {
    showModal.value = false
    form.reset()
}

function handleFileUpload(event) {
    form.gambar = event.target.files[0]
}

function submitForm() {
    if (editMode.value) {
        form.transform((data) => ({...data, _method: 'PUT'}))
        form.post(route('admin.products.update', selectedProduct.value.id_produk), {
            onSuccess: () => closeModal(),
            forceFormData: true
        })
    } else {
        form.post(route('admin.products.store'), {
            onSuccess: () => closeModal(),
            forceFormData: true
        })
    }
}

function deleteProduct(id) {
    if (confirm('Are you sure you want to delete this product?')) {
        form.delete(route('admin.products.destroy', id), {
            onSuccess: () => form.reset()
        })
    }
}
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <SidebarAdmin />
        <HeaderAdmin />

        <!-- Main Content -->
        <main class="p-8 pt-16 ml-64">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Products Management</h1>
                <button @click="openCreateModal" class="flex items-center gap-2 px-4 py-2 text-white bg-green-500 rounded-lg hover:bg-green-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add New Product
                </button>
            </div>

            <!-- Products Table -->
            <div class="overflow-hidden bg-white rounded-lg shadow-md">
                <div class="p-4 border-b">
                    <h2 class="text-xl font-semibold text-gray-800">All Products</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">ID</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Image</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Name</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Price</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Category</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Stock</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="product in products.data" :key="product.id_produk" class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ product.id_produk }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <img :src="product.gambar || '/assets/dashboard/profil.png'" alt="Product" class="object-cover w-12 h-12 rounded">
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ product.nama_produk }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">Rp {{ product.harga?.toLocaleString('id-ID') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">{{ product.kategori }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ product.stok }} unit</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">
                                        Active
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                                    <button @click="openEditModal(product)" class="mr-3 text-green-600 hover:text-green-900">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button @click="deleteProduct(product.id_produk)" class="text-red-600 hover:text-red-900">
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
                <div class="flex items-center justify-between px-6 py-4 border-t">
                    <div class="text-sm text-gray-700">
                        Showing {{ products.from }} to {{ products.to }} of {{ products.total }} entries
                    </div>
                    <div class="flex gap-2">
                        <button
                            v-for="link in products.links"
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
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg p-8 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                <h2 class="mb-4 text-2xl font-bold text-gray-800">{{ editMode ? 'Edit Product' : 'Add New Product' }}</h2>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Product Name</label>
                        <input v-model="form.nama_produk" type="text" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Description</label>
                        <textarea v-model="form.deskripsi" rows="3" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Price (Rp)</label>
                            <input v-model="form.harga" type="number" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Stock</label>
                            <input v-model="form.stok" type="number" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Category</label>
                        <input v-model="form.kategori" type="text" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Product Image</label>
                        <input name="gambar" @change="handleFileUpload" type="file" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        <p class="mt-1 text-xs text-gray-500">{{ editMode ? 'Leave blank to keep current image' : 'Upload product image' }}</p>
                    </div>

                    <div class="flex gap-3 mt-6">
                        <button type="submit" :disabled="form.processing" class="flex-1 py-2 text-white bg-green-500 rounded-md hover:bg-green-600">
                            {{ form.processing ? 'Saving...' : 'Save' }}
                        </button>
                        <button type="button" @click="closeModal" class="flex-1 py-2 text-gray-700 bg-gray-300 rounded-md hover:bg-gray-400">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
