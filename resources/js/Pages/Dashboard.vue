<script setup>
import { computed } from 'vue'
import { usePage, useForm } from '@inertiajs/vue3'

const page = usePage()
const user = computed(() => page.props.auth.user)

const logoutForm = useForm({})

function logout() {
    logoutForm.post(route('logout'))
}

function goToLogin() {
    window.location.href = route('login')
}

function goToRegister() {
    window.location.href = route('register')
}

function scrollToNews() {
    const newsSection = document.getElementById('news-section')
    if (newsSection) {
        newsSection.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        })
    }
}
</script>

<template>

    <div class="text-gray-900 font-inter">
        <!-- NAVBAR -->
        <header class="fixed inset-x-0 top-0 z-50 shadow-sm bg-white/90 backdrop-blur">
            <nav class="flex items-center justify-between w-full h-16 px-0 sm:px-2 lg:px-4 ">
                <div class="flex items-center gap-2">
                        <div class="flex justify-start">
                    <img src="/assets/dashboard/logo-ngundur.png" alt="NGUNDUR" class="h-20 w-50 ">
                        </div>
                </div>
                <ul class="items-center hidden gap-6 text-sm md:flex">
                    <li><a href="#" class="font-bold text-green-700">Home</a></li>
                    <li><a href="#" class="hover:text-green-700">Toko</a></li>
                    <li><a href="#" class="hover:text-green-700">Berita</a></li>
                    <li><a href="#" class="hover:text-green-700">Tentang</a></li>
                    <li><a href="#" class="hover:text-green-700">Blog</a></li>
                </ul>
                <div class="items-center hidden gap-2 sm:flex">
                    <div v-if="!user">
                        <button class="px-4 py-2 text-white bg-green-500 rounded-full hover:bg-green-600 font-inter" @click="goToLogin">Masuk</button>
                        <button class="px-4 py-2 text-green-700 bg-green-100 rounded-full hover:bg-green-200 font-inter" @click="goToRegister">Daftar</button>
                    </div>
                    <div v-else class="flex items-center gap-4">
                        <!-- Shopping Cart Icon -->
                        <svg class="w-6 h-6 text-gray-700 cursor-pointer hover:text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.6 8M7 13L5.4 5M7 13l2.25 2.25M16 16a2 2 0 104 0 2 2 0 00-4 0zM9 16a2 2 0 104 0 2 2 0 00-4 0z"></path>
                        </svg>

                        <!-- Profile Section -->
                        <div class="flex items-center gap-2">
                            <img src="/assets/dashboard/profil.png" alt="Profil" class="w-8 h-8 border border-green-700 rounded-full">
                            <span class="font-semibold text-green-700">{{ user.nama }}</span>
                            <button @click="logout" :disabled="logoutForm.processing" class="px-3 py-1 text-red-700 bg-red-100 rounded-full hover:bg-red-200 font-inter">
                                {{ logoutForm.processing ? 'Logging out...' : 'Logout' }}
                            </button>
                        </div>
                    </div>
                </div>
            </nav>
        </header>
        <!-- HERO -->
        <section class="relative flex justify-center">
            <div class="relative w-[1920px] h-[1000px] overflow-hidden rounded-2x1">
                <img src="/assets/dashboard/bg-homepage.png" alt="Leaves" class="absolute inset-0 object-cover w-full h-full">
                <div class="relative z-10 px-8 sm:px-40 lg:px-14 top-1/4">
                    <h1 class="leading-tight text-white text-5x1 sm:text-8xl font-Urbanist">
                        Stech Smart<br>Garden
                    </h1>
                    <p class="max-w-xl mt-4 text-white/90 font-inter">
                        Stech Smart Garden adalah sistem irigasi otomatis berbasis IoT skala kecil yang dirancang untuk membantu petani dan masyarakat yang hobi berkebun dalam mengelola kebutuhan air secara efisien.
                    </p>
                    <div class="flex flex-wrap gap-3 mt-6">
                        <a href="#" class="px-5 py-2 text-green-600 transition bg-white rounded-lg shadow hover:bg-green-100 font-inter">
                            Beli Sekarang
                        </a>
                        <button @click="scrollToNews" class="px-5 py-2 text-white transition border rounded-lg border-white/80 hover:bg-white hover:text-green-700 font-inter">
                            Baca Lengkap
                        </button>
                    </div>
                </div>
            </div>
        </section>
        <!-- NEWS STRIP -->
        <section id="news-section" class="py-10">
            <div class="relative max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
                <button class="absolute items-center justify-center hidden w-8 h-8 text-gray-600 -translate-y-1/2 bg-white rounded-full shadow md:flex -left-3 top-1/2">‹</button>
                <button class="absolute items-center justify-center hidden w-8 h-8 text-gray-600 -translate-y-1/2 bg-white rounded-full shadow md:flex -right-3 top-1/2">›</button>
                <div class="grid gap-6 md:grid-cols-3">
                    <article class="overflow-hidden bg-white shadow rounded-xl hover:shadow-md">
                        <img src="/assets/dashboard/berita-kiri.png" alt="" class="object-cover w-full h-40">
                        <div class="p-4">
                            <h3 class="font-semibold">Indonesia Dorong Pertanian Ramah Lingkungan dengan Teknologi IoT</h3>
                        </div>
                    </article>
                    <article class="overflow-hidden bg-white shadow rounded-xl hover:shadow-md">
                        <img src="/assets/dashboard/berita-1.png" alt="" class="object-cover w-full h-40">
                        <div class="p-4">
                            <h3 class="font-semibold">Petani Sayuran Mulai Terapkan Irigasi Otomatis untuk Hemat Air</h3>
                        </div>
                    </article>
                    <article class="overflow-hidden bg-white shadow rounded-xl hover:shadow-md">
                        <img src="/assets/dashboard/berita-kanan.png" alt="" class="object-cover w-full h-40">
                        <div class="p-4">
                            <h3 class="font-semibold">Tren Pertanian Urban: Berkebun di Lahan Sempit dengan Smart Garden</h3>
                        </div>
                    </article>
                </div>
            </div>
        </section>
        <!-- TITLE CHIP -->
        <section class="mt-4">
            <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">
                <div class="inline-block px-5 py-2 text-green-800 bg-green-100 border border-green-200 shadow-sm rounded-xl">
                    <span class="font-semibold">Keunggulan Alat</span>
                </div>
            </div>
        </section>
        <!-- FEATURES A -->
        <section class="py-10">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-[360px_1fr] gap-8 items-start">
                <div class="relative">
                    <img src="/assets/dashboard/profil.png" alt="Bear" class="w-full max-w-xs mx-auto lg:max-w-none drop-shadow-xl">
                </div>
                <div class="p-6 bg-white border border-gray-100 shadow-md rounded-2xl">
                    <h3 class="text-xl font-semibold text-gray-900">Modular &amp; Scalable</h3>
                    <p class="mt-3 text-gray-700">
                        Stech Smart Garden dirancang modular dan scalable: mudah menambah sensor (kelembaban, suhu, intensitas cahaya, pH & nutrisi) dan integrasi ke dashboard web.
                        Data tampil secara real-time sehingga mudah dianalisis dan divisualisasikan. Cocok untuk pertanian kecil, belajar IoT, atau riset.
                    </p>
                </div>
            </div>
        </section>
        <!-- FEATURES B -->
        <section class="pb-6">
            <div class="grid items-center max-w-6xl gap-8 px-4 mx-auto sm:px-6 lg:px-8 lg:grid-cols-2">
                <div class="order-2 p-6 bg-white border border-gray-100 shadow-md rounded-2xl lg:order-1">
                    <h3 class="text-xl font-semibold text-gray-900">Hemat Energi</h3>
                    <p class="mt-3 text-gray-700">
                        Varian dengan panel surya mendukung operasi mandiri memanfaatkan energi matahari. Hemat biaya operasional, mengurangi jejak karbon, dan ideal di area minim pasokan listrik.
                    </p>
                </div>
                <div class="order-1 lg:order-2">
                    <img src="/assets/dashboard/keunggulan-alat-1.png" alt="Solar" class="object-cover w-full border border-gray-100 shadow-md rounded-2xl">
                </div>
            </div>
        </section>
        <!-- FEATURES C -->
        <section class="py-4">
            <div class="grid items-start max-w-6xl gap-8 px-4 mx-auto sm:px-6 lg:px-8 lg:grid-cols-2">
                <div>
                    <img src="/assets/dashboard/keunggukan-alat-2.png" alt="Book" class="object-cover w-full border border-gray-100 shadow-md rounded-2xl">
                </div>
                <div class="p-6 bg-white border border-gray-100 shadow-md rounded-2xl">
                    <h3 class="text-xl font-semibold text-gray-900">Open Source &amp; Edukatif</h3>
                    <p class="mt-3 text-gray-700">
                        Dilengkapi buku panduan perakitan dan source code. Pengguna bisa memahami alur kerja sistem hingga memodifikasi sesuai kebutuhan.
                    </p>
                </div>
            </div>
        </section>
        <!-- IMAGE BAND -->
        <section class="py-10">
            <div class="grid max-w-6xl gap-4 px-4 mx-auto sm:px-6 lg:px-8 md:grid-cols-3">
                <div class="relative overflow-hidden rounded-xl">
                    <img src="/assets/dashboard/foto-bawah-3.png" alt="" class="object-cover w-full h-48 md:h-56">
                    <div class="absolute inset-0 bg-black/35"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-2xl font-bold text-white drop-shadow">Monitoring<br class="md:hidden"> Real time</span>
                    </div>
                </div>
                <div class="relative overflow-hidden rounded-xl">
                    <img src="/assets/dashboard/footer-logo.png" alt="" class="object-cover w-full h-48 md:h-56">
                    <div class="absolute inset-0 bg-black/35"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-2xl font-bold text-white drop-shadow">Irigasi<br class="md:hidden"> Otomatis</span>
                    </div>
                </div>
                <div class="relative overflow-hidden rounded-xl">
                    <img src="/assets/dashboard/profil.png" alt="" class="object-cover w-full h-48 md:h-56">
                    <div class="absolute inset-0 bg-black/35"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-2xl font-bold text-white drop-shadow">Dasboard<br class="md:hidden"> Web</span>
                    </div>
                </div>
            </div>
        </section>
        <!-- FOOTER -->
        <footer class="relative mt-10">
            <div class="relative text-white bg-gradient-to-r from-green-800 to-green-700">
                <div class="absolute inset-y-0 right-0 hidden w-1/3 opacity-30 md:block">
                    <img src="/assets/dashboard/footer-logo.png" alt="" class="object-cover w-full h-full">
                </div>
                <div class="relative grid max-w-6xl gap-8 px-4 py-10 mx-auto sm:px-6 lg:px-8 md:grid-cols-2">
                    <div>
                        <h4 class="text-xl font-semibold">NGUNDUR</h4>
                        <p class="mt-3 text-sm leading-7 text-white/90">
                            Jl. Ringroad Barat, Dowangan, Banyuraden,<br>
                            Gamping, Sleman, Daerah Istimewa Yogyakarta
                        </p>
                        <div class="mt-4 space-y-1 text-sm text-white/90">
                            <p><span class="font-semibold">Phone</span> +62 000-000-000</p>
                            <p><span class="font-semibold">WA</span> +62 000-000-000</p>
                            <p><span class="font-semibold">Hours</span> Senin–Jum'at 09.00–16.00</p>
                        </div>
                        <div class="h-px max-w-sm mt-6 bg-white/30"></div>
                        <p class="mt-4 text-xs text-white/70">© 2025 NGUNDUR</p>
                    </div>
                    <div class="flex items-center justify-end">
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
