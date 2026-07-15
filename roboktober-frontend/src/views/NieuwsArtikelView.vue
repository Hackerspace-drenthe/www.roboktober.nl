<script setup lang="ts">
import { getPost } from '@/api'
import type { Post } from '@/types/api'
import { applySeoMeta, removeJsonLd, upsertJsonLd } from '@/utils/seo'
import { onMounted, onUnmounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import headerImage from '@/assets/headers/header-nieuws-artikel.png'

const route = useRoute()
const router = useRouter()
const post = ref<Post | null>(null)
const laden = ref(true)
const fout = ref<string | null>(null)

const heroStyle = {
  backgroundImage: `url(${headerImage})`,
  backgroundSize: 'cover',
  backgroundPosition: 'center',
}

function formatDatum(iso: string | null): string {
  if (!iso) return ''
  return new Date(iso).toLocaleDateString('nl-NL', { day: 'numeric', month: 'long', year: 'numeric' })
}

function stripHtmlToText(content: string): string {
  return content.replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim()
}

function excerptFromContent(item: Post): string {
  const base = item.excerpt?.trim() || stripHtmlToText(item.content)
  return base.length <= 170 ? base : `${base.slice(0, 167)}...`
}

function updateArticleSeo(item: Post): void {
  const canonicalPath = `/nieuws/${item.slug}`
  const description = excerptFromContent(item)

  applySeoMeta({
    title: `${item.titel} — Roboktober`,
    description,
    canonicalPath,
    ogType: 'article',
    image: item.featured?.url ?? null,
  })

  upsertJsonLd('news-article', {
    '@context': 'https://schema.org',
    '@type': 'Article',
    headline: item.titel,
    description,
    datePublished: item.published_at,
    dateModified: item.published_at,
    author: {
      '@type': 'Organization',
      name: 'Roboktober',
    },
    publisher: {
      '@type': 'Organization',
      name: 'Hackerspace Drenthe',
    },
    image: item.featured?.url ? [item.featured.url] : undefined,
    mainEntityOfPage: {
      '@type': 'WebPage',
      '@id': `https://www.roboktober.nl${canonicalPath}`,
    },
  })
}

onMounted(async () => {
  const slug = String(route.params.slug)
  try {
    const loadedPost = await getPost(slug)
    post.value = loadedPost
    updateArticleSeo(loadedPost)
  } catch {
    router.replace('/niet-gevonden')
  } finally {
    laden.value = false
  }
})

onUnmounted(() => {
  removeJsonLd('news-article')
})
</script>

<template>
  <main id="main-content">
    <!-- Laden -->
    <div v-if="laden" class="mx-auto max-w-3xl px-6 py-24" aria-busy="true" aria-label="Artikel laden">
      <div class="animate-pulse space-y-4">
        <div class="h-8 w-2/3 rounded bg-slate-200" />
        <div class="h-4 w-1/4 rounded bg-slate-200" />
        <div class="h-4 w-full rounded bg-slate-200" />
        <div class="h-4 w-full rounded bg-slate-200" />
        <div class="h-4 w-4/5 rounded bg-slate-200" />
      </div>
    </div>

    <template v-else-if="post">
      <!-- Header -->
      <section class="relative overflow-hidden py-20 text-white" :style="heroStyle">
        <div class="absolute inset-0 bg-robo-dark/75" aria-hidden="true" />
        <div class="relative z-10 mx-auto max-w-3xl px-6">
          <RouterLink to="/nieuws" class="mb-6 inline-block text-sm text-slate-400 hover:text-white">
            &larr; Alle artikelen
          </RouterLink>
          <h1 class="mb-4 text-3xl font-black leading-tight md:text-4xl">{{ post.titel }}</h1>
          <time
            v-if="post.published_at"
            :datetime="post.published_at"
            class="text-sm text-slate-400"
          >
            {{ formatDatum(post.published_at) }}
          </time>
        </div>
      </section>

      <!-- Inhoud -->
      <article class="bg-white py-16">
        <div class="mx-auto max-w-3xl px-6">
          <!-- HTML content -->
          <!-- eslint-disable-next-line vue/no-v-html -->
          <div
            v-if="post.content_format === 'html'"
            class="prose prose-slate max-w-none prose-headings:font-black prose-a:text-robo-orange prose-a:no-underline hover:prose-a:underline"
            v-html="post.content"
          />
          <!-- Markdown fallback (plain tekst weergave) -->
          <pre v-else class="whitespace-pre-wrap font-sans text-slate-700">{{ post.content }}</pre>
        </div>
      </article>
    </template>

    <!-- Fout -->
    <div v-else-if="fout" class="mx-auto max-w-3xl px-6 py-24">
      <div role="alert" class="rounded-xl border border-red-200 bg-red-50 p-6 text-red-700">{{ fout }}</div>
    </div>
  </main>
</template>
