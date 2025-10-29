<script setup>
import { Link } from '@inertiajs/vue3'

const isActive = (routeName) => {
    return route().current(routeName)
}
</script>

<template>
    <aside class="fixed left-0 top-0 h-screen w-64 bg-gradient-to-b from-green-600 to-green-700 text-white shadow-xl">
        <div class="p-6">
            <h1 class="text-2xl font-bold">ADMIN PANEL</h1>
        </div>

        <nav class="mt-6">
            <Link
                :href="route('admin.dashboard')"
                class="flex items-center gap-3 px-6 py-3 hover:bg-green-500 transition-colors"
                :class="{ 'bg-green-500': isActive('admin.dashboard') }"
            >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                </svg>
                <span>Dashboard</span>
            </Link>

            <Link
                :href="route('admin.products.index')"
                class="flex items-center gap-3 px-6 py-3 hover:bg-green-500 transition-colors"
                :class="{ 'bg-green-500': isActive('admin.products.*') }"
            >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"></path>
                </svg>
                <span>Products</span>
            </Link>

            <Link
                :href="route('admin.users.index')"
                class="flex items-center gap-3 px-6 py-3 hover:bg-green-500 transition-colors"
                :class="{ 'bg-green-500': isActive('admin.users.*') }"
            >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                </svg>
                <span>Users</span>
            </Link>

            <Link
                :href="route('admin.orders.index')"
                class="flex items-center gap-3 px-6 py-3 hover:bg-green-500 transition-colors"
                :class="{ 'bg-green-500': isActive('admin.orders.*') }"
            >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                </svg>
                <span>Orders</span>
            </Link>

            <Link
                :href="route('admin.payments.index')"
                class="flex items-center gap-3 px-6 py-3 hover:bg-green-500 transition-colors"
                :class="{ 'bg-green-500': isActive('admin.payments.*') }"
            >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"></path>
                </svg>
                <span>Payment Verification</span>
            </Link>

            <div class="border-t border-green-500 my-4"></div>

            <Link
                :href="route('logout')"
                method="post"
                as="button"
                class="flex items-center gap-3 px-6 py-3 hover:bg-red-500 transition-colors w-full text-left"
            >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"></path>
                </svg>
                <span>Logout</span>
            </Link>
        </nav>
    </aside>
</template>
