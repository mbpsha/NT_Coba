<script setup>
import { ref, computed, watch } from 'vue'
import { Link, usePage, useForm, router } from '@inertiajs/vue3'

import Logo from '*/dashboard/logo-ngundur.png'

const page = usePage()
const user = computed(()=> page.props.auth?.user || null)
const isAuth = computed(()=> !!user.value)

const cartCount = computed(()=> page.props.cartCount || 0)

const showToast = ref(false)
const toastMsg = ref('')
watch(()=> page.props.flash?.cart_added, v=>{
  if (v) {
    toastMsg.value = v
    showToast.value = true
    setTimeout(()=> showToast.value = false, 2200)
  }
})

const profileMenu = ref(false)
function toggleProfile(){ profileMenu.value = !profileMenu.value }

// NEW: mobile hamburger state
const mobileOpen = ref(false)
function toggleMobile(){ mobileOpen.value = !mobileOpen.value }
// auto-close saat route berubah
watch(()=> page.url, ()=> { mobileOpen.value = false })

const logoutForm = useForm({})
function logout() {
  logoutForm.post(route('logout'),
  {
        preserveState: false,
        preserveScroll: false,
        onSuccess: () => {
            window.location.href = route('dashboard')
        }
    })
}

function goCart() {
  router.visit(route('cart.index'))
}
</script>

