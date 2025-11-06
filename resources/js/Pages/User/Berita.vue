<script setup>
import Header from '@/Components/User/Header.vue'
import Footer from '@/Components/User/Footer.vue'

import { ref, computed, nextTick, watch, onBeforeUnmount } from 'vue'
import { Head } from '@inertiajs/vue3'

// Gambar sama dengan NewsCarousel
import NewsLeft from '*/dashboard/berita-kiri.png'
import NewsMid from '*/dashboard/berita-1.png'
import NewsRight from '*/dashboard/berita-kanan.png'
import NewsExtra from '*/dashboard/berita-4.jpg'
import BgBerita from '*/dashboard/bg-berita.jpg'

const articles = [
  {
    title: 'Petani Sayuran Mulai Terapkan Irigasi Otomatis untuk Hemat Air',
    image: NewsMid,
    excerpt:
      'Beberapa kelompok petani di Sleman mulai menggunakan teknologi irigasi otomatis untuk mengurangi pemborosan air.',
    content:
      `SLEMAN — Sejumlah kelompok petani sayuran di Kabupaten Sleman, Daerah Istimewa Yogyakarta, mulai beralih menggunakan teknologi irigasi otomatis berbasis Internet of Things (IoT) untuk menghemat penggunaan air dalam kegiatan pertanian.  Langkah ini dilakukan sebagai upaya menghadapi tantangan perubahan iklim dan menurunnya ketersediaan air bersih di beberapa wilayah pertanian.

Teknologi irigasi otomatis ini bekerja menggunakan sensor kelembapan tanah yang mampu mendeteksi kondisi kadar air pada lahan. Ketika tanah mulai kering, sistem akan secara otomatis mengaktifkan pompa air untuk menyiram tanaman. Sebaliknya, ketika tanah sudah cukup lembap, pompa akan berhenti bekerja.

Menurut beberapa petani yang telah menggunakan sistem ini, penggunaan air dapat berkurang hingga 30–40 persen, dibandingkan dengan metode penyiraman manual yang dilakukan setiap hari.

“Sebelumnya kami menyiram dua kali sehari, pagi dan sore. Sekarang sistem otomatis ini hanya menyiram saat tanah benar-benar kering, jadi air tidak terbuang percuma,” ujar Sutrisno (45), salah satu petani sayuran di daerah Gamping, Sleman.

Selain efisien dalam penggunaan air, sistem ini juga membantu petani menghemat waktu dan tenaga. Mereka tidak perlu lagi mengawasi penyiraman secara manual karena semua proses dikendalikan secara otomatis.
Salah satu teknologi yang digunakan dalam implementasi ini adalah IoT penyiram tanaman Ngundur "(Nguthik Tandur)", hasil inovasi mahasiswa Universitas Sebelas Maret (UNS) melalui program hibah riset dan pengabdian. Alat tersebut dikembangkan bekerja sama dengan PT Stechoq Robotika Indonesia, yang berpengalaman dalam rekayasa sistem cerdas dan manufaktur teknologi.`,
  },
  {
    title: 'Tren Pertanian Urban: Berkebun di Lahan Sempit dengan Smart Garden',
    image: NewsRight,
    excerpt:
      'Masyarakat perkotaan kian tertarik berkebun di lahan terbatas. Smart Garden membantu pengelolaan nutrisi dan penyiraman.',
    content:
      `Kehidupan di perkotaan yang serba cepat dan padat lahan kini tidak lagi menjadi hambatan bagi masyarakat yang ingin berkebun. Fenomena pertanian urban (urban farming) sedang menjadi tren baru, terutama di kalangan generasi muda yang peduli terhadap gaya hidup sehat dan lingkungan.
Teknologi irigasi otomatis ini bekerja menggunakan sensor kelembapan tanah yang mampu mendeteksi kondisi kadar air pada lahan. Ketika tanah mulai kering, sistem akan secara otomatis mengaktifkan pompa air untuk menyiram tanaman. Sebaliknya, ketika tanah sudah cukup lembap, pompa akan berhenti bekerja.

Dengan bantuan teknologi seperti Smart Garden  sistem berkebun pintar berbasis Internet of Things (IoT)  masyarakat kota kini bisa menanam sayuran, rempah, atau tanaman hias di area rumah yang terbatas, bahkan di balkon apartemen sekalipun.

Smart Garden menggunakan sensor kelembapan tanah, pengatur pencahayaan, serta sistem penyiraman otomatis yang dapat dikendalikan melalui aplikasi smartphone. Teknologi ini memastikan tanaman tetap mendapatkan air dan cahaya dalam jumlah yang ideal tanpa harus disiram secara manual setiap hari.

“Kami melihat potensi besar dalam penerapan IoT bukan hanya di sawah, tapi juga di rumah. Konsep pertanian cerdas bisa dimulai dari hal kecil seperti Smart Garden,” kata Muharram Bagas Putra Santoso, salah satu pengembang sistem Ngundur.
Dengan tren ini, pertanian urban tidak hanya menjadi aktivitas hobi, tetapi juga bagian dari gaya hidup berkelanjutan. Masyarakat dapat memproduksi sebagian kebutuhan sayur atau tanaman hias sendiri, sekaligus berkontribusi dalam mengurangi jejak karbon dan meningkatkan kualitas udara di perkotaan.`,
  },
  {
    title: 'Indonesia Dorong Pertanian Ramah Lingkungan dengan Teknologi IoT',
    image: NewsLeft,
    excerpt:
      'Kementerian mendorong penggunaan sensor, drone, dan irigasi otomatis untuk meningkatkan produktivitas sekaligus keberlanjutan.',
    content:
      `JAKARTA — Pemerintah melalui Kementerian Pertanian (Kementan) terus mendorong penerapan teknologi Internet of Things (IoT) dalam sektor pertanian untuk meningkatkan produktivitas dan efisiensi penggunaan sumber daya alam. Langkah ini menjadi bagian dari strategi nasional menuju pertanian berkelanjutan yang ramah lingkungan dan adaptif terhadap perubahan iklim.

Berbagai inovasi berbasis IoT mulai diperkenalkan ke petani di sejumlah daerah, seperti sensor kelembapan tanah, drone pemantau lahan, hingga sistem irigasi otomatis. Teknologi ini memungkinkan petani memantau kondisi lahan secara real-time dan mengambil keputusan yang lebih cepat serta akurat dalam pengelolaan air dan pupuk.

“Kita ingin pertanian Indonesia tidak hanya produktif, tapi juga efisien dan berkelanjutan. Teknologi IoT akan membantu petani memahami kebutuhan tanaman secara presisi,” ujar Dr. Hadi Prasetyo, Direktur Jenderal Prasarana dan Sarana Pertanian Kementan, dalam keterangannya di Jakarta, Senin (19/10).

Selain meningkatkan efisiensi sumber daya, penggunaan IoT juga berkontribusi pada pengurangan emisi karbon dari sektor pertanian melalui optimalisasi energi dan air. Data yang dikumpulkan dari sensor-sensor di lapangan dapat dianalisis untuk memprediksi cuaca, mendeteksi kekeringan dini, hingga menentukan waktu tanam yang ideal.`,
  },
  {
    title: 'Pemanfaatan Panel Surya dalam Pertanian Modern',
    image: NewsExtra,
    excerpt:
      'Energi surya banyak dipakai untuk pompa air, sistem irigasi, dan sensor lapangan. Biaya operasional pun lebih rendah.',
    content:
      `SLEMAN — Inovasi pertanian berbasis teknologi kini semakin berkembang di kalangan petani lokal. Sejumlah kelompok petani sayuran di wilayah Sleman, Daerah Istimewa Yogyakarta, mulai menerapkan sistem irigasi otomatis yang terintegrasi dengan sumber energi terbarukan, seperti panel surya, untuk mendukung kegiatan pertanian yang lebih efisien dan ramah lingkungan.

Sistem ini memanfaatkan sensor kelembapan tanah untuk menentukan kapan lahan membutuhkan air. Pompa air akan aktif secara otomatis ketika kadar air di tanah menurun, dan berhenti saat kelembapan kembali stabil. Mekanisme otomatis ini membantu petani menghemat air hingga 40 persen, sekaligus memastikan tanaman memperoleh penyiraman yang optimal.

“Kami tidak perlu lagi menyiram secara manual setiap hari. Sistem otomatis ini menyalakan pompa hanya saat dibutuhkan, dan karena energinya dari panel surya, kami juga hemat listrik,” ujar Sutrisno (45), petani sayuran asal Gamping, Sleman.

Selain efisiensi air, penggunaan panel surya juga menjadi langkah cerdas dalam menekan biaya operasional. Energi matahari yang diubah menjadi listrik mampu menghidupkan pompa air, sensor IoT, dan sistem kontrol tanpa bergantung pada jaringan listrik konvensional.`,
  },
]

