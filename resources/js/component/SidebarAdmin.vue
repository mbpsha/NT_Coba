<script setup>
import { Link, useForm } from '@inertiajs/vue3'

const isActive = (routeName) => {
    return route().current(routeName)
}

const logoutForm = useForm({})
function logout() {
    logoutForm.post(route('logout'), {
        preserveState: false,
        preserveScroll: false,
        onSuccess: () => {
            window.location.href = route('dashboard')
        }
    })
}
</script>

<template>
    <aside class="fixed top-0 left-0 w-64 h-screen text-white shadow-xl bg-gradient-to-b from-green-600 to-green-700">
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
                :disabled="logoutForm.processing"
                class="flex items-center w-full px-6 py-3 mt-4 text-left transition hover:bg-red-500"
            >
                {{ logoutForm.processing ? 'Logging out...' : 'Logout' }}
            </button>
        </nav>
    </aside>
</template>