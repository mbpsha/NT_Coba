<script setup>
import Header from '@/component/Header.vue'    
import Footer from '@/component/Footer.vue'
import { Head } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const activeTab = ref('produk')
const openedIndex = ref(null)

const faqs = {
  produk: [
    {
      question: 'Apa itu Ngundur (Nguthik Tandur)?',
      answer:
        'Ngundur adalah perangkat IoT penyiram tanaman otomatis yang bekerja menggunakan sensor kelembapan tanah untuk menentukan kapan tanaman memerlukan air.'
    },
    {
      question: 'Bagaimana cara kerja sistem penyiraman otomatis?',
      answer:
        'Sensor akan membaca tingkat kelembapan tanah. Jika tanah terlalu kering, sistem akan mengaktifkan pompa air secara otomatis. Jika sudah cukup lembap, pompa akan berhenti.'
    },
    {
      question: 'Apakah alat ini bisa digunakan untuk berbagai jenis tanaman?',
      answer:
        'Ya, Ngundur dapat digunakan untuk berbagai jenis tanaman, baik skala rumah tangga, urban farming, hingga pertanian kecil'
    },
    {
      question: 'Apakah alat Ngundur membutuhkan listrik untuk bekerja?',
      answer: `Ya, Ngundur membutuhkan sumber energi. Terdapat dua pilihan jenis alat:
• Tipe Panel Surya  – cocok untuk lokasi jauh dari listrik.
• Tipe Listrik Langsung  - cocok untuk indoor atau area akses listrik stabil.`
    },
    {
      question: 'Apakah Ngundur bisa tetap bekerja saat cuaca mendung jika menggunakan panel surya?',
      answer:
        'Ya, Ngundur tetap bisa bekerja karena panel surya menyimpan daya pada baterai internal. Namun durasi operasional bergantung pada intensitas cahaya matahari.'
    }
  ],

  pembelian: [
    {
      question: 'Bagaimana cara membeli produk Ngundur “Nguthik Tandur”??',
      answer:
        'Anda dapat memilih produk pada halaman Toko Online, menambahkannya ke keranjang atau langsung klik Beli Sekarang, lalu menyelesaikan pembelian melalui proses checkout yang telah disediakan.'
    },
    {
      question: ' Metode pembayaran apa yang tersedia?',
      answer:
        'Pembayaran dapat dilakukan melalui  scan barcode QRIS yang telah disediakan.  Setelah pembayaran dilakukan, sistem admin akan memverifikasi transaksi Anda.'
    },
    {
      question: 'Apakah produk IoT tersedia ready stock atau pre-order?',
      answer:
        'Perangkat IoT Ngundur tersedia dalam sistem pre-order (PO) dengan estimasi waktu perakitan dan kalibrasi selama ±15 hari sebelum dikirimkan.'
    },
    {
      question: 'Bagaimana cara melacak status pesanan saya?',
      answer:
        'Anda dapat memantau status pesanan melalui akun Anda di menu Tracking Pesanan, mulai dari “Pembayaran Terverifikasi”, “Dalam Produksi”, “Dalam Pengiriman”, hingga “Beri Penilaian” setelah pesanan diterima.'
    },
    {
      question: 'Apakah produk memiliki garansi dan dukungan purna jual?',
      answer:
        'Ya. Produk mendapat garansi 30 hari untuk penggantian unit cacat dan 6 bulan garansi servis. Anda juga dapat menghubungi tim melalui kontak website untuk bantuan instalasi atau troubleshooting.'
    }
  ]
}

const visibleFaqs = computed(() => faqs[activeTab.value])

function setTab(tab) {
  activeTab.value = tab
  openedIndex.value = null // tutup semua ketika ganti tab
}

function toggle(idx) {
  openedIndex.value = openedIndex.value === idx ? null : idx
}
</script>

