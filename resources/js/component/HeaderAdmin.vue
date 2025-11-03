<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import Logo from '*/dashboard/logo-ngundur.png'

const page = usePage()
const user = computed(() => page.props.auth.user)
</script>

<template>
    <header class="fixed top-0 right-0 left-64 h-16 bg-gradient-to-r from-green-400 to-green-500 shadow-md z-10">
        <div class="flex items-center justify-between h-full px-8">
            <div class="flex items-center gap-3">
                <img :src="Logo" alt="NGUNDUR" class="h-12">
            </div>

            <div class="flex items-center gap-4 text-white">
                <span class="text-lg font-semibold">Admin</span>
                <div class="flex items-center gap-2 bg-white/20 rounded-full px-4 py-2">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">{{ user?.nama || 'Admin' }}</span>
                </div>
            </div>
        </div>
    </header>
</template>
