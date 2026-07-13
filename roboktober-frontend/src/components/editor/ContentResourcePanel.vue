<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { useRichMediaLibrary } from '@/composables/useRichMediaLibrary'

const props = withDefaults(defineProps<{
  contentFormat: 'html' | 'markdown'
  disabled?: boolean
}>(), {
  disabled: false,
})

const emit = defineEmits<{
  (e: 'insert', snippet: string): void
}>()

const {
  items,
  loading,
  uploading,
  errorMessage,
  totalItems,
  load,
  loadNextPage,
  hasMorePages,
  upload,
  snippetFor,
} = useRichMediaLibrary()

const search = ref('')
const actiefTabblad = ref<'library' | 'upload' | 'widgets'>('library')
const bestand = ref<File | null>(null)
const naam = ref('')
const altTekst = ref('')
const onderschrift = ref('')
const zichtbaarAantal = ref(12)
let searchTimer: ReturnType<typeof setTimeout> | null = null

const zichtbareItems = computed(() => items.value.slice(0, zichtbaarAantal.value))

const widgetSnippets = computed(() => {
  if (props.contentFormat === 'html') {
    return [
      {
        key: 'callout',
        titel: 'Callout blok',
        snippet: '<aside class="callout"><h3>Belangrijk</h3><p>Schrijf hier je kernboodschap.</p></aside>',
      },
      {
        key: 'button',
        titel: 'CTA knop',
        snippet: '<p><a class="button" href="https://">Bekijk meer</a></p>',
      },
      {
        key: 'video',
        titel: 'Video embed',
        snippet: '<figure><iframe src="https://www.youtube.com/embed/VIDEO_ID" title="Video" loading="lazy" allowfullscreen></iframe></figure>',
      },
      {
        key: 'gallery',
        titel: 'Galerij wrapper',
        snippet: '<section class="media-grid">\n  <!-- Voeg hieronder resource snippets in -->\n</section>',
      },
    ]
  }

  return [
    {
      key: 'callout',
      titel: 'Callout blok',
      snippet: '> **Belangrijk**\n> Schrijf hier je kernboodschap.',
    },
    {
      key: 'button',
      titel: 'CTA knop',
      snippet: '[Bekijk meer](https://)',
    },
    {
      key: 'video',
      titel: 'Video embed',
      snippet: '[Video bekijken](https://www.youtube.com/watch?v=VIDEO_ID)',
    },
    {
      key: 'gallery',
      titel: 'Galerij wrapper',
      snippet: '## Galerij\n\n<!-- Voeg hieronder resource snippets in -->',
    },
  ]
})

function onBestandChange(event: Event): void {
  const target = event.target as HTMLInputElement
  bestand.value = target.files?.[0] ?? null
}

async function zoekMedia(): Promise<void> {
  zichtbaarAantal.value = 12
  await load(search.value)
}

async function laadMeerMedia(): Promise<void> {
  if (zichtbaarAantal.value < items.value.length) {
    zichtbaarAantal.value += 12
    return
  }

  if (hasMorePages()) {
    await loadNextPage()
    zichtbaarAantal.value += 12
  }
}

async function uploadBestand(): Promise<void> {
  if (!bestand.value) {
    return
  }

  const item = await upload({
    bestand: bestand.value,
    naam: naam.value || undefined,
    collectie: 'default',
    alt_tekst: altTekst.value || undefined,
    onderschrift: onderschrift.value || undefined,
  })

  if (!item) {
    return
  }

  emit('insert', snippetFor(item, props.contentFormat))

  bestand.value = null
  naam.value = ''
  altTekst.value = ''
  onderschrift.value = ''
}

function insert(itemId: number): void {
  const item = items.value.find((candidate) => candidate.id === itemId)

  if (!item) {
    return
  }

  emit('insert', snippetFor(item, props.contentFormat))
}

function insertWidgetSnippet(snippet: string): void {
  emit('insert', snippet)
}

watch(search, () => {
  if (searchTimer) {
    clearTimeout(searchTimer)
  }

  searchTimer = setTimeout(() => {
    void zoekMedia()
  }, 250)
})

onBeforeUnmount(() => {
  if (searchTimer) {
    clearTimeout(searchTimer)
  }
})

onMounted(async () => {
  await load()
})
</script>

