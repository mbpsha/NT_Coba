<script setup>
import { Link, router } from '@inertiajs/vue3'
import axios from 'axios'

const isActive = (routeName) => {
    return route().current(routeName)
}

async function logout() {
    try {
        await axios.post('/logout', {}, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        router.visit('/dashboard', { replace: true, preserveState: false })
    } catch (error) {
        console.error('Logout failed:', error)
        router.visit('/dashboard', { replace: true, preserveState: false })
    }
}
</script>

<template>
    <aside class="fixed top-0 left-0 w-64 h-screen text-black shadow-xl bg-gradient-to-b from-green-200 to-green-260">
        <div class="p-6">
            <h1 class="text-2xl font-bold">ADMIN PANEL</h1>
        </div>

        <nav class="mt-6">
            <Link
                :href="route('admin.dashboard')"
                class="flex items-center px-6 py-3 transition hover:bg-green-500"
                :class="{ 'bg-green-500': isActive('admin.dashboard') }"
            >
                Dashboard
            </Link>

            <Link
                :href="route('admin.users.index')"
                class="flex items-center px-6 py-3 transition hover:bg-green-500"
                :class="{ 'bg-green-500': isActive('admin.users.index') }"
            >
                Users
            </Link>

            <Link
                :href="route('admin.products.index')"
                class="flex items-center px-6 py-3 transition hover:bg-green-500"
                :class="{ 'bg-green-500': isActive('admin.products.index') }"
            >
                Products
            </Link>

            <Link
                :href="route('admin.payments.index')"
                class="flex items-center px-6 py-3 transition hover:bg-green-500"
                :class="{ 'bg-green-500': isActive('admin.payments.index') }"
            >
                Payments
            </Link>

            <Link
                :href="route('admin.orders.index')"
                class="flex items-center px-6 py-3 transition hover:bg-green-500"
                :class="{ 'bg-green-500': isActive('admin.orders.index') }"
            >
                Orders
            </Link>

            <Link
                :href="route('admin.news.index')"
                class="flex items-center px-6 py-3 transition hover:bg-green-500"
                :class="{ 'bg-green-500': isActive('admin.news.index') }"
            >
                News
            </Link>

            <!-- Logout Button -->
            <button
                @click="logout"
                class="flex items-center w-full px-6 py-3 mt-4 text-left transition hover:bg-red-500"
            >
                Logout
            </button>
        </nav>
    </aside>
</template>
