<script setup lang="ts">
import { onMounted, ref } from 'vue'
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
  load,
  upload,
  snippetFor,
} = useRichMediaLibrary()

const search = ref('')
const bestand = ref<File | null>(null)
const naam = ref('')
const altTekst = ref('')
const onderschrift = ref('')

function onBestandChange(event: Event): void {
  const target = event.target as HTMLInputElement
  bestand.value = target.files?.[0] ?? null
}

async function zoekMedia(): Promise<void> {
  await load(search.value)
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

onMounted(async () => {
  await load()
})
</script>

<template>
  <section class="rounded-xl border border-white/15 bg-black/20 p-4">
    <div class="mb-3">
      <h3 class="text-lg font-bold">Resources in content invoegen</h3>
      <p class="text-sm text-slate-300">
        Upload of kies een resource en voeg direct een {{ props.contentFormat.toUpperCase() }} snippet toe aan je content.
      </p>
    </div>

    <p v-if="errorMessage" class="mb-3 rounded border border-red-500/30 bg-red-500/10 px-3 py-2 text-sm text-red-300">
      {{ errorMessage }}
    </p>

    <div class="grid gap-3 rounded-lg border border-white/10 bg-white/5 p-3 md:grid-cols-2">
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

    <div class="mt-4 rounded-lg border border-white/10 bg-white/5 p-3">
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

      <p v-if="loading" class="text-sm text-slate-300">Media laden...</p>
      <p v-else-if="items.length === 0" class="text-sm text-slate-300">Geen resources gevonden.</p>

      <ul v-else class="space-y-2">
        <li v-for="item in items.slice(0, 8)" :key="item.id" class="rounded border border-white/10 bg-black/20 px-3 py-2">
          <div class="flex flex-wrap items-center justify-between gap-2">
            <div>
              <p class="text-sm font-semibold">{{ item.naam }}</p>
              <p class="text-xs text-slate-400">{{ item.mime_type }}</p>
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
    </div>
  </section>
</template>
