<script setup lang="ts">
import { createAdminLink, deleteAdminLink, getAdminLinks, updateAdminLink } from '@/api'
import type { AdminLink, AdminLinkPayload, LinkCategorie } from '@/types/api'
import { onMounted, reactive, ref } from 'vue'

const links = ref<AdminLink[]>([])
const loading = ref(false)
const errorMessage = ref<string | null>(null)
const successMessage = ref<string | null>(null)
const editingId = ref<number | null>(null)

const filters = reactive<{ q: string; categorie: '' | LinkCategorie }>({
  q: '',
  categorie: '',
})

const form = reactive<{
  titel: string
  url: string
  beschrijving: string
  categorie: LinkCategorie
  eigenaar: string
  verified_at: string
}>({
  titel: '',
  url: '',
  beschrijving: '',
  categorie: 'community',
  eigenaar: '',
  verified_at: '',
})

function toLocalInputValue(isoValue: string | null): string {
  if (!isoValue) return ''

  const date = new Date(isoValue)
  if (Number.isNaN(date.getTime())) return ''

  const timezoneOffsetMs = date.getTimezoneOffset() * 60_000
  return new Date(date.getTime() - timezoneOffsetMs).toISOString().slice(0, 16)
}

function toIsoValue(localValue: string): string | null {
  if (localValue.trim() === '') return null

  const date = new Date(localValue)
  if (Number.isNaN(date.getTime())) return null

  return date.toISOString()
}

function resetForm(): void {
  editingId.value = null
  form.titel = ''
  form.url = ''
  form.beschrijving = ''
  form.categorie = 'community'
  form.eigenaar = ''
  form.verified_at = ''
}

function editLink(link: AdminLink): void {
  editingId.value = link.id
  form.titel = link.titel
  form.url = link.url
  form.beschrijving = link.beschrijving ?? ''
  form.categorie = link.categorie
  form.eigenaar = link.eigenaar ?? ''
  form.verified_at = toLocalInputValue(link.verified_at)
}

function buildPayload(): AdminLinkPayload {
  return {
    titel: form.titel.trim(),
    url: form.url.trim(),
    beschrijving: form.beschrijving.trim() || null,
    categorie: form.categorie,
    eigenaar: form.eigenaar.trim() || null,
    verified_at: toIsoValue(form.verified_at),
  }
}

async function loadLinks(): Promise<void> {
  loading.value = true
  errorMessage.value = null

  try {
    const response = await getAdminLinks({
      q: filters.q.trim() || undefined,
      categorie: filters.categorie || undefined,
    })
    links.value = response.data
  } catch {
    errorMessage.value = 'Links laden mislukt.'
  } finally {
    loading.value = false
  }
}

async function submitForm(): Promise<void> {
  errorMessage.value = null
  successMessage.value = null

  try {
    const payload = buildPayload()

    if (editingId.value === null) {
      await createAdminLink(payload)
      successMessage.value = 'Link toegevoegd.'
    } else {
      await updateAdminLink(editingId.value, payload)
      successMessage.value = 'Link bijgewerkt.'
    }

    resetForm()
    await loadLinks()
  } catch {
    errorMessage.value = 'Opslaan van link mislukt. Controleer titel, URL en categorie.'
  }
}

async function removeLink(link: AdminLink): Promise<void> {
  const confirmed = window.confirm(`Weet je zeker dat je link "${link.titel}" wilt verwijderen?`)
  if (!confirmed) return

  errorMessage.value = null
  successMessage.value = null

  try {
    await deleteAdminLink(link.id)
    successMessage.value = 'Link verwijderd.'

    if (editingId.value === link.id) {
      resetForm()
    }

    await loadLinks()
  } catch {
    errorMessage.value = 'Verwijderen van link mislukt.'
  }
}

onMounted(async () => {
  await loadLinks()
})
</script>

