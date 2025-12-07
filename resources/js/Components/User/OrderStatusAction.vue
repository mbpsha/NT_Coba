<script setup>
import { computed } from 'vue'

const props = defineProps({
    status: { type: String, required: true },
    paymentStatus: { type: String, default: null },
    orderId: { type: [Number, String], required: true }
})

// compute activeIndex dari status
const activeIndex = computed(() => {
    switch (props.status) {
        case 'pending': return 0
        case 'diproses': return 1
        case 'dikirim': return 2
        case 'selesai': return 3
        case 'dibatalkan': return -1
        default: return -1
    }
})
</script>

<template>
    <section class="w-full">
        <div class="rounded-xl bg-white/70 backdrop-blur ring-1 ring-gray-200 p-5 shadow-sm">
        <div class="grid grid-cols-3 gap-4">

            <!-- STEP 1: Pembayaran Terverifikasi -->
            <div class="flex flex-col items-center text-center gap-2">
            <div :class="[
                'w-12 h-12 grid place-items-center rounded-xl shadow-sm',
                activeIndex >= 0 ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400'
                ]">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12l2 2 4-4M12 22a10 10 0 110-20 10 10 0 010 20z"/>
                </svg>
            </div>
            <p :class="['text-xs', activeIndex >= 0 ? 'text-gray-800' : 'text-gray-400']">
                Pembayaran Terverifikasi
            </p>
            </div>

            <!-- STEP 2: Dalam Produksi -->
            <div class="flex flex-col items-center text-center gap-2">
            <div :class="[
                'w-12 h-12 grid place-items-center rounded-xl shadow-sm',
                activeIndex >= 1 ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400'
                ]">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        d="M9.75 3l.91 2.73M14.25 3l-.91 2.73M4 7h16M6 7l1.5 11h9L18 7M8 11h8"/>
                </svg>
            </div>
            <p :class="['text-xs', activeIndex >= 1 ? 'text-gray-800' : 'text-gray-400']">
                Dalam Produksi
            </p>
            </div>

            <!-- STEP 3: Dalam Pengiriman -->
            <div class="flex flex-col items-center text-center gap-2">
            <div :class="[
                'w-12 h-12 grid place-items-center rounded-xl shadow-sm',
                activeIndex >= 2 ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400'
                ]">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        d="M3 7h11v8H3zM14 10h4l3 3v2h-7v-5zM5 21a2 2 0 100-4 2 2 0 000 4zm10 0a2 2 0 100-4 2 2 0 000 4z"/>
                </svg>
            </div>
            <p :class="['text-xs', activeIndex >= 2 ? 'text-gray-800' : 'text-gray-400']">
                Dalam Pengiriman
            </p>
            </div>
        </div>
        </div>
    </section>
</template>
