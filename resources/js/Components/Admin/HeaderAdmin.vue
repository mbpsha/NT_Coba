<script setup>
import { computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import Logo from '*/dashboard/logo-tandur.png'

const page = usePage()
const user = computed(() => page.props.auth.user)

const onLogout = () => {
    router.post(route('logout'), {}, {
        onSuccess: () => router.visit('/', { replace: true, preserveState: false })
    })
}
</script>

<template>
    <header class="fixed top-0 right-0 left-64 h-16 bg-gradient-to-r from-green-300 to-green-300 shadow-md z-15">
        <div class="flex items-center justify-between h-full px-8">
            <div class="flex items-center gap-3">
                <img :src="Logo" alt="NGUNDUR" class="h-12" />
            </div>

            <div class="flex items-center gap-4 text-black">
                <span class="text-lg font-semibold">Admin</span>
                <div class="flex items-center gap-2 bg-white/20 rounded-full px-4 py-2">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium">{{ user?.nama || 'Admin' }}</span>
                </div>

                <!-- tombol logout -->
                <button @click="onLogout" class="px-3 py-1.5 rounded-md bg-white/20 hover:bg-red-300 text-black text-sm">
                    Log-out
                </button>
            </div>
        </div>
    </header>
</template>