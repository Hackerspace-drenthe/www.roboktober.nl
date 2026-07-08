<script setup lang="ts">
import { getRichMediaLibrary, uploadRichMedia } from '@/api'
import type { RichMediaItem, RichMediaTargetType } from '@/types/api'
import { onMounted, ref } from 'vue'

const items = ref<RichMediaItem[]>([])
const loading = ref(false)
const uploading = ref(false)
const errorMessage = ref<string | null>(null)
const successMessage = ref<string | null>(null)
const search = ref('')

const bestand = ref<File | null>(null)
const naam = ref('')
const targetType = ref<RichMediaTargetType | ''>('')
const targetId = ref<number | null>(null)
const collectie = ref<'featured' | 'gallery' | 'bijlagen' | 'hero' | 'foto' | 'default'>('default')
const altTekst = ref('')
const onderschrift = ref('')

async function laadMedia(): Promise<void> {
  loading.value = true
  errorMessage.value = null

  try {
    const data = await getRichMediaLibrary({ q: search.value || undefined })
    items.value = data.data
  } catch {
    errorMessage.value = 'Media laden mislukt.'
  } finally {
    loading.value = false
  }
}

function onBestandChange(event: Event): void {
  const target = event.target as HTMLInputElement
  bestand.value = target.files?.[0] ?? null
}

async function upload(): Promise<void> {
  if (!bestand.value) {
    errorMessage.value = 'Selecteer eerst een bestand.'
    return
  }

  if (targetType.value !== '' && (targetId.value === null || targetId.value <= 0)) {
    errorMessage.value = 'Vul target ID in als je direct wilt koppelen.'
    return
  }

  uploading.value = true
  errorMessage.value = null
  successMessage.value = null

  try {
    const result = await uploadRichMedia({
      bestand: bestand.value,
      naam: naam.value || undefined,
      target_type: targetType.value || undefined,
      target_id: targetType.value ? (targetId.value ?? undefined) : undefined,
      collectie: collectie.value,
      alt_tekst: altTekst.value || undefined,
      onderschrift: onderschrift.value || undefined,
    })

    items.value = [result.data, ...items.value]

    successMessage.value = result.attached_to
      ? `Upload gelukt en gekoppeld aan ${result.attached_to.type} #${result.attached_to.id} (${result.attached_to.collectie}).`
      : 'Upload gelukt. Bestand staat in de media-library.'

    bestand.value = null
    naam.value = ''
    targetType.value = ''
    targetId.value = null
    altTekst.value = ''
    onderschrift.value = ''
  } catch {
    errorMessage.value = 'Upload mislukt. Controleer bestandstype, grootte of rechten.'
  } finally {
    uploading.value = false
  }
}

async function copy(text: string): Promise<void> {
  try {
    await navigator.clipboard.writeText(text)
    successMessage.value = 'Snippet gekopieerd naar klembord.'
  } catch {
    errorMessage.value = 'Kopieren mislukt op deze browser.'
  }
}

onMounted(async () => {
  await laadMedia()
})
</script>

