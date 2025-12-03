<script setup>
import Header from '@/Components/User/Header.vue'
import Footer from '@/Components/User/Footer.vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  news: { type: Object, required: true } // pagination dari controller
})

const cover = (img) => img ? `/storage/${img}` : '/assets/dashboard/berita-1.png'
</script>

<template>
  <div class="font-inter text-gray-900 min-h-screen flex flex-col bg-white">
    <Header />
    <Head title="Berita" />

    <!-- Background moved to main to ensure it touches the footer -->
    <main
      class="flex-1 mt-16 bg-no-repeat bg-cover bg-center pb-0"
      style="background-image:
        linear-gradient(to bottom, rgba(255,255,255,0.45), rgba(236,253,245,0.08)),
        url('/assets/dashboard/bg-berita.jpg');"
    >
      <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 pt-6 md:pt-8">
        <h1 class="text-4xl md:text-5xl font-extrabold tracking-wide text-[#2D6A4F] mb-6 md:mb-8">
          BERITA PERTANIAN
        </h1>

        <div class="space-y-6">
          <article v-for="a in news.data" :key="a.id"
                  class="relative overflow-hidden rounded-2xl bg-green-50/80 backdrop-blur-md shadow-lg ring-1 ring-green-200">
            <Link class="w-full text-left block" :href="route('berita.show', a.id)">
              <div class="p-4 md:p-6">
                <div class="flex flex-col md:flex-row gap-4 md:gap-6 items-center">
                  <div class="shrink-0 rounded-lg overflow-hidden shadow-sm flex-none">
                    <img :src="cover(a.image)" :alt="a.title" class="w-40 h-28 md:w-48 md:h-32 object-cover rounded-lg" />
                  </div>
                  <div class="flex-1 min-w-0">
                    <h2 class="text-lg md:text-xl font-semibold text-[#155e3b]">{{ a.title }}</h2>
                    <p class="mt-2 text-sm md:text-base text-gray-700">{{ a.excerpt }}</p>
                    <div class="mt-3 md:mt-2 text-right">
                      <span class="inline-block text-sm font-medium text-[#196f3d] hover:underline">Baca Selengkapnya</span>
                    </div>
                  </div>
                </div>
              </div>
            </Link>
          </article>

          <!-- Empty state: keep some height so background stays tall -->
          <div v-if="news.data?.length === 0"
              class="p-8 text-center text-gray-500 bg-white/80 rounded-xl min-h-[20vh] flex items-center justify-center">
            Belum ada berita yang dipublikasikan.
          </div>
        </div>

        <div v-if="news.links && news.links.length > 3" class="mt-8 flex items-center justify-between">
          <div class="text-sm text-gray-600">Menampilkan {{ news.from }}–{{ news.to }} dari {{ news.total }}</div>
          <div class="flex items-center gap-2">
            <Link v-for="link in news.links" :key="link.label" :href="link.url || '#'"
                  class="px-3 py-1 rounded border"
                  :class="link.active ? 'bg-green-700 text-white border-green-700' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                  v-html="link.label" />
          </div>
        </div>
      </div>
    </main>

    <Footer />
  </div>
</template>