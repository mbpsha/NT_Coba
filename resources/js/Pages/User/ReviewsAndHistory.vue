<script setup>
import Header from '@/Components/User/Header.vue'
import Footer from '@/Components/User/Footer.vue'
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
  <div class="min-h-screen text-gray-900 font-inter bg-gray-50">
    <Header />
    <Head title="Beri Penilaian" />

    <main class="max-w-5xl px-4 pt-24 pb-20 mx-auto sm:px-6 lg:px-8">
      <!-- Tabs -->
      <div class="flex items-center justify-center gap-10 mb-6">
        <button
          class="pb-2 text-sm font-semibold border-b-2"
          :class="activeTab==='review' ? 'text-green-700 border-green-600' : 'text-gray-500 border-transparent'"
          @click="activeTab='review'">
          Beri Penilaian
        </button>
        <button
          class="pb-2 text-sm font-semibold border-b-2"
          :class="activeTab==='history' ? 'text-green-700 border-green-600' : 'text-gray-500 border-transparent'"
          @click="activeTab='history'">
          Histori pembelian
        </button>
      </div>

      <!-- Beri Penilaian -->
      <section v-show="activeTab==='review'">
        <div v-if="currentItem" class="p-6 bg-white border shadow-sm rounded-2xl">
          <!-- Header produk -->
          <div class="flex items-start gap-4">
            <img :src="currentItem.image" class="object-contain w-16 h-16 bg-white border rounded-md" alt="produk">
            <div class="flex-1">
              <p class="text-base font-semibold leading-tight">{{ currentItem.name }}</p>
              <p class="text-xs text-gray-600">{{ currentItem.note }}</p>
            </div>
          </div>

          <!-- Nilai Produk -->
          <div class="mt-8 text-center">
            <p class="mb-3 text-sm font-semibold">Nilai Produk</p>
            <div class="flex items-center justify-center gap-3 text-4xl">
              <button v-for="i in 5" :key="i" @click="setRating(i)"
                      :class="i<=ratingModel ? 'text-yellow-400' : 'text-gray-300'">★</button>
            </div>

            <!-- Ulasan -->
            <p class="mt-8 mb-2 text-sm font-semibold">Tulis Ulasan</p>
            <textarea v-model="reviewModel" rows="6"
                      class="w-full px-3 py-2 border rounded-xl bg-gray-50"
                      placeholder="Ceritakan pengalamanmu (opsional)"></textarea>

            <button @click="submitReview"
                    class="h-10 px-6 mt-6 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">
              Kirim
            </button>
          </div>
        </div>

        <p v-else class="py-10 text-sm text-center text-gray-500">
          Tidak ada pesanan yang perlu dinilai.
        </p>
      </section>

      <!-- Histori pembelian -->
      <section v-show="activeTab==='history'" class="space-y-4">
        <div v-for="it in orders" :key="it.id" class="flex items-center gap-4 p-4 border border-green-100 bg-green-50/70 rounded-2xl">
          <img :src="it.image" alt="produk" class="object-contain w-16 h-16 bg-white border rounded-md" />
          <div class="flex-1">
            <p class="text-sm font-semibold leading-tight">
              {{ it.name }} <span class="font-normal text-gray-600"> {{ it.note }}</span>
            </p>
            <div class="mt-1 text-lg tracking-tight text-yellow-400">
              <span v-for="i in 5" :key="i">{{ i<= (it.rating ?? 0) ? '★' : '☆' }}</span>
            </div>
            <p v-if="it.reviewText" class="mt-1 text-xs text-gray-700 line-clamp-2">“{{ it.reviewText }}”</p>
            <p class="text-[11px] text-gray-500 mt-1">{{ it.date }}</p>
          </div>
          <button @click="buyAgain(it)"
                  class="px-4 py-2 text-sm text-green-700 bg-green-100 rounded-md hover:bg-green-200">
            Beli Lagi
          </button>
        </div>
      </section>
    </main>

    <Footer />

    <!-- Toast -->
    <transition name="fade">
      <div v-if="showToast" class="fixed px-4 py-2 text-sm text-white -translate-x-1/2 bg-green-600 rounded-md shadow bottom-6 left-1/2">
        {{ toastMsg }}
      </div>
    </transition>
  </div>
</template>

<style scoped>
.fade-enter-active,.fade-leave-active{transition:opacity .2s}
.fade-enter-from,.fade-leave-to{opacity:0}
</style>