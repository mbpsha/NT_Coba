<script setup>
import { Link, useForm, usePage, router } from '@inertiajs/vue3'
import Logo from '*/dashboard/logo-ngundur.png'

const page = usePage()
const checkoutIntent = page.props.checkoutIntent
const needAddress = page.props.needAddress

const user = page.props.user ?? page.props.auth?.user ?? {}

const form = useForm({
  nama: user.nama || '',
  username: user.username || '',
  email: user.email || '',
  no_telp: user.no_telp || '',
  alamat: user.alamat || '',
  // hidden jika dari checkout
  checkout_return: checkoutIntent ? 1 : 0,
  checkout_product_id: checkoutIntent?.id_produk || null,
  checkout_qty: checkoutIntent?.qty || 1,
})

function onPhoneInput(e){ form.no_telp = (e.target.value || '').replace(/\D+/g,'') }

function save(){
  form.no_telp = (form.no_telp || '').replace(/\D+/g,'')
  form.put(route('profile.update'), { preserveScroll: true })
}

function backToCheckout(){
  if (!form.alamat) return alert('Isi alamat terlebih dahulu.')
  // langsung kunjungi checkout (kalau belum menyimpan perubahan)
  const pid = checkoutIntent.id_produk
  const qty = checkoutIntent.qty || 1
  router.visit(`/checkout/${pid}?qty=${qty}`)
}

const logoutForm = useForm({})
function doLogout(){ logoutForm.post(route('logout'), { replace:true }) }
</script>

<template>
    <div class="min-h-screen flex flex-col bg-slate-50">
        <header class="h-16 flex items-center justify-between px-4 bg-white/80 backdrop-blur shadow-sm">
        <Link :href="route('dashboard')" class="flex items-center gap-2">
            <img :src="Logo" alt="NGUNDUR" class="h-14" />
        </Link>
        <Link :href="route('dashboard')" class="px-4 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white text-sm">
            Kembali ke Home
        </Link>
        </header>

        <main class="flex-1 max-w-3xl mx-auto w-full p-6">
        <h1 class="text-2xl font-semibold text-center mb-6">
            Profil Saya
        </h1>

        <!-- Pesan khusus jika dari checkout dan belum ada alamat -->
        <div v-if="needAddress && checkoutIntent" class="mb-4 rounded border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700">
            *Isikan alamat anda sebelum melakukan Checkout Produk
        </div>

        <div v-if="$page.props.flash?.success" class="mb-4 rounded bg-green-100 text-green-800 px-4 py-2">
            {{ $page.props.flash.success }}
        </div>

        <div class="bg-green-50/70 rounded-xl border border-green-200 p-6 shadow">
            <form @submit.prevent="save" class="grid grid-cols-1 gap-4">
            <div>
                <label class="block text-sm mb-1">Username</label>
                <input v-model="form.username" class="w-full rounded-md bg-white/80 border border-green-200 px-3 py-2" />
                <p v-if="form.errors.username" class="text-sm text-red-600 mt-1">{{ form.errors.username }}</p>
            </div>
            <div>
                <label class="block text-sm mb-1">Nama</label>
                <input v-model="form.nama" class="w-full rounded-md bg-white/80 border border-green-200 px-3 py-2" placeholder="Opsional" />
                <p v-if="form.errors.nama" class="text-sm text-red-600 mt-1">{{ form.errors.nama }}</p>
            </div>
            <div>
                <label class="block text-sm mb-1">Email</label>
                <input v-model="form.email" type="email" class="w-full rounded-md bg-white/80 border border-green-200 px-3 py-2" />
                <p v-if="form.errors.email" class="text-sm text-red-600 mt-1">{{ form.errors.email }}</p>
            </div>
            <div>
                <label class="block text-sm mb-1">Nomor Telepon</label>
                <input v-model="form.no_telp" inputmode="numeric" pattern="[0-9]*" maxlength="20"
                    @input="onPhoneInput"
                    class="w-full rounded-md bg-white/80 border border-green-200 px-3 py-2" />
                <p v-if="form.errors.no_telp" class="text-sm text-red-600 mt-1">{{ form.errors.no_telp }}</p>
            </div>
            <div>
                <label class="block text-sm mb-1">Alamat</label>
                <input v-model="form.alamat" class="w-full rounded-md bg-white/80 border border-green-200 px-3 py-2" />
                <p v-if="form.errors.alamat" class="text-sm text-red-600 mt-1">{{ form.errors.alamat }}</p>
            </div>

            <!-- Tombol -->
            <div class="mt-2 flex flex-wrap gap-3">
                <button type="submit" :disabled="form.processing"
                        class="px-5 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white">
                {{ form.processing ? 'Menyimpan...' : 'Selesai' }}
                </button>

                <button type="button" :disabled="logoutForm.processing" @click="doLogout"
                        class="px-5 py-2 rounded-lg bg-red-500 hover:bg-red-600 text-white">
                Logout
                </button>

                <!-- Kembali ke Checkout -->
                <button
                v-if="checkoutIntent"
                type="button"
                @click="backToCheckout"
                class="px-5 py-2 rounded-lg bg-green-100 text-green-700 hover:bg-green-200"
                :disabled="!form.alamat">
                Kembali ke Checkout
                </button>
            </div>
            </form>
        </div>
        </main>
    </div>
</template>