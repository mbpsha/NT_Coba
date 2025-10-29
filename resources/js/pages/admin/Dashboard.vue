<template>
  <!-- Admin Layout: Sidebar + Main -->
  <div class="min-h-screen bg-gray-100 flex">
    <!-- Sidebar -->
    <AdminSidebar />

    <!-- Main content -->
    <main class="flex-1 flex flex-col min-w-0">
      <AdminHeader />

      <!-- Dashboard body -->
      <section class="flex-1 p-4 sm:p-6 lg:p-8 bg-green-100/70">
        <!-- Stat cards (one line, no wrap, like the reference image) -->
        <div class="flex flex-nowrap gap-4 overflow-x-auto pb-2">
          <!-- Total Products -->
          <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-green-500 min-w-[260px] sm:min-w-[280px]">
            <div class="flex items-start justify-between gap-4">
              <div>
                <div class="text-[11px] font-semibold uppercase tracking-wider text-gray-500">Total Products</div>
                <div class="text-3xl font-extrabold text-gray-900 mt-1">{{ metrics.totalProducts }}</div>
              </div>
              <div class="shrink-0 h-10 w-10 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                <SvgCart class="w-5 h-5" />
              </div>
            </div>
          </div>

          <!-- Total Users -->
          <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-emerald-500 min-w-[260px] sm:min-w-[280px]">
            <div class="flex items-start justify-between gap-4">
              <div>
                <div class="text-[11px] font-semibold uppercase tracking-wider text-gray-500">Total Users</div>
                <div class="text-3xl font-extrabold text-gray-900 mt-1">{{ metrics.totalUsers }}</div>
              </div>
              <div class="shrink-0 h-10 w-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                <SvgUsers class="w-5 h-5" />
              </div>
            </div>
          </div>

          <!-- Total Orders -->
          <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-purple-500 min-w-[260px] sm:min-w-[280px]">
            <div class="flex items-start justify-between gap-4">
              <div>
                <div class="text-[11px] font-semibold uppercase tracking-wider text-gray-500">Total Orders</div>
                <div class="text-3xl font-extrabold text-gray-900 mt-1">{{ metrics.totalOrders }}</div>
              </div>
              <div class="shrink-0 h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-600">
                <SvgReceipt class="w-5 h-5" />
              </div>
            </div>
          </div>

          <!-- Pending Verification -->
          <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-yellow-500 min-w-[260px] sm:min-w-[280px]">
            <div class="flex items-start justify-between gap-4">
              <div>
                <div class="text-[11px] font-semibold uppercase tracking-wider text-gray-500">Pending Verification</div>
                <div class="text-3xl font-extrabold text-gray-900 mt-1">{{ metrics.pendingVerification }}</div>
              </div>
              <div class="shrink-0 h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600">
                <SvgAlert class="w-5 h-5" />
              </div>
            </div>
          </div>
        </div>

        <!-- Tables row -->
        <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Total Produk table -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between p-4 border-b">
              <h3 class="font-semibold text-gray-900">Total Produk</h3>
              <button class="px-3 py-1.5 text-xs rounded-md bg-green-100 text-green-700 hover:bg-green-200">Selengkapnya</button>
            </div>
            <div class="overflow-x-auto">
              <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600">
                  <tr>
                    <th class="text-left px-4 py-2 font-medium">Nama</th>
                    <th class="text-left px-4 py-2 font-medium">Jumlah</th>
                    <th class="text-left px-4 py-2 font-medium">Harga</th>
                    <th class="text-left px-4 py-2 font-medium">Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="p in products" :key="p.name" class="border-t">
                    <td class="px-4 py-2">{{ p.name }}</td>
                    <td class="px-4 py-2">{{ p.qty }}</td>
                    <td class="px-4 py-2">{{ p.price }}</td>
                    <td class="px-4 py-2">
                      <span :class="badgeClass(p.status)" class="px-2 py-1 rounded text-xs">{{ p.status }}</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Daftar Pengguna table -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between p-4 border-b">
              <h3 class="font-semibold text-gray-900">Daftar Pengguna</h3>
              <button class="px-3 py-1.5 text-xs rounded-md bg-green-100 text-green-700 hover:bg-green-200">Selengkapnya</button>
            </div>
            <div class="overflow-x-auto">
              <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600">
                  <tr>
                    <th class="text-left px-4 py-2 font-medium">Nama</th>
                    <th class="text-left px-4 py-2 font-medium">Email</th>
                    <th class="text-left px-4 py-2 font-medium">Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="u in users" :key="u.email" class="border-t">
                    <td class="px-4 py-2">{{ u.name }}</td>
                    <td class="px-4 py-2">{{ u.email }}</td>
                    <td class="px-4 py-2">
                      <span :class="u.role === 'Admin' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700'" class="px-2 py-1 rounded text-xs">{{ u.role }}</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Chart placeholder -->
        <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-100">
          <div class="p-4 border-b">
            <h3 class="font-semibold text-gray-900">Penjualan Bulanan</h3>
          </div>
          <div class="h-64 flex items-center justify-center text-gray-400 text-sm">
            <!-- Replace with chart later -->
            Grafik penjualan akan ditampilkan di sini
          </div>
        </div>
      </section>
    </main>
  </div>
