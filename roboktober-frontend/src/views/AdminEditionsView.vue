<script setup lang="ts">
import { createAdminEdition, deleteAdminEdition, getAdminEditions, getAdminLocations, updateAdminEdition } from '@/api'
import type { AdminEditionPayload, Edition, EditionLocation } from '@/types/api'
import { onMounted, reactive, ref } from 'vue'

const editions = ref<Edition[]>([])
const loading = ref(false)
const errorMessage = ref<string | null>(null)
const successMessage = ref<string | null>(null)
const editingId = ref<number | null>(null)
const locationLookup = ref('')
const locationSuggestions = ref<EditionLocation[]>([])
const locationLookupBusy = ref(false)
let locationLookupTimer: ReturnType<typeof setTimeout> | null = null

const form = reactive({
  naam: '',
  location_name: '',
  location_address: '',
  location_place: '',
  location_zipcode: '',
  location_osm_url: '',
  location_instructions: '',
  omschrijving: '',
  start_at: '',
  end_at: '',
  is_done: false,
  afbeelding: null as File | null,
  afbeelding_verwijderen: false,
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
  locationLookup.value = ''
  locationSuggestions.value = []
  form.naam = ''
  form.location_name = ''
  form.location_address = ''
  form.location_place = ''
  form.location_zipcode = ''
  form.location_osm_url = ''
  form.location_instructions = ''
  form.omschrijving = ''
  form.start_at = ''
  form.end_at = ''
  form.is_done = false
  form.afbeelding = null
  form.afbeelding_verwijderen = false
}

function applyEditionToForm(edition: Edition): void {
  editingId.value = edition.id
  locationLookup.value = edition.location?.full_address ?? ''
  form.naam = edition.naam
  form.location_name = edition.location?.name ?? ''
  form.location_address = edition.location?.address ?? ''
  form.location_place = edition.location?.place ?? ''
  form.location_zipcode = edition.location?.zipcode ?? ''
  form.location_osm_url = edition.location?.osm_url ?? ''
  form.location_instructions = edition.location?.instructions ?? ''
  form.omschrijving = edition.omschrijving ?? ''
  form.start_at = toLocalInputValue(edition.start_at)
  form.end_at = toLocalInputValue(edition.end_at)
  form.is_done = edition.is_done
  form.afbeelding = null
  form.afbeelding_verwijderen = false
}

function buildPayload(): AdminEditionPayload {
  const startAt = toIsoValue(form.start_at)

  if (!startAt) {
    throw new Error('Startdatum is ongeldig.')
  }

  return {
    naam: form.naam.trim(),
    location: {
      name: form.location_name.trim(),
      address: form.location_address.trim(),
      place: form.location_place.trim(),
      zipcode: form.location_zipcode.trim(),
      osm_url: form.location_osm_url.trim() || null,
      instructions: form.location_instructions.trim() || null,
    },
    omschrijving: form.omschrijving.trim() || null,
    start_at: startAt,
    end_at: toIsoValue(form.end_at),
    is_done: form.is_done,
    afbeelding: form.afbeelding,
    afbeelding_verwijderen: form.afbeelding_verwijderen,
  }
}

function onAfbeeldingChange(event: Event): void {
  const input = event.target as HTMLInputElement
  form.afbeelding = input.files?.[0] ?? null

  if (form.afbeelding !== null) {
    form.afbeelding_verwijderen = false
  }
}

async function loadEditions(): Promise<void> {
  loading.value = true
  errorMessage.value = null

  try {
    const response = await getAdminEditions()
    editions.value = response.data
  } catch {
    errorMessage.value = 'Edities laden mislukt.'
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
      await createAdminEdition(payload)
      successMessage.value = 'Editie toegevoegd.'
    } else {
      await updateAdminEdition(editingId.value, payload)
      successMessage.value = 'Editie bijgewerkt.'
    }

    resetForm()
    await loadEditions()
  } catch {
    errorMessage.value = 'Opslaan van editie mislukt. Controleer verplichte velden en datums.'
  }
}

async function removeEdition(edition: Edition): Promise<void> {
  const confirmed = window.confirm(`Weet je zeker dat je editie \"${edition.naam}\" wilt verwijderen?`)
  if (!confirmed) return

  errorMessage.value = null
  successMessage.value = null

  try {
    await deleteAdminEdition(edition.id)
    successMessage.value = 'Editie verwijderd.'

    if (editingId.value === edition.id) {
      resetForm()
    }

    await loadEditions()
  } catch {
    errorMessage.value = 'Verwijderen mislukt. Mogelijk zijn er nog teams of competitiegegevens gekoppeld.'
  }
}

