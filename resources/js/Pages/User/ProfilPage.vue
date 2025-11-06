<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3'
import Logo from '*/dashboard/logo-ngundur.png'

const page = usePage()
const user = page.props.user ?? page.props.auth?.user ?? {}

const form = useForm({
    nama: user.nama || '',
    username: user.username || '',
    email: user.email || '',
    no_telp: user.no_telp || '',
    alamat: user.alamat || '',
})

function onPhoneInput(e) {
    form.no_telp = (e.target.value || '').replace(/\D+/g, '')
}

function save() {
    form.no_telp = (form.no_telp || '').replace(/\D+/g, '')
    form.put(route('profile.update'), { preserveScroll: true })
}

const logoutForm = useForm({})
function doLogout() {
    logoutForm.post(route('logout'), {
        preserveState: false,
        preserveScroll: false,
        onSuccess: () =>{
            window.location.href = route('dashboard')
        }
    })
}
</script>

<template>
    <div class="flex flex-col min-h-screen bg-slate-50">
        <header class="flex items-center justify-between h-16 px-4 shadow-sm bg-white/80 backdrop-blur">
        <Link :href="route('dashboard')" class="flex items-center gap-2">
            <img :src="Logo" alt="NGUNDUR" class="h-14" />
        </Link>
        <Link :href="route('dashboard')" class="px-4 py-2 text-sm text-white bg-green-600 rounded-lg hover:bg-green-700">
            Kembali ke Home
        </Link>
        </header>

        <main class="flex-1 w-full max-w-3xl p-6 mx-auto">
        <h1 class="mb-6 text-2xl font-semibold text-center">Profil Saya</h1>

        <div v-if="$page.props.flash?.success" class="px-4 py-2 mb-4 text-green-800 bg-green-100 rounded">
            {{$page.props.flash.success}}
        </div>

        <div class="p-6 border border-green-200 shadow bg-green-50/70 rounded-xl">
            <div class="flex flex-col items-center mb-6">
            <div class="flex items-center justify-center w-24 h-24 text-green-700 bg-green-200 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-12 h-12">
                <path fill-rule="evenodd" d="M12 2a5 5 0 100 10 5 5 0 000-10zm-7 17a7 7 0 1114 0v1a1 1 0 01-1 1H6a1 1 0 01-1-1v-1z" clip-rule="evenodd" />
                </svg>
            </div>
            <p class="mt-2 font-medium text-gray-700">{{ form.username }}</p>
            </div>

            <form @submit.prevent="save" class="grid grid-cols-1 gap-4">
            <div>
                <label class="block mb-1 text-sm">Username</label>
                <input v-model="form.username" class="w-full px-3 py-2 border border-green-200 rounded-md bg-white/80" />
                <p v-if="form.errors.username" class="mt-1 text-sm text-red-600">{{ form.errors.username }}</p>
            </div>
            <div>
                <label class="block mb-1 text-sm">Nama</label>
                <input v-model="form.nama" class="w-full px-3 py-2 border border-green-200 rounded-md bg-white/80" placeholder="Opsional" />
                <p v-if="form.errors.nama" class="mt-1 text-sm text-red-600">{{ form.errors.nama }}</p>
            </div>
            <div>
                <label class="block mb-1 text-sm">Email</label>
                <input v-model="form.email" type="email" class="w-full px-3 py-2 border border-green-200 rounded-md bg-white/80" />
                <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
            </div>
            <div>
                <label class="block mb-1 text-sm">Nomor Telepon</label>
                <input v-model="form.no_telp" inputmode="numeric" pattern="[0-9]*" maxlength="20"
                    @input="onPhoneInput"
                    class="w-full px-3 py-2 border border-green-200 rounded-md bg-white/80" />
                <p v-if="form.errors.no_telp" class="mt-1 text-sm text-red-600">{{ form.errors.no_telp }}</p>
            </div>
            <div>
                <label class="block mb-1 text-sm">Alamat</label>
                <input v-model="form.alamat" class="w-full px-3 py-2 border border-green-200 rounded-md bg-white/80" />
                <p v-if="form.errors.alamat" class="mt-1 text-sm text-red-600">{{ form.errors.alamat }}</p>
            </div>

            <div class="flex gap-3 mt-2">
                <button type="submit" :disabled="form.processing" class="px-5 py-2 text-white bg-green-600 rounded-lg hover:bg-green-700">
                {{ form.processing ? 'Menyimpan...' : 'Selesai' }}
                </button>
                <button type="button" :disabled="logoutForm.processing" @click="doLogout"
                        class="px-5 py-2 text-white bg-red-500 rounded-lg hover:bg-red-600">
                Logout
                </button>
            </div>
            </form>
        </div>
        </main>
    </div>
</template>