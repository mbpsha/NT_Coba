<script setup>
import Header from '@/component/Header.vue'    
import Footer from '@/component/Footer.vue'
import { Head } from '@inertiajs/vue3'
</script>

<template>
  <div class="min-h-screen text-gray-900 bg-gray-100 font-inter">
    <Header />
    <Head title="Blog" />

    <section
      class="mt-16 h-[260px] md:h-[320px] relative flex items-center bg-cover bg-right"
      style="background-image: linear-gradient(to bottom, rgba(128,128,128,0.65), rgba(128,128,128,0.65)), url('/assets/dashboard/bg-blog.jpg'); background-size: cover; background-position: center;"
    >
      <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
        <h1 class="text-4xl font-extrabold text-white md:text-5xl drop-shadow">Blog</h1>
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

          <section class="mt-12 grid gap-8 rounded-2xl border border-green-100 bg-gradient-to-r from-green-700 via-green-600 to-green-500 p-8 text-white shadow-lg md:grid-cols-[1.2fr_0.8fr]">
            <div class="space-y-3">
              <span class="text-xs font-semibold uppercase tracking-[0.3em] text-green-100">Ngundur</span>
              <h3 class="text-3xl font-bold leading-snug">Smart Garden untuk Pertanian Modern</h3>
              <p class="text-sm md:text-base text-green-100/90">
                Integrasi sensor, kontrol otomatis, dan manajemen energi terbarukan menghadirkan pengalaman berkebun yang praktis
                sekaligus berkelanjutan. Solusi ini membantu mempertahankan kelembaban ideal dan meminimalkan penggunaan air.
              </p>
            </div>

            <div class="relative p-6 rounded-xl bg-white/10 backdrop-blur">
              <p class="text-sm text-green-50">
                Sistem mampu mengirim data ke dashboard monitoring, sehingga pengguna dapat memantau kondisi tanaman kapan saja.
                Fitur jadwal pintar memungkinkan penyesuaian sesuai kebutuhan varietas tanaman yang berbeda.
              </p>
            </div>
          </section>
        </div>
      </div>
    </main>

    <Footer />
  </div>
</template>