function formatDateTime(value: string | null): string {
  if (!value) return '-'

  return new Date(value).toLocaleString('nl-NL', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

function formatEditionLocation(edition: Edition): string {
  if (!edition.location) {
    return '-'
  }

  return edition.location.full_address || `${edition.location.name}, ${edition.location.address}, ${edition.location.zipcode} ${edition.location.place}`
}

async function loadLocationSuggestions(query: string): Promise<void> {
  if (query.trim().length < 2) {
    locationSuggestions.value = []
    return
  }

  locationLookupBusy.value = true

  try {
    const response = await getAdminLocations({ q: query.trim(), page: 1 })
    locationSuggestions.value = response.data
  } catch {
    locationSuggestions.value = []
  } finally {
    locationLookupBusy.value = false
  }
}

async function loadInitialLocationSuggestions(): Promise<void> {
  locationLookupBusy.value = true

  try {
    const response = await getAdminLocations({ page: 1 })
    locationSuggestions.value = response.data
  } catch {
    locationSuggestions.value = []
  } finally {
    locationLookupBusy.value = false
  }
}

function onLocationLookupInput(): void {
  if (locationLookupTimer) {
    clearTimeout(locationLookupTimer)
  }

  locationLookupTimer = setTimeout(() => {
    void loadLocationSuggestions(locationLookup.value)
  }, 250)
}

function onLocationLookupFocus(): void {
  if (locationLookup.value.trim().length >= 2) {
    void loadLocationSuggestions(locationLookup.value)
    return
  }

  if (locationSuggestions.value.length === 0 && !locationLookupBusy.value) {
    void loadInitialLocationSuggestions()
  }
}

function selectLocationSuggestion(locationId: number): void {
  const selected = locationSuggestions.value.find((location) => location.id === locationId)

  if (!selected) {
    return
  }

  form.location_name = selected.name
  form.location_address = selected.address
  form.location_place = selected.place
  form.location_zipcode = selected.zipcode
  form.location_osm_url = selected.osm_url ?? ''
  form.location_instructions = selected.instructions ?? ''
  locationLookup.value = selected.full_address
  locationSuggestions.value = []
}

function onLocationSuggestionChange(event: Event): void {
  const select = event.target as HTMLSelectElement
  const selectedId = Number(select.value)

  if (!Number.isFinite(selectedId) || selectedId <= 0) {
    return
  }

  selectLocationSuggestion(selectedId)
  select.value = ''
}

onMounted(async () => {
  await loadEditions()
})
</script>

<template>
  <main class="mx-auto min-h-[70vh] max-w-7xl px-6 py-12">
    <header class="mb-8">
      <h1 class="text-3xl font-black text-white">Admin · Edities</h1>
      <p class="mt-2 text-slate-300">Volledig API-beheer van edities, inclusief locatie voor programma en routes.</p>
    </header>

    <p v-if="errorMessage" class="mb-4 rounded-md border border-red-400/40 bg-red-950/30 px-3 py-2 text-sm text-red-200">
      {{ errorMessage }}
    </p>
    <p v-if="successMessage" class="mb-4 rounded-md border border-emerald-400/40 bg-emerald-950/30 px-3 py-2 text-sm text-emerald-200">
      {{ successMessage }}
    </p>

    <section class="mb-6 rounded-xl border border-white/10 bg-robo-dark/60 p-5">
      <div class="mb-3 flex items-center justify-between">
        <h2 class="text-lg font-bold text-white">{{ editingId === null ? 'Nieuwe editie' : 'Editie bewerken' }}</h2>
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
          <span class="font-semibold text-slate-200">Naam</span>
          <input v-model="form.naam" required class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white" />
        </label>

        <label class="space-y-1 text-sm text-slate-300">
          <span class="font-semibold text-slate-200">Locatienaam</span>
          <input v-model="form.location_name" required class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white" />
        </label>

        <label class="space-y-1 text-sm text-slate-300 md:col-span-2">
          <span class="font-semibold text-slate-200">Herbruikbare locatie zoeken</span>
          <input
            v-model="locationLookup"
            type="search"
            class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white"
            placeholder="Zoek op naam, adres, plaats of postcode"
            @input="onLocationLookupInput"
            @focus="onLocationLookupFocus"
          />
          <p v-if="locationLookupBusy" class="text-xs text-slate-400">Locaties zoeken...</p>
          <select
            v-if="locationSuggestions.length > 0"
            class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white"
            @change="onLocationSuggestionChange"
          >
            <option value="">Kies een bestaande locatie</option>
            <option v-for="location in locationSuggestions" :key="location.id" :value="location.id">
              {{ location.full_address }}
            </option>
          </select>
        </label>

        <label class="space-y-1 text-sm text-slate-300 md:col-span-2">
          <span class="font-semibold text-slate-200">Adres</span>
          <input v-model="form.location_address" required class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white" />
        </label>

        <label class="space-y-1 text-sm text-slate-300">
          <span class="font-semibold text-slate-200">Postcode</span>
          <input v-model="form.location_zipcode" required class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white" />
        </label>

        <label class="space-y-1 text-sm text-slate-300">
          <span class="font-semibold text-slate-200">Plaats</span>
          <input v-model="form.location_place" required class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white" />
        </label>

        <label class="space-y-1 text-sm text-slate-300 md:col-span-2">
          <span class="font-semibold text-slate-200">Locatie-instructies</span>
          <textarea v-model="form.location_instructions" rows="2" class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white" />
        </label>

        <label class="space-y-1 text-sm text-slate-300 md:col-span-2">
          <span class="font-semibold text-slate-200">OpenStreetMap URL (optioneel)</span>
          <input
            v-model="form.location_osm_url"
            type="url"
            placeholder="https://www.openstreetmap.org/node/..."
            class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white"
          />
        </label>

        <label class="space-y-1 text-sm text-slate-300">
          <span class="font-semibold text-slate-200">Start datum/tijd</span>
          <input v-model="form.start_at" type="datetime-local" required class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white" />
        </label>

        <label class="space-y-1 text-sm text-slate-300">
          <span class="font-semibold text-slate-200">Eind datum/tijd</span>
          <input v-model="form.end_at" type="datetime-local" class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white" />
        </label>

        <label class="space-y-1 text-sm text-slate-300 md:col-span-2">
          <span class="font-semibold text-slate-200">Omschrijving</span>
          <textarea v-model="form.omschrijving" rows="3" class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white" />
        </label>

        <label class="space-y-1 text-sm text-slate-300 md:col-span-2">
          <span class="font-semibold text-slate-200">Afbeelding</span>
          <input
            type="file"
            accept="image/jpeg,image/png,image/webp"
            class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white"
            @change="onAfbeeldingChange"
          />
        </label>

        <label class="flex items-center gap-2 text-sm text-slate-200 md:col-span-2">
          <input v-model="form.afbeelding_verwijderen" type="checkbox" class="h-4 w-4 rounded border-white/20 bg-slate-900 text-robo-orange" />
          Bestaande editie-afbeelding verwijderen
        </label>

        <label class="flex items-center gap-2 text-sm text-slate-200 md:col-span-2">
          <input v-model="form.is_done" type="checkbox" class="h-4 w-4 rounded border-white/20 bg-slate-900 text-robo-orange" />
          Editie afsluiten (done)
        </label>

        <div class="md:col-span-2">
          <button type="submit" class="rounded-lg bg-robo-orange px-4 py-2 font-bold text-white hover:bg-robo-orange-dark">
            {{ editingId === null ? 'Editie toevoegen' : 'Wijzigingen opslaan' }}
          </button>
        </div>
      </form>
    </section>

    <section class="overflow-hidden rounded-xl border border-white/10 bg-robo-dark/60">
      <table class="w-full border-collapse">
        <thead class="bg-slate-900/70 text-left text-xs uppercase tracking-wide text-slate-300">
          <tr>
            <th class="px-4 py-3">Naam</th>
            <th class="px-4 py-3">Locatie</th>
            <th class="px-4 py-3">Afbeelding</th>
            <th class="px-4 py-3">Start</th>
            <th class="px-4 py-3">Einde</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3">Acties</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td colspan="7" class="px-4 py-6 text-center text-slate-300">Laden...</td>
          </tr>
          <tr v-else-if="editions.length === 0">
            <td colspan="7" class="px-4 py-6 text-center text-slate-300">Geen edities gevonden.</td>
          </tr>
          <tr v-for="edition in editions" :key="edition.id" class="border-t border-white/10">
            <td class="px-4 py-3 text-white">{{ edition.naam }}</td>
            <td class="px-4 py-3 text-slate-300">{{ formatEditionLocation(edition) }}</td>
            <td class="px-4 py-3 text-slate-300">
              <span v-if="edition.afbeelding_url">Ja</span>
              <span v-else>-</span>
            </td>
            <td class="px-4 py-3 text-slate-300">{{ formatDateTime(edition.start_at) }}</td>
            <td class="px-4 py-3 text-slate-300">{{ formatDateTime(edition.end_at) }}</td>
            <td class="px-4 py-3 text-sm" :class="edition.is_done ? 'text-amber-300' : 'text-emerald-300'">
              {{ edition.is_done ? 'Afgesloten' : 'Open' }}
            </td>
            <td class="px-4 py-3">
              <div class="flex flex-wrap gap-2">
                <button
                  type="button"
                  class="rounded-md border border-robo-orange/60 px-3 py-1.5 text-xs font-semibold text-robo-orange hover:bg-robo-orange/15"
                  @click="applyEditionToForm(edition)"
                >
                  Bewerken
                </button>
                <button
                  type="button"
                  class="rounded-md border border-red-400/50 px-3 py-1.5 text-xs font-semibold text-red-200 hover:bg-red-500/15"
                  @click="removeEdition(edition)"
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
