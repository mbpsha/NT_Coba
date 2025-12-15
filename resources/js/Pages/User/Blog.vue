<script setup>
import { Head } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

import Header from '@/Components/User/Header.vue'
import Footer from '@/Components/User/Footer.vue'

import faqBG from '*/dashboard/blogfaq.png'

const activeTab = ref('produk')
const openedIndex = ref(null)

const heroStyle = computed(() => ({
  backgroundImage: `linear-gradient(to bottom, rgba(255,255,255,0), rgba(255,255,255,0)), url('${faqBG}')`,
  height: '400px',
  backgroundPosition: 'center 0px',
  backgroundSize: 'cover',
}))

const faqs = {
  produk: [
    {
      question: 'Apa itu Stech Smart Garden?',
      answer:
        'Stech Smart Garden adalah perangkat IoT penyiram tanaman otomatis yang bekerja menggunakan sensor kelembapan tanah untuk menentukan kapan tanaman memerlukan air.',
    },
    {
      question: 'Bagaimana cara kerja sistem penyiraman otomatis?',
      answer:
        'Sensor akan membaca tingkat kelembapan tanah. Jika tanah terlalu kering, sistem akan mengaktifkan pompa air secara otomatis. Jika sudah cukup lembap, pompa akan berhenti.',
    },
    {
      question: 'Apakah alat ini bisa digunakan untuk berbagai jenis tanaman?',
      answer:
        'Ya, Stech Smart Garden dapat digunakan untuk berbagai jenis tanaman, baik skala rumah tangga, urban farming, hingga pertanian kecil',
    },
    {
      question: 'Apakah alat Stech Smart Garden membutuhkan listrik untuk bekerja?',
      answer: `Ya, Stech Smart Garden membutuhkan sumber energi. Terdapat dua pilihan jenis alat:
              • Tipe Panel Surya  – cocok untuk lokasi jauh dari listrik.
              • Tipe Listrik Langsung  - cocok untuk indoor atau area akses listrik stabil.`,
    },
    {
      question: 'Apakah Stech Smart Garden bisa tetap bekerja saat cuaca mendung jika menggunakan panel surya?',
      answer:
        'Ya, Stech Smart Garden tetap bisa bekerja karena panel surya menyimpan daya pada baterai internal. Namun durasi operasional bergantung pada intensitas cahaya matahari.',
    },
  ],

  pembelian: [
    {
      question: 'Bagaimana cara membeli produk Stech Smart Garden?',
      answer:
        'Anda dapat memilih produk pada halaman Toko, menambahkannya ke keranjang atau langsung klik Beli Sekarang, lalu menyelesaikan pembelian melalui proses checkout yang telah disediakan.',
    },
    {
      question: ' Metode pembayaran apa yang tersedia?',
      answer:
        'Pembayaran dapat dilakukan melalui  scan barcode QRIS yang telah disediakan.  Setelah pembayaran dilakukan, sistem admin akan memverifikasi transaksi Anda.',
    },
    {
      question: 'Apakah produk IoT tersedia ready stock atau pre-order?',
      answer:
        'Perangkat IoT Stech Smart Garden tersedia dalam sistem pre-order (PO) dengan estimasi waktu perakitan dan kalibrasi selama ±15 hari sebelum dikirimkan.',
    },
    {
      question: 'Bagaimana cara melacak status pesanan saya?',
      answer:
        'Anda dapat memantau status pesanan melalui akun Anda di menu Tracking Pesanan, mulai dari “Pembayaran Terverifikasi”, “Dalam Produksi”, “Dalam Pengiriman”, hingga “Beri Penilaian” setelah pesanan diterima.',
    },
    {
      question: 'Apakah produk memiliki garansi dan dukungan purna jual?',
      answer:
        'Ya. Produk mendapat garansi 30 hari untuk penggantian unit cacat dan 6 bulan garansi servis. Anda juga dapat menghubungi tim melalui kontak website untuk bantuan instalasi atau troubleshooting.',
    },
  ],
}

const visibleFaqs = computed(() => faqs[activeTab.value])

function setTab(tab) {
  activeTab.value = tab
  // tutup semua ketika ganti tab
  openedIndex.value = null
}

function toggle(idx) {
  openedIndex.value = openedIndex.value === idx ? null : idx
}
</script>

<template>
  <div class="min-h-screen bg-white font-inter text-gray-800">
    <Header />
    <Head title="FAQ" />

    <section class="relative flex w-full items-center justify-center bg-cover" :style="heroStyle">
      <div class="text-center">
        <h1 class="text-[40px] md:text-[60px] font-extrabold leading-tight tracking-tight text-white drop-shadow-lg">
          <span class="text-white">Frequently</span>
          <span class="font-extrabold text-[#a855f7]"> Asked Questions</span>
        </h1>
        <p class="mt-2 text-[18px] tracking-wide text-white">
          Pertanyaan yang sering diajukan seputar alat IoT penyiram tanaman otomatis
        </p>
      </div>
    </section>

    <!-- ================== CONTENT =================== -->
    <main class="relative mx-auto -mt-10 max-w-6xl px-6 pb-16 sm:px-6 lg:px-8">
      <div class="relative overflow-hidden rounded-3xl border border-gray-200 bg-white p-10 shadow-2xl md:p-14">
        <div class="relative z-30">
          <h2 class="mb-10 text-center text-5xl font-bold tracking-wide">FAQ</h2>

          <!-- TAB -->
          <div class="mb-12 flex justify-center gap-8">
            <button
              class="pb-1 text-sm font-semibold tracking-wide transition"
              :class="
                activeTab === 'produk'
                  ? 'text-[#a855f7] border-b-2 border-[#a855f7]'
                  : 'text-gray-500'
              "
              @click="setTab('produk')"
            >
              PRODUK
            </button>

            <button
              class="pb-1 text-sm font-semibold tracking-wide transition"
              :class="
                activeTab === 'pembelian'
                  ? 'text-[#a855f7] border-b-2 border-[#a855f7]'
                  : 'text-gray-500'
              "
              @click="setTab('pembelian')"
            >
              PEMBELIAN
            </button>
          </div>

          <!-- FAQ LIST -->
          <div class="space-y-4">
            <div
              v-for="(item, idx) in visibleFaqs"
              :key="idx"
              class="relative overflow-hidden rounded-2xl border border-gray-200 shadow-md transition-shadow duration-200 hover:shadow-lg"
            >
              <button class="flex w-full items-center justify-between px-6 py-5 text-left" @click="toggle(idx)">
                <h3 class="text-[16px] font-semibold">
                  {{ item.question }}
                </h3>
                <span class="text-xl text-gray-500">{{ openedIndex === idx ? '−' : '+' }}</span>
              </button>

              <transition name="accordion">
                <div
                  v-if="openedIndex === idx"
                  class="whitespace-pre-line border-t border-green-100 bg-[#E2F2DA] px-6 pt-6 pb-6 text-sm text-gray-700"
                >
                  {{ item.answer }}
                </div>
              </transition>
            </div>
          </div>
        </div>
      </div>
    </main>

    <Footer />
  </div>
</template>

<style scoped>
.accordion-enter-active,
.accordion-leave-active {
  overflow: hidden;
  /* atur kecepatan di sini */
  transition: max-height 0.35s ease, opacity 0.35s ease;
}

.accordion-enter-from,
.accordion-leave-to {
  max-height: 0;
  opacity: 0;
}

.accordion-enter-to,
.accordion-leave-from {
  /* cukup besar untuk isi jawaban */
  max-height: 500px;
  opacity: 1;
}
</style>