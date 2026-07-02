<script setup lang="ts">
import { getPage } from '@/api'
import type { Page } from '@/types/api'
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import headerImage from '@/assets/headers/header-pagina.png'

const route = useRoute()
const router = useRouter()
const pagina = ref<Page | null>(null)
const laden = ref(true)

const heroStyle = {
  backgroundImage: `url(${headerImage})`,
  backgroundSize: 'cover',
  backgroundPosition: 'center',
}

onMounted(async () => {
  const slug = String(route.params.slug)
  try {
    pagina.value = await getPage(slug)
  } catch {
    router.replace('/niet-gevonden')
  } finally {
    laden.value = false
  }
})
</script>

<template>
  <main id="main-content">
    <!-- Laden -->
    <div v-if="laden" class="mx-auto max-w-3xl px-6 py-24" aria-busy="true" aria-label="Pagina laden">
      <div class="animate-pulse space-y-4">
        <div class="h-8 w-2/3 rounded bg-slate-200" />
        <div class="h-4 w-full rounded bg-slate-200" />
        <div class="h-4 w-5/6 rounded bg-slate-200" />
      </div>
    </div>

    <template v-else-if="pagina">
      <section class="relative overflow-hidden py-20 text-white" :style="heroStyle">
        <div class="absolute inset-0 bg-robo-dark/75" aria-hidden="true" />
        <div class="relative z-10 mx-auto max-w-3xl px-6">
          <h1 class="text-4xl font-black md:text-5xl">{{ pagina.titel }}</h1>
        </div>
      </section>

      <article class="bg-white py-16">
        <div class="mx-auto max-w-3xl px-6">
          <!-- eslint-disable-next-line vue/no-v-html -->
          <div
            v-if="pagina.content_format === 'html'"
            class="prose prose-slate max-w-none prose-headings:font-black prose-a:text-robo-orange"
            v-html="pagina.content"
          />
          <pre v-else class="whitespace-pre-wrap font-sans text-slate-700">{{ pagina.content }}</pre>
        </div>
      </article>
    </template>
  </main>
</template>