const selected = ref(null)
const modalContentRef = ref(null)
const lastActiveElement = ref(null)
const detailArticle = ref(null)

const open = (a, evt) => {
  // store the element that triggered the modal so we can return focus to it
  lastActiveElement.value = evt?.currentTarget || document.activeElement
  selected.value = a
}

const close = async () => {
  selected.value = null
  // restore focus to the triggering element after modal closes
  await nextTick()
  if (lastActiveElement.value && lastActiveElement.value.focus) {
    try { lastActiveElement.value.focus() } catch (e) { /* ignore */ }
  }
}

const openDetail = (a, evt) => {
  // open a full-page detail view for the article
  lastActiveElement.value = evt?.currentTarget || document.activeElement
  detailArticle.value = a
  // scroll to top of content area
  window.scrollTo({ top: 0, behavior: 'auto' })
}

const backToList = () => {
  detailArticle.value = null
  // restore focus to the triggering element if available
  if (lastActiveElement.value && lastActiveElement.value.focus) {
    try { lastActiveElement.value.focus() } catch (e) {}
  }
}

// Keep focus inside modal and prevent background scrolling when modal open
const handleGlobalKeydown = (e) => {
  if (!selected.value) return
  if (e.key === 'Escape') {
    e.preventDefault()
    close()
    return
  }

  if (e.key === 'Tab') {
    // focus trap
    const container = modalContentRef.value
    if (!container) return
    const focusable = Array.from(container.querySelectorAll('a, button, input, textarea, select, [tabindex]:not([tabindex="-1"])'))
      .filter((el) => !el.hasAttribute('disabled') && el.offsetParent !== null)
    if (focusable.length === 0) {
      e.preventDefault()
      return
    }
    const idx = focusable.indexOf(document.activeElement)
    if (e.shiftKey) {
      if (idx === 0 || document.activeElement === container) {
        focusable[focusable.length - 1].focus()
        e.preventDefault()
      }
    } else {
      if (idx === focusable.length - 1) {
        focusable[0].focus()
        e.preventDefault()
      }
    }
  }
}

