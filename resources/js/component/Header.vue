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
// tampilkan juga toast umum (mis. setelah konfirmasi pembayaran)
watch(()=> page.props.flash?.toast, v=>{
  if (v) {
    toastMsg.value = v
    showToast.value = true
    setTimeout(()=> showToast.value = false, 2500)
  }
})

const profileMenu = ref(false)
function toggleProfile(){ profileMenu.value = !profileMenu.value }

const logoutForm = useForm({})
function doLogout(){
  logoutForm.post(route('logout'))
}
</script>

<template>
  <header class="fixed inset-x-0 top-0 z-50 bg-white/90 backdrop-blur shadow-sm">
    <nav class="h-16 max-w-7xl mx-auto px-4 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <Link :href="route('dashboard')" class="flex items-center gap-2">
          <img :src="Logo" alt="NGUNDUR" class="h-11" />
        </Link>
      </div>

      <ul class="hidden md:flex items-center gap-6 text-sm">
        <li><Link :href="route('dashboard')" :class="route().current('dashboard')?'text-green-700 font-semibold':'text-gray-800 hover:text-green-700'">Home</Link></li>
        <li><Link :href="route('toko')" :class="page.url.startsWith('/toko')?'text-green-700 font-semibold':'text-gray-800 hover:text-green-700'">Toko</Link></li>
        <li><Link :href="route('berita')" :class="page.url.startsWith('/berita')?'text-green-700 font-semibold':'text-gray-800 hover:text-green-700'">Berita</Link></li>
        <li><Link :href="route('about')" :class="route().current('about')?'text-green-700 font-semibold':'text-gray-800 hover:text-green-700'">Tentang</Link></li>
        <li><Link :href="route('blog')" :class="page.url.startsWith('/blog')?'text-green-700 font-semibold':'text-gray-800 hover:text-green-700'">FaQ</Link></li>
      </ul>

      <div class="flex items-center gap-3">
        <template v-if="!isAuth">
          <Link :href="route('login')" class="px-4 py-2 rounded-full bg-green-600 text-white text-sm hover:bg-green-700">Masuk</Link>
          <Link :href="route('register')" class="px-4 py-2 rounded-full bg-green-100 text-green-700 text-sm hover:bg-green-200">Daftar</Link>
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
                    class="h-10 w-10 rounded-full bg-green-100 text-green-700 flex items-center justify-center hover:bg-green-200 ring-1 ring-green-300">
              <svg class="w-6 h-6" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
              </svg>
            </button>
            <div v-if="profileMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow ring-1 ring-black/5 overflow-hidden z-50">
              <div class="px-4 py-3 text-sm">
                <p class="font-medium text-gray-900 truncate">{{ user.nama || user.username }}</p>
                <p v-if="user.email" class="text-xs text-gray-500 truncate">{{ user.email }}</p>
              </div>
              <Link href="/profile" class="block px-4 py-2 text-sm hover:bg-gray-50">Profil</Link>
              <Link method="post" :href="route('logout')" as="button"
                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-50">
                Keluar
              </Link>
            </div>
          </div>
        </template>
      </div>
    </nav>

    <!-- Toast -->
    <transition name="fade">
      <div v-if="showToast"
           class="fixed top-20 right-4 bg-green-600 text-white text-sm px-4 py-2 rounded-md shadow">
        {{ toastMsg }}
      </div>
    </transition>
  </header>
</template>

<style scoped>
.fade-enter-active,.fade-leave-active{transition:opacity .25s}
.fade-enter-from,.fade-leave-to{opacity:0}
</style>