<template>
  <main class="mx-auto min-h-[70vh] max-w-7xl px-6 py-12">
    <header class="mb-8">
      <h1 class="text-3xl font-black text-white">Admin · Links</h1>
      <p class="mt-2 text-slate-300">Volledig API-beheer van Build Hub links.</p>
    </header>

    <p v-if="errorMessage" class="mb-4 rounded-md border border-red-400/40 bg-red-950/30 px-3 py-2 text-sm text-red-200">
      {{ errorMessage }}
    </p>
    <p v-if="successMessage" class="mb-4 rounded-md border border-emerald-400/40 bg-emerald-950/30 px-3 py-2 text-sm text-emerald-200">
      {{ successMessage }}
    </p>

    <section class="mb-6 rounded-xl border border-white/10 bg-robo-dark/60 p-5">
      <div class="mb-3 flex items-center justify-between">
        <h2 class="text-lg font-bold text-white">{{ editingId === null ? 'Nieuwe link' : 'Link bewerken' }}</h2>
        <button
          v-if="editingId !== null"
          type="button"
          class="rounded-md border border-white/30 px-3 py-1.5 text-xs font-semibold text-slate-100 hover:bg-white/10"
          @click="resetForm"
        >
          Annuleren
        </button>
      </div>

      <form class="grid gap-3 md:grid-cols-2" @submit.prevent="submitForm">
        <label class="space-y-1 text-sm text-slate-300">
          <span class="font-semibold text-slate-200">Titel</span>
          <input v-model="form.titel" required class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white" />
        </label>

        <label class="space-y-1 text-sm text-slate-300">
          <span class="font-semibold text-slate-200">URL</span>
          <input v-model="form.url" type="url" required class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white" />
        </label>

        <label class="space-y-1 text-sm text-slate-300">
          <span class="font-semibold text-slate-200">Categorie</span>
          <select v-model="form.categorie" class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white">
            <option value="wallie">Wallie</option>
            <option value="community">Community</option>
            <option value="competitie">Competitie</option>
            <option value="tools">Tools</option>
            <option value="onderdelen">Onderdelen</option>
            <option value="documentatie">Documentatie</option>
          </select>
        </label>

        <label class="space-y-1 text-sm text-slate-300">
          <span class="font-semibold text-slate-200">Eigenaar</span>
          <input v-model="form.eigenaar" class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white" />
        </label>

        <label class="space-y-1 text-sm text-slate-300">
          <span class="font-semibold text-slate-200">Laatste verificatie</span>
          <input v-model="form.verified_at" type="datetime-local" class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white" />
        </label>

        <label class="space-y-1 text-sm text-slate-300 md:col-span-2">
          <span class="font-semibold text-slate-200">Beschrijving</span>
          <textarea v-model="form.beschrijving" rows="3" class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white" />
        </label>

        <div class="md:col-span-2">
          <button type="submit" class="rounded-lg bg-robo-orange px-4 py-2 font-bold text-white hover:bg-robo-orange-dark">
            {{ editingId === null ? 'Link toevoegen' : 'Wijzigingen opslaan' }}
          </button>
        </div>
      </form>
    </section>

    <section class="mb-6 rounded-xl border border-white/10 bg-robo-dark/60 p-4">
      <form class="grid gap-2 md:grid-cols-3" @submit.prevent="loadLinks">
        <input
          v-model="filters.q"
          placeholder="Zoek titel/url/eigenaar"
          class="rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white"
        />
        <select v-model="filters.categorie" class="rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white">
          <option value="">Alle categorieen</option>
          <option value="wallie">Wallie</option>
          <option value="community">Community</option>
          <option value="competitie">Competitie</option>
          <option value="tools">Tools</option>
          <option value="onderdelen">Onderdelen</option>
          <option value="documentatie">Documentatie</option>
        </select>
        <button type="submit" class="rounded-lg bg-slate-700 px-4 py-2 font-semibold text-white hover:bg-slate-600">Filter</button>
      </form>
    </section>

    <section class="overflow-hidden rounded-xl border border-white/10 bg-robo-dark/60">
      <table class="w-full border-collapse">
        <thead class="bg-slate-900/70 text-left text-xs uppercase tracking-wide text-slate-300">
          <tr>
            <th class="px-4 py-3">Titel</th>
            <th class="px-4 py-3">Categorie</th>
            <th class="px-4 py-3">URL</th>
            <th class="px-4 py-3">Acties</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td colspan="4" class="px-4 py-6 text-center text-slate-300">Laden...</td>
          </tr>
          <tr v-else-if="links.length === 0">
            <td colspan="4" class="px-4 py-6 text-center text-slate-300">Geen links gevonden.</td>
          </tr>
          <tr v-for="link in links" :key="link.id" class="border-t border-white/10">
            <td class="px-4 py-3 text-white">{{ link.titel }}</td>
            <td class="px-4 py-3 text-slate-300">{{ link.categorie_label }}</td>
            <td class="px-4 py-3 text-slate-300">
              <a :href="link.url" target="_blank" rel="noopener noreferrer" class="underline hover:text-robo-orange">{{ link.url }}</a>
            </td>
            <td class="px-4 py-3">
              <div class="flex flex-wrap gap-2">
                <button
                  type="button"
                  class="rounded-md border border-robo-orange/60 px-3 py-1.5 text-xs font-semibold text-robo-orange hover:bg-robo-orange/15"
                  @click="editLink(link)"
                >
                  Bewerken
                </button>
                <button
                  type="button"
                  class="rounded-md border border-red-400/50 px-3 py-1.5 text-xs font-semibold text-red-200 hover:bg-red-500/15"
                  @click="removeLink(link)"
                >
                  Verwijderen
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </section>
  </main>
</template>