watch(selected, async (val) => {
  if (val) {
    // prevent background scroll
    document.documentElement.style.overflow = 'hidden'
    await nextTick()
    if (modalContentRef.value) modalContentRef.value.focus()
    window.addEventListener('keydown', handleGlobalKeydown)
  } else {
    document.documentElement.style.overflow = ''
    window.removeEventListener('keydown', handleGlobalKeydown)
  }
})

onBeforeUnmount(() => {
  document.documentElement.style.overflow = ''
  window.removeEventListener('keydown', handleGlobalKeydown)
})

// Pagination: show 4 articles per page, client-side
const perPage = 4
const currentPage = ref(1)
const totalPages = computed(() => Math.max(1, Math.ceil(articles.length / perPage)))
const pagedArticles = computed(() => {
  const start = (currentPage.value - 1) * perPage
  return articles.slice(start, start + perPage)
})
const goToPage = (n) => { if (n >= 1 && n <= totalPages.value) currentPage.value = n }
const nextPage = () => { if (currentPage.value < totalPages.value) currentPage.value++ }
const prevPage = () => { if (currentPage.value > 1) currentPage.value-- }

const bgStyle = computed(() => ({
  // use a soft white translucent overlay to brighten the background while keeping a subtle green tint
  // this is more visibly lighter than a dark green overlay
  backgroundImage: `linear-gradient(to bottom, rgba(255,255,255,0.45), rgba(236,253,245,0.08)), url('${BgBerita}')`,
  backgroundSize: 'cover',
  // align background bottom to stick with footer
  backgroundPosition: 'center bottom',
}))
</script>