</template>

<script setup>
// Admin Dashboard dynamic UI: fetch metrics/products/users and auto-refresh on auth/login.
import { ref, reactive, onMounted, onBeforeUnmount } from 'vue'
import axios from 'axios'
import AdminSidebar from '../../components/AdminSidebar.vue'
import AdminHeader from '../../components/AdminHeader.vue'

// Reactive state
const loading = ref(false)
const error = ref(null)
const metrics = reactive({ totalProducts: 0, totalUsers: 0, totalOrders: 0, pendingVerification: 0 })
const products = ref([])
const users = ref([])

// Sidebar moved to shared component

// Fetch dashboard data from backend; graceful fallback if API missing
async function fetchDashboard() {
  loading.value = true
  error.value = null
  try {
    const { data } = await axios.get('/api/admin/dashboard')
    if (data?.metrics) {
      metrics.totalProducts = Number(data.metrics.totalProducts || 0)
      metrics.totalUsers = Number(data.metrics.totalUsers || 0)
      metrics.totalOrders = Number(data.metrics.totalOrders || 0)
      metrics.pendingVerification = Number(data.metrics.pendingVerification || 0)
    }
    products.value = Array.isArray(data?.products) ? data.products : []
    users.value = Array.isArray(data?.users) ? data.users : []
  } catch (e) {
    // Fallback demo data using localStorage so cards still show something in dev
    try {
      const savedRaw = localStorage.getItem('auth:savedUser')
      const saved = savedRaw ? JSON.parse(savedRaw) : null
      const hasUser = !!saved
      metrics.totalUsers = hasUser ? 1 : 0
      metrics.totalProducts = 0
      metrics.totalOrders = 0
      metrics.pendingVerification = 0
      users.value = hasUser ? [{ id: 1, name: saved.username || 'Admin', email: saved.email || 'admin@admin', role: (saved.roles||[]).includes('admin') ? 'Admin' : 'Pengguna' }] : []
      products.value = []
    } catch {}
    error.value = e
  } finally {
    loading.value = false
  }
}

let pollId
onMounted(() => {
  fetchDashboard()
  // Poll every 15s for changes (orders, new users)
  pollId = setInterval(fetchDashboard, 15000)
  // Refresh when auth lifecycle changes
  window.addEventListener('auth:login', fetchDashboard)
  window.addEventListener('auth:register', fetchDashboard)
  window.addEventListener('auth:logout', fetchDashboard)
  // Optional custom events (e.g., broadcast from order creation)
  window.addEventListener('order:created', fetchDashboard)
})

onBeforeUnmount(() => {
  if (pollId) clearInterval(pollId)
  window.removeEventListener('auth:login', fetchDashboard)
  window.removeEventListener('auth:register', fetchDashboard)
  window.removeEventListener('auth:logout', fetchDashboard)
  window.removeEventListener('order:created', fetchDashboard)
})


function badgeClass(status) {
  return status === 'Pending' ? 'bg-orange-100 text-orange-700' : 'bg-green-100 text-green-700'
}

// Inline SVG icon components
// Keep only icons used in cards (users icon reused here)
const SvgUsers = { template: `<svg v-bind="$attrs" xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' aria-hidden='true'><path d='M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2'/><circle cx='9' cy='7' r='4'/><path d='M23 21v-2a4 4 0 0 0-3-3.87'/><path d='M16 3.13A4 4 0 0 1 20 7'/></svg>` }
const SvgReceipt = { template: `<svg v-bind="$attrs" xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' aria-hidden='true'><path d='M7 3h10a2 2 0 0 1 2 2v15l-4-2-4 2-4-2-4 2V5a2 2 0 0 1 2-2z'/><path d='M8 7h8M8 11h8M8 15h5'/></svg>` }

// Extra icons for stat cards
const SvgCart = { template: `<svg v-bind="$attrs" xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' aria-hidden='true'><path d='M3 3h2l.4 2M7 13h10l3-7H6.4M7 13L5.4 5M7 13l-2 9M18 13l2 9'/><circle cx='10' cy='21' r='1'/><circle cx='18' cy='22' r='1'/></svg>` }
const SvgAlert = { template: `<svg v-bind="$attrs" xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' aria-hidden='true'><path d='M12 9v4m0 4h.01'/><path d='M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z'/></svg>` }
</script>

<style scoped>
/* Simple scrollbar adjustments for sidebar if content grows */
aside { scrollbar-width: thin; }
</style>