<template>
  <div class="min-h-screen text-gray-900 bg-gray-100 font-inter">
    <Header />
    <Head title="FAQ" />

    <section
      class="relative w-full flex items-center justify-center bg-cover"
  style="
    background-image:
    linear-gradient(to bottom, rgba(255,255,255,0.0), rgba(255,255,255,0.0)),
    url('/assets/dashboard/blogfaq.png');
    height: 400px;
    background-position: center -0.1000px;  /* 🔥 ini yang memangkas gambar ke bawah */
  "
    >
      <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
        <h1 class="text-4xl font-extrabold text-white md:text-5xl drop-shadow">FAQ</h1>
      </div>
    </section>

    <main class="relative max-w-6xl px-4 pb-16 mx-auto -mt-10 sm:px-6 lg:px-8">
      <div class="relative overflow-hidden bg-white border border-gray-200 shadow-2xl rounded-3xl">
        <div
          class="absolute inset-0 pointer-events-none"
          style="background-image: url('/assets/dashboard/bg-blog.jpg'); background-size: cover; background-position: right center; opacity: 0.05; filter: blur(0.5px);"
        ></div>

        <div class="relative z-10 p-6 sm:p-10 lg:p-14">
          <div class="grid gap-10 md:grid-cols-[2fr_1fr] items-start">
            <article class="space-y-6 text-base leading-relaxed text-gray-700">
              <header class="space-y-3">
                <h2 class="text-4xl md:text-[44px] font-bold text-green-700 leading-tight">Stech Smart Garden</h2>
                <p class="text-sm text-gray-500">Penulis: Tim Ngundur — Selamat Berjuang Sukses</p>
              </header>

              <p>
                Teknologi semakin dekat dengan kehidupan sehari-hari, termasuk dalam bidang pertanian. Melalui inovasi IoT penyiram tanaman “Ngundur (Nguthik Tandur)”, proses penyiraman kini bisa dilakukan secara otomatis berdasarkan kebutuhan air tanaman. Sistem ini dirancang untuk membantu petani dan masyarakat agar lebih efisien dalam mengelola air dan waktu.
              </p>

              <p>
                Dengan sistem sensor kelembapan tanah dan kontrol otomatis, Ngundur mampu menentukan kapan tanaman membutuhkan air, sehingga penyiraman dilakukan hanya pada saat yang dibutuhkan. Hal ini tidak hanya menghemat air, tetapi juga meningkatkan kualitas hasil tanam.
              </p>

              <p>
                Ngundur lahir dari semangat mahasiswa Universitas Sebelas Maret (UNS) yang ingin menghadirkan solusi nyata bagi pertanian modern di Indonesia. Proyek ini merupakan hasil dari program hibah inovasi mahasiswa UNS, yang mendukung pengembangan solusi berbasis teknologi untuk sektor pertanian.
              </p>

              <p>
                Dengan dukungan dari PT Stechoq Robotika Indonesia, perusahaan yang berpengalaman di bidang sistem cerdas dan rekayasa manufaktur teknologi, tim mahasiswa UNS berhasil mewujudkan inovasi ini menjadi produk nyata yang dapat digunakan oleh masyarakat luas. Melalui kolaborasi antara dunia pendidikan, penelitian, dan industri, Ngundur (Nguthik Tandur) diharapkan mampu menjadi langkah kecil menuju pertanian cerdas yang lebih efisien, berkelanjutan, dan ramah lingkungan.
              </p>
            </article>

            <aside class="space-y-6">
              <figure class="overflow-hidden shadow-md rounded-2xl ring-1 ring-gray-200">
                <img src="/assets/dashboard/KITAA.jpg" alt="Team" class="object-cover w-full h-64 md:h-full" />
              </figure>

              <div class="p-6 border border-green-200 shadow-sm rounded-2xl bg-green-50">
                <h4 class="text-xs font-semibold uppercase tracking-[0.3em] text-gray-500">Sistem Penyiraman Cerdas</h4>
                <h3 class="mt-2 text-2xl font-bold text-green-700">NGUNDUR</h3>
                <p class="mt-4 text-sm text-gray-700">
                  Stech Smart Garden adalah sistem irigasi otomatis berbasis IoT yang memantau kelembaban tanah secara real-time
                  dan mengaktifkan pompa hanya saat diperlukan. Hadir dengan varian panel surya, produk ini ramah lingkungan, hemat
                  energi, dan siap mendukung pertanian modern maupun hobi berkebun Anda.
                </p>
              </div>
            </aside>
          </div>

          <!-- FAQ LIST -->
          <div class="space-y-6">
            <!-- Tabs -->
            <div class="flex gap-3">
              <button
                type="button"
                @click="setTab('produk')"
                :class="['px-4 py-2 rounded-full text-sm font-medium transition', activeTab === 'produk' ? 'bg-green-600 text-white shadow' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50']"
              >Produk</button>
              <button
                type="button"
                @click="setTab('pembelian')"
                :class="['px-4 py-2 rounded-full text-sm font-medium transition', activeTab === 'pembelian' ? 'bg-green-600 text-white shadow' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50']"
              >Pembelian</button>
            </div>

            <!-- Accordion Items -->
            <div
              v-for="(item, idx) in visibleFaqs"
              :key="idx"
              class="relative rounded-2xl border border-gray-200 overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-200"
            >


              <button
                class="w-full px-6 py-5 flex items-center justify-between text-left"
                @click="toggle(idx)"
                :aria-expanded="openedIndex === idx ? 'true' : 'false'"
                :aria-controls="'faq-panel-' + idx"
                type="button"
              >
                <h3 class="font-semibold text-[16px]">
                  {{ item.question }}
                </h3>
                <span class="text-xl text-gray-500">
                  {{ openedIndex === idx ? '−' : '+' }}
                </span>
              </button>

              <transition name="accordion">
                <div
                  v-if="openedIndex === idx"
                  :id="'faq-panel-' + idx"
                  class="px-6 pt-6 pb-6 bg-[#E2F2DA] border-t border-green-100 text-sm text-gray-700 whitespace-pre-line"
                >
                  {{ item.answer }}
                </div>
              </transition>
            </div>

            <div class="relative p-6 rounded-xl bg-white/10 backdrop-blur">
              <p class="text-sm text-green-50">
                Sistem mampu mengirim data ke dashboard monitoring, sehingga pengguna dapat memantau kondisi tanaman kapan saja.
                Fitur jadwal pintar memungkinkan penyesuaian sesuai kebutuhan varietas tanaman yang berbeda.
              </p>
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
  transition:
    max-height 0.35s ease,
    opacity 0.35s ease;
}

.accordion-enter-from,
.accordion-leave-to {
  max-height: 0;
  opacity: 0;
}

.accordion-enter-to,
.accordion-leave-from {
  max-height: 500px; /* cukup besar untuk isi jawaban */
  opacity: 1;
}
</style>
