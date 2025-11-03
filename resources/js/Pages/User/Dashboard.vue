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
    <div class="text-gray-900 font-inter">
        <!-- NAVBAR -->
        <Header />

    <!-- HERO -->
    <section class="mt-16">
        <HeroSection @readMore="scrollToNews" />
    </section>

    <!-- NEWS -->
    <section id="news-section" class="py-12">
        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
            <NewsCarousel />
        </div>
    </section>

    <!-- TITLE CHIP -->
    <section class="mt-2">
        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
            <div class="inline-block px-5 py-2 text-green-800 bg-green-100 border border-green-200 shadow-sm rounded-xl">
            <span class="font-semibold">Keunggulan Alat</span>
            </div>
        </div>
    </section>

    <!-- FEATURES -->
    <FeatureSection />

    <!-- IMAGE BAND -->
    <section class="py-10">
        <div class="grid max-w-6xl gap-4 px-4 mx-auto sm:px-6 lg:px-8 md:grid-cols-3">
            <div class="relative overflow-hidden rounded-xl">
            <img :src="Band1" alt="" class="object-cover w-full h-48 md:h-56">
            <div class="absolute inset-0 bg-black/35"></div>
            <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-2xl font-bold text-white drop-shadow">Monitoring<br class="md:hidden"> Real time</span>
            </div>
            </div>
            <div class="relative overflow-hidden rounded-xl">
            <img :src="Band2" alt="" class="object-cover w-full h-48 md:h-56">
            <div class="absolute inset-0 bg-black/35"></div>
            <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-2xl font-bold text-white drop-shadow">Irigasi<br class="md:hidden"> Otomatis</span>
            </div>
            </div>
            <div class="relative overflow-hidden rounded-xl">
            <img :src="Band3" alt="" class="object-cover w-full h-48 md:h-56">
            <div class="absolute inset-0 bg-black/35"></div>
            <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-2xl font-bold text-white drop-shadow">Dashboard<br class="md:hidden"> Web</span>
            </div>
            </div>
        </div>
        </section>

        <!-- FOOTER -->
        <Footer />
    </div>
</template>
