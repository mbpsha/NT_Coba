<script setup>
import Header from '@/component/Header.vue'
import Footer from '@/component/Footer.vue'
import { Head, router } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'

const activeTab = ref('review')
const showToast = ref(false)
const toastMsg = ref('')

// Dummy: seluruh histori pesanan (rating null = belum dinilai)
const orders = ref([
    {
    id: 101,
    name: 'IoT Stech Smart Garden',
    note: '(Belum include panel surya) + Buku panduan + Source Code',
    date: '2 Maret 2024',
    image: '/assets/dashboard/profil.png',
    rating: null,
    reviewText: ''
    },

    {
    id: 102,
    name: 'IoT Stech Smart Garden',
    note: '(Include panel surya) + Buku panduan + Source Code',
    date: '2 Maret 2024',
    image: '/assets/dashboard/profil.png',
    rating: null,
    reviewText: ''
    },

    {
    id: 103,
    name: 'Pupuk Organik Cair 1L',
    note: 'Cocok untuk sayur',
    date: '1 Maret 2024',
    image: '/assets/dashboard/profil.png',
    rating: 4,
    reviewText: 'Bagus, tanaman cepat segar.'
    }
])

// Pesanan yang belum dinilai
const pending = computed(() => orders.value.filter(o => o.rating === null))

// Produk yang sedang dinilai (default: yang pertama pending)
const selectedId = ref(pending.value[0]?.id ?? null)
watch(pending, (p) => {
    if (!p.length) selectedId.value = null
    else if (!p.find(x => x.id === selectedId.value)) selectedId.value = p[0].id
})

const currentItem = computed(() => orders.value.find(o => o.id === selectedId.value) || null)
const ratingModel = ref(0)
const reviewModel = ref('')

watch(currentItem, (it) => {
    ratingModel.value = it?.rating || 0
    reviewModel.value = it?.reviewText || ''
}, { immediate: true })

function setRating(v) { ratingModel.value = v }

function submitReview() {
    if (!currentItem.value) return
    if (!ratingModel.value) return toast('Pilih rating terlebih dahulu.')

  // Simpan dummy ke data lokal
    currentItem.value.rating = ratingModel.value
    currentItem.value.reviewText = reviewModel.value?.trim() || ''
    toast('Terima kasih! Penilaianmu sudah terkirim.')
    activeTab.value = 'history'
}

function buyAgain(it) {
  // Arahkan ke checkout (dummy)
    router.visit(`/checkout/${it.id}?qty=1`, { preserveScroll: true })
}

function toast(message) {
    toastMsg.value = message
    showToast.value = true
    setTimeout(() => (showToast.value = false), 2200)
}
</script>

<template>
    <div class="font-inter text-gray-900 bg-gray-50 min-h-screen">
        <Header />
        <Head title="Beri Penilaian" />

        <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-20">
        <!-- Tabs -->
        <div class="flex items-center justify-center gap-10 mb-6">
            <button
            class="text-sm font-semibold border-b-2 pb-2"
            :class="activeTab==='review' ? 'text-green-700 border-green-600' : 'text-gray-500 border-transparent'"
            @click="activeTab='review'">
            Beri Penilaian
            </button>
            <button
            class="text-sm font-semibold border-b-2 pb-2"
            :class="activeTab==='history' ? 'text-green-700 border-green-600' : 'text-gray-500 border-transparent'"
            @click="activeTab='history'">
            Histori pembelian
            </button>
        </div>

        <!-- Beri Penilaian -->
        <section v-show="activeTab==='review'">
            <div v-if="currentItem" class="bg-white rounded-2xl border shadow-sm p-6">
            <!-- Header produk -->
            <div class="flex items-start gap-4">
                <img :src="currentItem.image" class="w-16 h-16 object-contain rounded-md border bg-white" alt="produk">
                <div class="flex-1">
                <p class="text-base font-semibold leading-tight">{{ currentItem.name }}</p>
                <p class="text-xs text-gray-600">{{ currentItem.note }}</p>
                </div>
            </div>

            <!-- Nilai Produk -->
            <div class="mt-8 text-center">
                <p class="text-sm font-semibold mb-3">Nilai Produk</p>
                <div class="flex items-center justify-center gap-3 text-4xl">
                <button v-for="i in 5" :key="i" @click="setRating(i)"
                        :class="i<=ratingModel ? 'text-yellow-400' : 'text-gray-300'">★</button>
                </div>

                <!-- Ulasan -->
                <p class="text-sm font-semibold mt-8 mb-2">Tulis Ulasan</p>
                <textarea v-model="reviewModel" rows="6"
                        class="w-full rounded-xl border px-3 py-2 bg-gray-50"
                        placeholder="Ceritakan pengalamanmu (opsional)"></textarea>

                <button @click="submitReview"
                        class="mt-6 px-6 h-10 rounded-md bg-green-600 hover:bg-green-700 text-white text-sm font-medium">
                Kirim
                </button>
            </div>
            </div>

            <p v-else class="text-center text-gray-500 text-sm py-10">
            Tidak ada pesanan yang perlu dinilai.
            </p>
        </section>

        <!-- Histori pembelian -->
        <section v-show="activeTab==='history'" class="space-y-4">
            <div v-for="it in orders" :key="it.id" class="bg-green-50/70 border border-green-100 rounded-2xl p-4 flex items-center gap-4">
            <img :src="it.image" alt="produk" class="w-16 h-16 object-contain rounded-md bg-white border" />
            <div class="flex-1">
                <p class="text-sm font-semibold leading-tight">
                {{ it.name }} <span class="font-normal text-gray-600"> {{ it.note }}</span>
                </p>
                <div class="mt-1 text-yellow-400 text-lg tracking-tight">
                <span v-for="i in 5" :key="i">{{ i<= (it.rating ?? 0) ? '★' : '☆' }}</span>
                </div>
                <p v-if="it.reviewText" class="text-xs text-gray-700 mt-1 line-clamp-2">“{{ it.reviewText }}”</p>
                <p class="text-[11px] text-gray-500 mt-1">{{ it.date }}</p>
            </div>
            <button @click="buyAgain(it)"
                    class="px-4 py-2 text-sm rounded-md bg-green-100 text-green-700 hover:bg-green-200">
                Beli Lagi
            </button>
            </div>
        </section>
    </main>
    <Footer />

    <!-- Toast -->
    <transition name="fade">
        <div v-if="showToast" class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-green-600 text-white text-sm px-4 py-2 rounded-md shadow">
            {{ toastMsg }}
        </div>
        </transition>
    </div>
</template>

<style scoped>
.fade-enter-active,.fade-leave-active{transition:opacity .2s}
.fade-enter-from,.fade-leave-to{opacity:0}
</style>