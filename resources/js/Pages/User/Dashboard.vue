<script setup>
import { onMounted } from 'vue'
import Header from '@/Components/User/Header.vue'
import Footer from '@/Components/User/Footer.vue'
import HeroSection from '@/Components/User/HeroSection.vue'
import NewsCarousel from '@/Components/User/NewsCarousel.vue'
import FeatureSection from '@/Components/User/FeatureSection.vue'

import Band1 from '*/dashboard/bawah1.png'
import Band2 from '*/dashboard/bawah2.png'
import Band3 from '*/dashboard/bawah3.png'
import FooterLogo from '*/dashboard/footer-logo.png'
import Profil from '*/dashboard/profil.png'

function scrollToNews() {
    const el = document.getElementById('news-section')
    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' })
}
onMounted(() => window.scrollTo({ top: 0 }))
</script>

<template>
    <div class="font-inter text-gray-900">
        <!-- NAVBAR -->
        <Header />

        <!-- HERO -->
        <section class="mt-16">
            <HeroSection @readMore="scrollToNews" />
        </section>

        <!-- NEWS -->
        <section id="news-section" class="py-12">
          <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative w-full rounded-2xl overflow-hidden shadow-xl ring-1 ring-green-200">
              <div class="absolute inset-0 bg-gradient-to-r from-green-50 to-green-100"></div>
              <h2 class="relative px-6 py-6 md:px-8 md:py-7 text-3xl md:text-4xl font-extrabold text-[#285F3C] text-center">
                  Berita Pertanian
                </h2>
              </div>
                <NewsCarousel />
            </div>
        </section>

        <!-- TITLE CHIP -->
        <section class="mt-6">
          <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative w-full rounded-2xl overflow-hidden shadow-xl ring-1 ring-green-200">
              <div class="absolute inset-0 bg-gradient-to-r from-green-50 to-green-100"></div>
              <h2 class="relative px-6 py-6 md:px-8 md:py-7 text-3xl md:text-4xl font-extrabold text-[#285F3C] text-center">
                Keunggulan Alat
              </h2>
            </div>
          </div>
        </section>

        <!-- FEATURES -->
        <FeatureSection />

        <!-- IMAGE BAND -->
        <section class="py-10">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 grid md:grid-cols-3 gap-4">
              
                <div class="relative rounded-xl overflow-hidden">
                  <img :src="Band1" alt="" class="w-full h-48 md:h-56 object-cover">
                    <div class="absolute inset-0 bg-black/35"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-white text-2xl font-bold drop-shadow">Monitoring<br class="md:hidden"> Real time</span>
                  </div>
                </div>

                <div class="relative rounded-xl overflow-hidden">
                  <img :src="Band2" alt="" class="w-full h-48 md:h-56 object-cover">
                    <div class="absolute inset-0 bg-black/35"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-white text-2xl font-bold drop-shadow">Irigasi<br class="md:hidden"> Otomatis</span>
                  </div>
                </div>
                
                <div class="relative rounded-xl overflow-hidden">
                  <img :src="Band3" alt="" class="w-full h-48 md:h-56 object-cover">
                    <div class="absolute inset-0 bg-black/35"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-white text-2xl font-bold drop-shadow">Dashboard<br class="md:hidden"> Web</span>
                  </div>
                </div>
            </div>
          </section>

        <!-- FOOTER -->
        <Footer />
    </div>
</template>