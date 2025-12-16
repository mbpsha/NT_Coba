<script setup>
import { computed } from 'vue'
import FooterBg from '*/dashboard/footer-logo.png'

// Smoothly blend the image into the green background
const imageBlendStyle = computed(() => ({
    backgroundImage: `linear-gradient(to left, rgba(71,118,74,0), rgba(71,118,74,0.85) 65%, #47764A 100%), url('${FooterBg}')`,
    backgroundSize: 'cover',
    backgroundPosition: 'center',
}))
</script>

<template>
    <footer class="w-full"> <!-- keep full width -->
        <div class="relative overflow-hidden text-white bg-[#47764A]"> <!-- overflow-hidden to avoid seams -->
            <!-- Image + gradient overlay that fades into background color -->
            <div
                class="absolute inset-y-0 right-0 hidden w-1/3 bg-right bg-no-repeat bg-cover pointer-events-none md:block"
                :style="imageBlendStyle"
            ></div>

            <div class="relative z-10 grid max-w-6xl grid-cols-1 gap-8 px-4 py-10 mx-auto sm:px-6 lg:grid-cols-2 lg:px-8">
                <div>
                    <h4 class="text-xl font-semibold">TANDUR</h4>
                    <p class="mt-3 text-sm leading-7 text-white/90">
                        Jl. Ringroad Barat, Dowangan, Banyuraden,<br>
                        Gamping, Sleman, Daerah Istimewa Yogyakarta
                    </p>
                    <div class="mt-4 space-y-1 text-sm text-white/90">
                        <p><span class="font-semibold">Phone</span> +62 813-2749-5481</p>
                        <p><span class="font-semibold">WA</span> +62 813-2749-5481</p>
                        <p><span class="font-semibold">Service Hours</span> Senin–Jum'at 09.00–16.00</p>
                    </div>
                    <div class="h-px max-w-sm mt-6 bg-white/30"></div>
                    <p class="mt-4 text-xs text-white/70">© 2025 TANDUR</p>
                </div>

                <div class="flex items-center justify-end"></div>
            </div>
        </div>
    </footer>
</template>