<template>
  <main class="mx-auto min-h-[70vh] max-w-6xl px-6 py-12">
    <header class="mb-8">
      <h1 class="text-3xl font-black text-white">Admin · Media Library</h1>
      <p class="mt-2 text-slate-300">Upload afbeeldingen, STL, video en bijlagen en koppel ze direct aan pages, blogs of teams.</p>
    </header>

    <p v-if="errorMessage" class="mb-4 rounded-md border border-red-400/40 bg-red-950/30 px-3 py-2 text-sm text-red-200">{{ errorMessage }}</p>
    <p v-if="successMessage" class="mb-4 rounded-md border border-emerald-400/40 bg-emerald-950/30 px-3 py-2 text-sm text-emerald-200">{{ successMessage }}</p>

    <section class="mb-8 rounded-xl border border-white/10 bg-robo-dark/60 p-5">
      <h2 class="mb-4 text-lg font-bold text-white">Upload nieuw bestand</h2>

      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <label class="mb-1 block text-sm text-slate-300">Bestand</label>
          <input type="file" class="w-full rounded-md border border-white/20 bg-white/5 px-3 py-2 text-sm" @change="onBestandChange" />
        </div>

        <div>
          <label class="mb-1 block text-sm text-slate-300">Naam (optioneel)</label>
          <input v-model="naam" type="text" class="w-full rounded-md border border-white/20 bg-white/5 px-3 py-2 text-sm text-white" />
        </div>

        <div>
          <label class="mb-1 block text-sm text-slate-300">Target type (optioneel)</label>
          <select v-model="targetType" class="w-full rounded-md border border-white/20 bg-robo-dark px-3 py-2 text-sm text-white">
            <option value="">Niet koppelen</option>
            <option value="post">Post</option>
            <option value="page">Page</option>
            <option value="team">Team</option>
            <option value="team_update">Team update</option>
          </select>
        </div>

        <div>
          <label class="mb-1 block text-sm text-slate-300">Target ID (optioneel)</label>
          <input v-model.number="targetId" type="number" min="1" class="w-full rounded-md border border-white/20 bg-white/5 px-3 py-2 text-sm text-white" />
        </div>

        <div>
          <label class="mb-1 block text-sm text-slate-300">Collectie</label>
          <select v-model="collectie" class="w-full rounded-md border border-white/20 bg-robo-dark px-3 py-2 text-sm text-white">
            <option value="default">default</option>
            <option value="featured">featured</option>
            <option value="gallery">gallery</option>
            <option value="bijlagen">bijlagen</option>
            <option value="hero">hero</option>
            <option value="foto">foto</option>
          </select>
        </div>

        <div>
          <label class="mb-1 block text-sm text-slate-300">Alt tekst (optioneel)</label>
          <input v-model="altTekst" type="text" class="w-full rounded-md border border-white/20 bg-white/5 px-3 py-2 text-sm text-white" />
        </div>
      </div>

      <div class="mt-4">
        <label class="mb-1 block text-sm text-slate-300">Onderschrift (optioneel)</label>
        <input v-model="onderschrift" type="text" class="w-full rounded-md border border-white/20 bg-white/5 px-3 py-2 text-sm text-white" />
      </div>

      <button
        class="mt-5 rounded-md bg-robo-orange px-4 py-2 font-semibold text-white hover:bg-robo-orange-dark disabled:opacity-50"
        :disabled="uploading"
        @click="upload"
      >
        {{ uploading ? 'Uploaden...' : 'Uploaden' }}
      </button>
    </section>

    <section class="rounded-xl border border-white/10 bg-robo-dark/60 p-5">
      <div class="mb-4 flex items-center justify-between gap-3">
        <h2 class="text-lg font-bold text-white">Recente media</h2>
        <div class="flex gap-2">
          <input
            v-model="search"
            type="search"
            placeholder="Zoek op naam of mime..."
            class="rounded-md border border-white/20 bg-white/5 px-3 py-2 text-sm text-white"
          />
          <button class="rounded-md border border-white/30 px-3 py-2 text-sm text-slate-100 hover:bg-white/10" @click="laadMedia">Zoeken</button>
        </div>
      </div>

      <p v-if="loading" class="text-slate-300">Laden...</p>
      <p v-else-if="items.length === 0" class="text-slate-300">Nog geen media gevonden.</p>

      <ul v-else class="space-y-3">
        <li v-for="item in items" :key="item.id" class="rounded-lg border border-white/10 bg-black/20 p-4">
          <div class="mb-2 flex items-center justify-between gap-3">
            <p class="font-semibold text-white">{{ item.naam }}</p>
            <p class="text-xs text-slate-400">{{ item.mime_type }} · {{ item.extensie.toUpperCase() }}</p>
          </div>

          <a :href="item.url" target="_blank" rel="noopener" class="text-sm text-robo-orange underline">{{ item.url }}</a>

          <div class="mt-3 grid gap-2 md:grid-cols-2">
            <div>
              <p class="mb-1 text-xs uppercase tracking-wide text-slate-400">HTML snippet</p>
              <textarea class="w-full rounded border border-white/20 bg-robo-dark px-2 py-1 text-xs text-slate-200" rows="2" readonly :value="item.html_snippet" />
              <button class="mt-1 rounded border border-white/30 px-2 py-1 text-xs text-slate-100 hover:bg-white/10" @click="copy(item.html_snippet)">Kopieer HTML</button>
            </div>

            <div>
              <p class="mb-1 text-xs uppercase tracking-wide text-slate-400">Markdown snippet</p>
              <textarea class="w-full rounded border border-white/20 bg-robo-dark px-2 py-1 text-xs text-slate-200" rows="2" readonly :value="item.markdown_snippet" />
              <button class="mt-1 rounded border border-white/30 px-2 py-1 text-xs text-slate-100 hover:bg-white/10" @click="copy(item.markdown_snippet)">Kopieer Markdown</button>
            </div>
          </div>
        </li>
      </ul>
    </section>
  </main>
</template>