<template>
  <header class="fixed inset-x-0 top-0 z-50 shadow-sm bg-white/90 backdrop-blur">
    <nav class="flex items-center justify-between h-16 px-4 mx-auto max-w-7xl">
      <div class="flex items-center gap-3">
        <Link :href="route('dashboard')" class="flex items-center gap-2">
          <img :src="Logo" alt="NGUNDUR" class="h-11" />
        </Link>
      </div>

      <!-- NAV DESKTOP -->
      <ul class="items-center hidden gap-6 text-sm md:flex">
        <li><Link :href="route('dashboard')" :class="route().current('dashboard')?'text-green-700 font-semibold':'text-gray-800 hover:text-green-700'">Home</Link></li>
        <li><Link :href="route('toko')" :class="page.url.startsWith('/toko')?'text-green-700 font-semibold':'text-gray-800 hover:text-green-700'">Toko</Link></li>
        <li><Link :href="route('berita')" :class="page.url.startsWith('/berita')?'text-green-700 font-semibold':'text-gray-800 hover:text-green-700'">Berita</Link></li>
        <li><Link :href="route('about')" :class="route().current('about')?'text-green-700 font-semibold':'text-gray-800 hover:text-green-700'">Tentang</Link></li>
        <li><Link :href="route('blog')" :class="page.url.startsWith('/blog')?'text-green-700 font-semibold':'text-gray-800 hover:text-green-700'">FAQ</Link></li>
      </ul>

      <!-- ACTIONS DESKTOP -->
      <div class="items-center hidden gap-3 md:flex">
        <template v-if="!isAuth">
          <Link :href="route('login')" class="px-4 py-2 text-sm text-white bg-green-600 rounded-full hover:bg-green-700">Masuk</Link>
          <Link :href="route('register')" class="px-4 py-2 text-sm text-green-700 bg-green-100 rounded-full hover:bg-green-200">Daftar</Link>
        </template>

        <template v-else>
          <!-- Icon Keranjang -->
            <Link :href="route('cart.index')" class="relative p-2 rounded-full hover:bg-gray-100" aria-label="Keranjang">
              <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              <span v-if="cartCount>0"
                    class="absolute -top-0.5 -right-0.5 bg-red-600 text-white text-[10px] font-semibold px-1.5 py-0.5 rounded-full leading-none">
                {{ cartCount }}
              </span>
            </Link>


          <!-- Profil -->
          <div class="relative">
            <button @click="toggleProfile"
                    class="flex items-center justify-center w-10 h-10 text-green-700 bg-green-100 rounded-full hover:bg-green-200 ring-1 ring-green-300">
              <svg class="w-6 h-6" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
              </svg>
            </button>
            <div v-if="profileMenu" class="absolute right-0 z-50 w-48 mt-2 overflow-hidden bg-white rounded-md shadow ring-1 ring-black/5">
              <div class="px-4 py-3 text-sm border-b">
                <p class="font-medium text-gray-900 truncate">{{ user.nama || user.username }}</p>
                <p v-if="user.email" class="text-xs text-gray-500 truncate">{{ user.email }}</p>
              </div>
              <Link href="/profile" class="block px-4 py-2 text-sm hover:bg-gray-50">
                <span class="flex items-center gap-2">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                  </svg>
                  Profil
                </span>
              </Link>
              <Link :href="route('orders.my')" class="block px-4 py-2 text-sm hover:bg-gray-50">
                <span class="flex items-center gap-2">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                  </svg>
                  Pesanan Saya
                </span>
              </Link>
              <button @click="logout" :disabled="logoutForm.processing"
                      class="w-full px-4 py-2 text-sm text-left text-red-600 border-t hover:bg-gray-50">
                <span class="flex items-center gap-2">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                  </svg>
                  Keluar
                </span>
              </button>
            </div>
          </div>
        </template>
      </div>

      <!-- HAMBURGER MOBILE -->
      <button class="inline-flex items-center justify-center w-10 h-10 rounded-md hover:bg-gray-100 md:hidden"
              @click="toggleMobile" aria-label="Menu">
        <svg v-if="!mobileOpen" class="w-6 h-6 text-green-700" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
        <svg v-else class="w-6 h-6 text-green-700" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </nav>

    <!-- PANEL MOBILE -->
    <transition name="fade">
      <div v-if="mobileOpen" class="md:hidden border-t bg-white/95 backdrop-blur">
        <div class="max-w-7xl mx-auto px-4 py-3 space-y-1 text-sm">
          <Link :href="route('dashboard')" class="block px-3 py-2 rounded hover:bg-gray-100"
                :class="route().current('dashboard')?'text-green-700 font-semibold':'text-gray-800'">Home</Link>
          <Link :href="route('toko')" class="block px-3 py-2 rounded hover:bg-gray-100"
                :class="page.url.startsWith('/toko')?'text-green-700 font-semibold':'text-gray-800'">Toko</Link>
          <Link :href="route('berita')" class="block px-3 py-2 rounded hover:bg-gray-100"
                :class="page.url.startsWith('/berita')?'text-green-700 font-semibold':'text-gray-800'">Berita</Link>
          <Link :href="route('about')" class="block px-3 py-2 rounded hover:bg-gray-100"
                :class="route().current('about')?'text-green-700 font-semibold':'text-gray-800'">Tentang</Link>
          <Link :href="route('blog')" class="block px-3 py-2 rounded hover:bg-gray-100"
                :class="page.url.startsWith('/blog')?'text-green-700 font-semibold':'text-gray-800'">FAQ</Link>

          <!-- optional auth/cart/profile mobile -->
          <div class="mt-2 border-t pt-2">
            <template v-if="!isAuth">
              <div class="flex gap-2">
                <Link :href="route('login')" class="flex-1 px-4 py-2 text-center text-white bg-green-600 rounded hover:bg-green-700">Masuk</Link>
                <Link :href="route('register')" class="flex-1 px-4 py-2 text-center text-green-700 bg-green-100 rounded hover:bg-green-200">Daftar</Link>
              </div>
            </template>
            <template v-else>
              <div class="flex items-center gap-2">
                <button @click="goCart" class="flex-1 px-4 py-2 rounded bg-green-50 text-green-700 hover:bg-green-100">Keranjang ({{ cartCount }})</button>
                <button @click="toggleProfile" class="w-10 h-10 rounded-full bg-green-100 text-green-700 hover:bg-green-200">
                  <svg class="w-6 h-6 mx-auto" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                </button>
              </div>
            </template>
          </div>
        </div>
      </div>
    </transition>

    <!-- Toast -->
    <transition name="fade">
      <div v-if="showToast"
            class="fixed px-4 py-2 text-sm text-white bg-green-600 rounded-md shadow top-20 right-4">
        {{ toastMsg }}
      </div>
    </transition>
  </header>
</template>

<style scoped>
.fade-enter-active,.fade-leave-active{transition:opacity .25s}
.fade-enter-from,.fade-leave-to{opacity:0}
</style>