<script setup lang="ts">
import { getLinks } from '@/api'
import type { Link, LinkCategorie } from '@/types/api'
import { computed, onMounted, ref } from 'vue'
import headerImage from '@/assets/headers/header-build-hub.png'

const links = ref<Link[]>([])
const laden = ref(true)
const fout = ref<string | null>(null)

const heroStyle = {
  backgroundImage: `url(${headerImage})`,
  backgroundSize: 'cover',
  backgroundPosition: 'center',
}

const categorieLabels: Record<LinkCategorie, string> = {
  wallie: 'Hackerspace Drenthe',
  community: 'Community',
  competitie: 'Competitie & Regels',
  tools: 'Tools & Software',
  onderdelen: 'Onderdelen & Shops',
  documentatie: 'Documentatie & Gidsen',
}

const volgorde: LinkCategorie[] = ['wallie', 'community', 'competitie', 'tools', 'onderdelen', 'documentatie']

const perCategorie = computed(() => {
  const map = new Map<LinkCategorie, Link[]>()
  for (const cat of volgorde) {
    const items = links.value.filter((l) => l.categorie === cat)
    if (items.length) map.set(cat, items)
  }
  return map
})

onMounted(async () => {
  try {
    links.value = await getLinks()
  } catch {
    fout.value = 'Links konden niet worden geladen. Probeer het later opnieuw.'
  } finally {
    laden.value = false
  }
})
</script>

<template>
  <main id="main-content">
    <section class="relative overflow-hidden py-20 text-white" :style="heroStyle">
      <div class="absolute inset-0 bg-robo-dark/75" aria-hidden="true" />
      <div class="relative z-10 mx-auto max-w-3xl px-6 text-center">
        <h1 class="mb-4 text-4xl font-black md:text-5xl">Build Hub</h1>
        <p class="text-lg text-slate-300">
          Handige links, tools, leveranciers en community-bronnen voor robot-bouwers.
        </p>
      </div>
    </section>

    <section class="bg-white py-20" aria-labelledby="buildhub-title">
      <div class="mx-auto max-w-4xl px-6">
        <h2 id="buildhub-title" class="sr-only">Overzicht van links</h2>

        <!-- Laden -->
        <div v-if="laden" class="space-y-10" aria-busy="true" aria-label="Links laden">
          <div v-for="n in 3" :key="n" class="animate-pulse space-y-3" aria-hidden="true">
            <div class="h-5 w-1/4 rounded bg-slate-200" />
            <div class="h-4 w-full rounded bg-slate-100" />
            <div class="h-4 w-5/6 rounded bg-slate-100" />
          </div>
        </div>

        <!-- Fout -->
        <div v-else-if="fout" role="alert" class="rounded-xl border border-red-200 bg-red-50 p-6 text-red-700">
          {{ fout }}
        </div>

        <!-- Links per categorie -->
        <template v-else>
          <div
            v-for="[cat, items] in perCategorie"
            :key="cat"
            class="mb-12"
          >
            <h2 class="mb-4 border-b border-slate-200 pb-2 text-xl font-black text-robo-dark">
              {{ categorieLabels[cat] }}
            </h2>
            <ul class="space-y-4" role="list">
              <li v-for="link in items" :key="link.id" class="flex flex-col gap-1">
                <a
                  :href="link.url"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="font-semibold text-robo-orange hover:underline focus:outline-none focus:ring-2 focus:ring-robo-orange"
                >
                  {{ link.titel }}
                  <span class="sr-only">(opent in nieuw venster)</span>
                </a>
                <p v-if="link.beschrijving" class="text-sm text-slate-600">
                  {{ link.beschrijving }}
                </p>
              </li>
            </ul>
          </div>
        </template>
      </div>
    </section>
  </main>
</template>
