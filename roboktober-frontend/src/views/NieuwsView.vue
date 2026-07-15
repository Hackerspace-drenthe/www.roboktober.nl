<script setup lang="ts">
import { getPosts } from '@/api'
import type { Post } from '@/types/api'
import { applySeoMeta, removeJsonLd, upsertJsonLd } from '@/utils/seo'
import { onMounted, onUnmounted, ref } from 'vue'
import headerImage from '@/assets/headers/header-nieuws.png'

const posts = ref<Post[]>([])
const laden = ref(true)
const fout = ref<string | null>(null)

const heroStyle = {
  backgroundImage: `url(${headerImage})`,
  backgroundSize: 'cover',
  backgroundPosition: 'center',
}

function updateNieuwsStructuredData(): void {
  upsertJsonLd('news-list', {
    '@context': 'https://schema.org',
    '@type': 'CollectionPage',
    name: 'Nieuws — Roboktober',
    url: 'https://www.roboktober.nl/nieuws',
    isPartOf: {
      '@type': 'WebSite',
      name: 'Roboktober',
      url: 'https://www.roboktober.nl/',
    },
    mainEntity: {
      '@type': 'ItemList',
      itemListElement: posts.value.map((item, index) => ({
        '@type': 'ListItem',
        position: index + 1,
        url: `https://www.roboktober.nl/nieuws/${item.slug}`,
        name: item.titel,
      })),
    },
  })
}

function formatDatum(iso: string | null): string {
  if (!iso) return ''
  return new Date(iso).toLocaleDateString('nl-NL', { day: 'numeric', month: 'long', year: 'numeric' })
}

onMounted(async () => {
  applySeoMeta({
    title: 'Nieuws — Roboktober',
    description: 'Lees het laatste nieuws, updates en aankondigingen rond Roboktober en combat robotics in Drenthe.',
    canonicalPath: '/nieuws',
  })

  try {
    const response = await getPosts({ per_page: 20 })
    posts.value = response.data
    updateNieuwsStructuredData()
  } catch {
    fout.value = 'Nieuws kon niet worden geladen. Probeer het later opnieuw.'
  } finally {
    laden.value = false
  }
})

onUnmounted(() => {
  removeJsonLd('news-list')
})
</script>

<template>
  <main id="main-content">
    <section class="relative overflow-hidden py-20 text-white" :style="heroStyle">
      <div class="absolute inset-0 bg-robo-dark/75" aria-hidden="true" />
      <div class="relative z-10 mx-auto max-w-3xl px-6 text-center">
        <h1 class="mb-4 text-4xl font-black md:text-5xl">Nieuws</h1>
        <p class="text-lg text-slate-300">Updates over Roboktober, bouw-logs en aankondigingen.</p>
      </div>
    </section>

    <section class="bg-white py-20" aria-labelledby="nieuws-title">
      <div class="mx-auto max-w-4xl px-6">
        <h2 id="nieuws-title" class="sr-only">Nieuwsoverzicht</h2>

        <!-- Laden -->
        <div v-if="laden" class="space-y-6" aria-busy="true" aria-label="Artikelen laden">
          <div
            v-for="n in 3"
            :key="n"
            class="animate-pulse rounded-xl border border-slate-200 p-6"
            aria-hidden="true"
          >
            <div class="mb-3 h-5 w-1/3 rounded bg-slate-200" />
            <div class="mb-2 h-4 w-full rounded bg-slate-200" />
            <div class="h-4 w-3/4 rounded bg-slate-200" />
          </div>
        </div>

        <!-- Fout -->
        <div v-else-if="fout" role="alert" class="rounded-xl border border-red-200 bg-red-50 p-6 text-red-700">
          {{ fout }}
        </div>

        <!-- Leeg -->
        <p v-else-if="posts.length === 0" class="py-12 text-center text-slate-500">
          Nog geen artikelen gepubliceerd.
        </p>

        <!-- Artikelen -->
        <ul v-else class="space-y-6" role="list">
          <li v-for="post in posts" :key="post.id">
            <article class="rounded-xl border border-slate-200 p-6 shadow-sm transition hover:border-robo-orange/40 hover:shadow-md">
              <RouterLink :to="`/nieuws/${post.slug}`" class="group focus:outline-none">
                <h2 class="mb-1 text-xl font-bold text-robo-dark group-hover:text-robo-orange group-focus:underline">
                  {{ post.titel }}
                </h2>
              </RouterLink>
              <time
                v-if="post.published_at"
                :datetime="post.published_at"
                class="mb-3 block text-sm text-slate-400"
              >
                {{ formatDatum(post.published_at) }}
              </time>
              <p v-if="post.excerpt" class="text-slate-600">{{ post.excerpt }}</p>
              <RouterLink
                :to="`/nieuws/${post.slug}`"
                class="mt-4 inline-block text-sm font-medium text-robo-orange hover:underline focus:outline-none focus:ring-2 focus:ring-robo-orange"
                :aria-label="`Lees: ${post.titel}`"
              >
                Lees meer &rarr;
              </RouterLink>
            </article>
          </li>
        </ul>
      </div>
    </section>
  </main>
</template>