<template>
  <div class="font-inter text-gray-900">
    <Header />

    <Head title="Berita" />

    <main class="mt-16 min-h-screen">
      <!-- Background limited to this inner section so header stays plain -->
      <div :style="bgStyle" class="w-full">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 py-6 md:py-8">
        <!-- Judul -->
        <h1 class="text-4xl md:text-5xl font-extrabold tracking-wide text-[#2D6A4F] mb-6 md:mb-8">
          BERITA PERTANIAN
        </h1>

        <!-- List Berita -->
        <div v-if="!detailArticle" class="space-y-6">
          <article
            v-for="(a, i) in pagedArticles"
            :key="i"
            class="relative overflow-hidden rounded-2xl bg-green-50/80 backdrop-blur-md shadow-lg ring-1 ring-green-200"
          >
            <button type="button" class="w-full text-left" @click="openDetail(a, $event)">
              <div class="p-4 md:p-6">
                <div class="flex flex-col md:flex-row gap-4 md:gap-6 items-center">
                  <!-- Gambar -->
                  <div class="shrink-0 rounded-lg overflow-hidden shadow-sm flex-none">
                    <img
                      :src="a.image"
                      :alt="a.title"
                      class="w-40 h-28 md:w-48 md:h-32 object-cover rounded-lg"
                    />
                  </div>

                  <!-- Konten -->
                  <div class="flex-1 min-w-0">
                    <h2 class="text-lg md:text-xl font-semibold text-[#155e3b]">
                      {{ a.title }}
                    </h2>
                    <p class="mt-2 text-sm md:text-base text-gray-700">
                      {{ a.excerpt }}
                    </p>

                    <div class="mt-3 md:mt-2 text-right">
                      <span class="inline-block text-sm font-medium text-[#196f3d] hover:underline">
                        Baca Selengkapnya
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </button>
          </article>
  </div>

  <!-- Pagination controls -->
  <div v-if="!detailArticle" class="mt-8 flex items-center justify-between">
          <div>
            <button
              type="button"
              class="px-3 py-2 rounded-md bg-white/60 hover:bg-white text-sm text-gray-700"
              :disabled="currentPage === 1"
              @click="prevPage"
            >
              ◀
            </button>
          </div>

          <div class="flex items-center gap-3">
            <template v-for="n in totalPages" :key="n">
              <button
                type="button"
                class="w-9 h-9 rounded-full grid place-items-center text-sm font-medium"
                :class="n === currentPage ? 'bg-green-700 text-white' : 'bg-white/70 text-gray-800'"
                @click="goToPage(n)"
              >
                {{ n }}
              </button>
            </template>
          </div>

          <div>
            <button
              type="button"
              class="px-3 py-2 rounded-md bg-white/60 hover:bg-white text-sm text-gray-700"
              :disabled="currentPage === totalPages"
              @click="nextPage"
            >
              ▶
            </button>
          </div>
        </div>

        <!-- Detail view -->
        <div v-if="detailArticle" class="bg-white/70 rounded-xl p-6 md:p-10">
          <button @click="backToList" class="mb-4 inline-flex items-center gap-2 px-3 py-2 bg-white/90 rounded shadow">
            ← Kembali
          </button>

          <article class="prose lg:prose-xl max-w-none text-gray-800">
            <img :src="detailArticle.image" :alt="detailArticle.title" class="w-full h-64 md:h-96 object-cover rounded-lg mb-6" />
            <h1 class="text-2xl md:text-3xl font-bold text-[#155e3b]">{{ detailArticle.title }}</h1>
            <p class="text-sm text-gray-600">Penulis: Tim Ngundur - Selamat Berjuang Sukses</p>
            <div class="mt-4 text-gray-700 whitespace-pre-line">{{ detailArticle.content }}</div>
          </article>
        </div>
        </div>
        <Footer />
      </div>
    </main>

    <!-- Modal Detail -->
    <Transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0 translate-y-4 scale-95"
      enter-to-class="opacity-100 translate-y-0 scale-100"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100 translate-y-0 scale-100"
      leave-to-class="opacity-0 translate-y-4 scale-95"
    >
      <div v-if="selected" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" role="dialog" aria-modal="true">
        <div ref="modalContentRef" tabindex="0" class="bg-white rounded-2xl max-w-3xl w-full overflow-hidden shadow-xl max-h-[90vh]">
        <div class="relative">
          <img :src="selected.image" :alt="selected.title" class="w-full h-52 md:h-64 object-cover" />
          <button
            class="absolute top-3 right-3 bg-white/90 hover:bg-white text-gray-700 rounded-full w-9 h-9 grid place-items-center shadow"
            @click="close"
            aria-label="Tutup"
          >
            ✕
          </button>
        </div>
        <div class="p-5 md:p-6 overflow-y-auto max-h-[60vh]">
          <h3 class="text-xl md:text-2xl font-semibold text-gray-800">
            {{ selected.title }}
          </h3>
          <p class="mt-3 md:mt-4 text-gray-700 whitespace-pre-line">
            {{ selected.content }}
          </p>
          <div class="mt-5 text-right">
            <button class="px-4 py-2 rounded-lg bg-[#2D6A4F] text-white hover:bg-[#1f5c43]" @click="close">
              Tutup
            </button>
          </div>
        </div>
      </div>
  </div>
  </Transition>
<<<<<<< HEAD
    
    
=======


>>>>>>> main
  </div>
</template>