<template>
  <section class="rounded-xl border border-white/15 bg-black/20 p-4">
    <div class="mb-4">
      <h3 class="text-lg font-bold">Resources in content invoegen</h3>
      <p class="text-sm text-slate-300">
        Kies snel uit upload, bibliotheek of widgets en voeg direct een {{ props.contentFormat.toUpperCase() }} snippet toe aan je content.
      </p>
    </div>

    <p v-if="errorMessage" class="mb-3 rounded border border-red-500/30 bg-red-500/10 px-3 py-2 text-sm text-red-300">
      {{ errorMessage }}
    </p>

    <div class="mb-4 flex flex-wrap gap-2">
      <button
        type="button"
        class="rounded border px-3 py-1.5 text-sm font-semibold"
        :class="actiefTabblad === 'library' ? 'border-robo-orange bg-robo-orange/15 text-white' : 'border-white/25 text-slate-200 hover:bg-white/10'"
        :disabled="disabled"
        @click="actiefTabblad = 'library'"
      >
        Bibliotheek
      </button>
      <button
        type="button"
        class="rounded border px-3 py-1.5 text-sm font-semibold"
        :class="actiefTabblad === 'upload' ? 'border-robo-orange bg-robo-orange/15 text-white' : 'border-white/25 text-slate-200 hover:bg-white/10'"
        :disabled="disabled"
        @click="actiefTabblad = 'upload'"
      >
        Upload
      </button>
      <button
        type="button"
        class="rounded border px-3 py-1.5 text-sm font-semibold"
        :class="actiefTabblad === 'widgets' ? 'border-robo-orange bg-robo-orange/15 text-white' : 'border-white/25 text-slate-200 hover:bg-white/10'"
        :disabled="disabled"
        @click="actiefTabblad = 'widgets'"
      >
        Widgets
      </button>
    </div>

    <div v-if="actiefTabblad === 'upload'" class="grid gap-3 rounded-lg border border-white/10 bg-white/5 p-3 md:grid-cols-2">
      <div>
        <label class="mb-1 block text-sm font-semibold">Bestand</label>
        <input
          type="file"
          accept=".jpg,.jpeg,.png,.webp,.gif,.mp4,.webm,.mov,.stl,.obj,.3mf,.pdf,.zip,.txt,.md"
          class="w-full rounded border border-white/20 bg-white/5 px-3 py-2 text-sm"
          :disabled="disabled || uploading"
          @change="onBestandChange"
        />
      </div>

      <div>
        <label class="mb-1 block text-sm font-semibold">Naam (optioneel)</label>
        <input v-model="naam" type="text" class="w-full rounded border border-white/20 bg-white/5 px-3 py-2 text-sm" :disabled="disabled || uploading" />
      </div>

      <div>
        <label class="mb-1 block text-sm font-semibold">Alt tekst (optioneel)</label>
        <input v-model="altTekst" type="text" class="w-full rounded border border-white/20 bg-white/5 px-3 py-2 text-sm" :disabled="disabled || uploading" />
      </div>

      <div>
        <label class="mb-1 block text-sm font-semibold">Onderschrift (optioneel)</label>
        <input v-model="onderschrift" type="text" class="w-full rounded border border-white/20 bg-white/5 px-3 py-2 text-sm" :disabled="disabled || uploading" />
      </div>

      <div class="md:col-span-2">
        <button
          type="button"
          class="rounded-lg bg-robo-orange px-4 py-2 font-semibold text-white hover:bg-robo-orange-dark disabled:opacity-60"
          :disabled="disabled || uploading || !bestand"
          @click="uploadBestand"
        >
          {{ uploading ? 'Uploaden...' : 'Upload en voeg in' }}
        </button>
      </div>
    </div>

    <div v-else-if="actiefTabblad === 'widgets'" class="rounded-lg border border-white/10 bg-white/5 p-3">
      <p class="mb-3 text-sm text-slate-300">Snelle bouwblokken voor consistente layout in de editor.</p>

      <ul class="grid gap-2 md:grid-cols-2">
        <li v-for="widget in widgetSnippets" :key="widget.key" class="rounded border border-white/10 bg-black/20 px-3 py-2">
          <div class="mb-2 flex items-center justify-between gap-2">
            <p class="text-sm font-semibold">{{ widget.titel }}</p>
            <button
              type="button"
              class="rounded border border-white/30 px-2 py-1 text-xs font-semibold hover:bg-white/10"
              :disabled="disabled"
              @click="insertWidgetSnippet(widget.snippet)"
            >
              Invoegen
            </button>
          </div>
          <pre class="max-h-24 overflow-auto rounded bg-black/30 p-2 text-xs text-slate-300">{{ widget.snippet }}</pre>
        </li>
      </ul>
    </div>

    <div v-else class="rounded-lg border border-white/10 bg-white/5 p-3">
      <div class="mb-3 flex flex-wrap items-center gap-2">
        <input
          v-model="search"
          type="search"
          placeholder="Zoek resources op naam of mime"
          class="min-w-52 flex-1 rounded border border-white/20 bg-white/5 px-3 py-2 text-sm"
          :disabled="disabled || loading"
        />
        <button
          type="button"
          class="rounded border border-white/30 px-3 py-2 text-sm font-semibold text-slate-100 hover:bg-white/10 disabled:opacity-60"
          :disabled="disabled || loading"
          @click="zoekMedia"
        >
          {{ loading ? 'Laden...' : 'Zoeken' }}
        </button>
      </div>

      <p class="mb-2 text-xs text-slate-400">
        {{ items.length }} van {{ totalItems }} resources geladen
      </p>

      <p v-if="loading" class="text-sm text-slate-300">Media laden...</p>
      <p v-else-if="items.length === 0" class="text-sm text-slate-300">Geen resources gevonden.</p>

      <ul v-else class="space-y-2">
        <li v-for="item in zichtbareItems" :key="item.id" class="rounded border border-white/10 bg-black/20 px-3 py-2">
          <div class="flex flex-wrap items-center justify-between gap-2">
            <div>
              <p class="text-sm font-semibold">{{ item.naam }}</p>
              <p class="text-xs text-slate-400">{{ item.mime_type }} · {{ item.extensie.toUpperCase() }} · {{ Math.round(item.grootte / 1024) }} KB</p>
            </div>
            <button
              type="button"
              class="rounded border border-white/30 px-2 py-1 text-xs font-semibold hover:bg-white/10"
              :disabled="disabled"
              @click="insert(item.id)"
            >
              Invoegen
            </button>
          </div>
        </li>
      </ul>

      <div v-if="!loading && (zichtbaarAantal < items.length || hasMorePages())" class="mt-3">
        <button
          type="button"
          class="rounded border border-white/30 px-3 py-2 text-sm font-semibold text-slate-100 hover:bg-white/10 disabled:opacity-60"
          :disabled="disabled || loading"
          @click="laadMeerMedia"
        >
          Meer laden
        </button>
      </div>
    </div>
  </section>
</template>
