<script setup lang="ts">
import { getEditionProgrammaItems, getEditions } from '@/api'
import headerImage from '@/assets/headers/header-programma.png'
import type { Edition, ProgrammaItem } from '@/types/api'
import { computed, onMounted, ref } from 'vue'

const heroStyle = {
  backgroundImage: `url(${headerImage})`,
  backgroundSize: 'cover',
  backgroundPosition: 'center',
}

const editie = ref<Edition | null>(null)
const programmaItems = ref<ProgrammaItem[]>([])
const mapCoords = ref<{ lat: number; lon: number } | null>(null)
const loading = ref(false)
const error = ref('')

function editieOsmUrl(item: Edition | null): string | null {
  const value = item?.location?.osm_url
  return typeof value === 'string' && value.trim() !== '' ? value.trim() : null
}

function editieLocatieZoektekst(item: Edition | null): string {
  if (!item?.location) {
    return ''
  }

  return item.location.full_address || [item.location.name, item.location.address, `${item.location.zipcode} ${item.location.place}`].join(', ')
}

function editieLocatieTitel(item: Edition | null): string {
  if (!item?.location) {
    return 'locatie volgt'
  }

  return item.location.name
}

function editieLocatieSubtitel(item: Edition | null): string {
  if (!item?.location) {
    return '-'
  }

  return item.location.full_address || [item.location.address, `${item.location.zipcode} ${item.location.place}`].join(', ')
}

function kiesProgrammaEditie(edities: Edition[]): Edition | null {
  if (edities.length === 0) {
    return null
  }

  const openEditie = [...edities]
    .filter((item) => !item.is_done)
    .sort((a, b) => a.start_at.localeCompare(b.start_at))[0]

  return openEditie ?? [...edities].sort((a, b) => a.start_at.localeCompare(b.start_at))[0] ?? null
}

const gegroepeerdeItems = computed(() => {
  const groups = new Map<string, ProgrammaItem[]>()

  for (const item of programmaItems.value) {
    const date = new Date(item.start_at)
    const key = Number.isNaN(date.getTime())
      ? 'Onbekende datum'
      : date.toLocaleDateString('nl-NL', {
        weekday: 'long',
        day: '2-digit',
        month: 'long',
        year: 'numeric',
      })

    const current = groups.get(key) ?? []
    current.push(item)
    groups.set(key, current)
  }

  return Array.from(groups.entries()).map(([datum, items]) => ({
    datum,
    items: [...items].sort((a, b) => a.start_at.localeCompare(b.start_at) || a.volgorde - b.volgorde || a.id - b.id),
  }))
})

async function geocodeLocatie(locatie: string): Promise<void> {
  mapCoords.value = null

  try {
    const response = await fetch(
      `https://nominatim.openstreetmap.org/search?format=json&limit=1&q=${encodeURIComponent(locatie)}`,
    )

    if (!response.ok) {
      return
    }

    const payload = await response.json() as Array<{ lat?: string; lon?: string }>
    const lat = Number(payload[0]?.lat)
    const lon = Number(payload[0]?.lon)

    if (Number.isFinite(lat) && Number.isFinite(lon)) {
      mapCoords.value = { lat, lon }
    }
  } catch {
    mapCoords.value = null
  }
}

function extractNodeId(osmUrl: string): string | null {
  const match = osmUrl.match(/\/node\/(\d+)/)
  return match?.[1] ?? null
}

async function geocodeOsmNodeUrl(osmUrl: string): Promise<void> {
  const nodeId = extractNodeId(osmUrl)

  if (!nodeId) {
    return
  }

  try {
    const response = await fetch(`https://www.openstreetmap.org/api/0.6/node/${nodeId}.json`)

    if (!response.ok) {
      return
    }

    const payload = await response.json() as {
      elements?: Array<{ lat?: number; lon?: number }>
    }

    const lat = Number(payload.elements?.[0]?.lat)
    const lon = Number(payload.elements?.[0]?.lon)

    if (Number.isFinite(lat) && Number.isFinite(lon)) {
      mapCoords.value = { lat, lon }
    }
  } catch {
    // Keep fallback behavior if node lookup fails.
  }
}

function formatDatum(iso: string | null): string {
  if (!iso) {
    return 'Datum volgt'
  }

  return new Date(iso).toLocaleDateString('nl-NL', {
    day: '2-digit',
    month: 'long',
    year: 'numeric',
  })
}

function formatEditiePeriode(item: Edition | null): string {
  if (!item) {
    return 'Programma wordt binnenkort bekendgemaakt.'
  }

  const start = formatDatum(item.start_at)
  const end = item.end_at ? formatDatum(item.end_at) : ''
  return end ? `${start} t/m ${end}` : start
}

function formatTijd(iso: string | null): string {
  if (!iso) {
    return 'Tijd volgt'
  }

  return new Date(iso).toLocaleTimeString('nl-NL', {
    hour: '2-digit',
    minute: '2-digit',
  })
}

function formatTijdRange(start: string, end: string | null): string {
  const startText = formatTijd(start)
  const endText = formatTijd(end)

  if (!end || endText === 'Tijd volgt') {
    return startText
  }

  return `${startText} - ${endText}`
}

const mapEmbedUrl = computed(() => {
  if (!mapCoords.value) {
    return ''
  }

  const lat = mapCoords.value.lat
  const lon = mapCoords.value.lon
  const delta = 0.01
  const left = lon - delta
  const right = lon + delta
  const top = lat + delta
  const bottom = lat - delta

  return `https://www.openstreetmap.org/export/embed.html?bbox=${left}%2C${bottom}%2C${right}%2C${top}&layer=mapnik&marker=${lat}%2C${lon}`
})

const mapFallbackUrl = computed(() => {
  if (!editie.value) {
    return 'https://www.openstreetmap.org'
  }

  const osmUrl = editieOsmUrl(editie.value)
  if (osmUrl) {
    return osmUrl
  }

  return `https://www.openstreetmap.org/search?query=${encodeURIComponent(editieLocatieZoektekst(editie.value))}`
})

async function openRouteInOpenStreetMap(): Promise<void> {
  if (!editie.value) {
    return
  }

  if (!mapCoords.value || !('geolocation' in navigator)) {
    window.open(mapFallbackUrl.value, '_blank', 'noopener,noreferrer')
    return
  }

  const destination = `${mapCoords.value.lat},${mapCoords.value.lon}`

  const currentPosition = await new Promise<GeolocationPosition | null>((resolve) => {
    navigator.geolocation.getCurrentPosition(
      (position) => resolve(position),
      () => resolve(null),
      { timeout: 5000, enableHighAccuracy: false },
    )
  })

  if (!currentPosition) {
    window.open(mapFallbackUrl.value, '_blank', 'noopener,noreferrer')
    return
  }

  const origin = `${currentPosition.coords.latitude},${currentPosition.coords.longitude}`
  const routeUrl = `https://www.openstreetmap.org/directions?engine=fossgis_osrm_car&route=${encodeURIComponent(`${origin};${destination}`)}`
  window.open(routeUrl, '_blank', 'noopener,noreferrer')
}

onMounted(async () => {
  loading.value = true
  error.value = ''

  try {
    const edities = await getEditions()
    editie.value = kiesProgrammaEditie(edities)

    if (editie.value) {
      programmaItems.value = await getEditionProgrammaItems(editie.value.id)
    }

    const geocodeQuery = editieLocatieZoektekst(editie.value)
    if (geocodeQuery) {
      await geocodeLocatie(geocodeQuery)
    }

    if (!mapCoords.value) {
      const osmUrl = editieOsmUrl(editie.value)
      if (osmUrl) {
        await geocodeOsmNodeUrl(osmUrl)
      }
    }
  } catch {
    error.value = 'Programma-informatie laden mislukt.'
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <main id="main-content">
    <!-- Hero -->
    <section class="relative overflow-hidden py-20 text-white" :style="heroStyle">
      <div class="absolute inset-0 bg-robo-dark/75" aria-hidden="true" />
      <div class="relative z-10 mx-auto max-w-3xl px-6 text-center">
        <h1 class="mb-4 text-4xl font-black md:text-5xl">Programma</h1>
        <p class="text-lg text-slate-300">
          Roboktober vindt plaats op <strong>{{ formatEditiePeriode(editie) }}</strong>
          bij <strong>{{ editieLocatieTitel(editie) }}</strong>.
        </p>
      </div>
    </section>

    <section class="bg-slate-100 py-12" aria-labelledby="locatie-title">
      <div class="mx-auto max-w-5xl px-6">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
          <h2 id="locatie-title" class="text-2xl font-black text-robo-dark">Locatie en route</h2>
          <button
            type="button"
            class="rounded-lg bg-robo-orange px-4 py-2 text-sm font-bold text-white transition hover:bg-robo-orange-dark"
            :disabled="!editie"
            @click="openRouteInOpenStreetMap"
          >
            Open route in OpenStreetMap
          </button>
        </div>

        <p v-if="loading" class="text-slate-600">Locatie laden...</p>
        <p v-else-if="error" class="text-red-700">{{ error }}</p>
        <p v-else-if="editie" class="mb-3 text-slate-700">
          <strong class="text-robo-dark">{{ editie.naam }}</strong> · {{ editieLocatieSubtitel(editie) }}
        </p>

        <div v-if="mapEmbedUrl" class="overflow-hidden rounded-xl border border-slate-300 bg-white shadow-sm">
          <iframe
            title="Locatiekaart Roboktober"
            :src="mapEmbedUrl"
            class="h-80 w-full"
            loading="lazy"
          />
        </div>
        <div v-else class="rounded-xl border border-slate-300 bg-white p-6 text-slate-700">
          Kaart laden is niet gelukt. Gebruik de routeknop om de locatie direct in OpenStreetMap te openen.
        </div>
      </div>
    </section>

    <!-- Geplande momenten -->
    <section class="bg-white py-20" aria-labelledby="programma-title">
      <div class="mx-auto max-w-3xl px-6">
        <h2 id="programma-title" class="mb-10 text-2xl font-black text-robo-dark">
          Programma per dag
        </h2>

        <p v-if="loading" class="text-slate-600">Programma laden...</p>
        <p v-else-if="error" class="text-red-700">{{ error }}</p>
        <p v-else-if="gegroepeerdeItems.length === 0" class="text-slate-600">
          Er zijn nog geen programma-items gepubliceerd voor deze editie.
        </p>

        <div v-else class="space-y-10">
          <section v-for="groep in gegroepeerdeItems" :key="groep.datum" class="rounded-xl border border-slate-200 bg-slate-50 p-5">
            <h3 class="mb-5 text-lg font-black capitalize text-robo-dark">{{ groep.datum }}</h3>

            <ol class="relative border-l-2 border-robo-orange/30">
              <li v-for="(item, index) in groep.items" :key="item.id" class="ml-8" :class="index < groep.items.length - 1 ? 'mb-8' : ''">
                <span
                  class="absolute -left-3 flex h-6 w-6 items-center justify-center rounded-full bg-robo-orange text-xs font-black text-white"
                  aria-hidden="true"
                >{{ index + 1 }}</span>

                <h4 class="mb-1 text-xl font-bold text-robo-dark">{{ item.titel }}</h4>
                <time class="mb-2 block text-sm font-medium text-robo-orange">{{ formatTijdRange(item.start_at, item.end_at) }}</time>

                <div class="prose prose-slate max-w-none text-slate-700">
                  <!-- eslint-disable-next-line vue/no-v-html -->
                  <div v-html="item.beschrijving_rendered" />
                </div>
              </li>
            </ol>
          </section>
        </div>

        <div class="mt-12 rounded-xl border border-robo-orange/30 bg-orange-50 p-6">
          <p class="text-sm text-slate-600">
            <strong class="text-robo-dark">Let op:</strong> Exacte data, tijden en locatiedetails
            kunnen nog wijzigen. Volg onze
            <a
              href="https://hackerspacedrenthe.nl"
              target="_blank"
              rel="noopener noreferrer"
              class="font-medium text-robo-orange underline hover:text-robo-orange-dark"
            >website</a>
            of schrijf je in als deelnemer om op de hoogte te blijven.
          </p>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section class="bg-robo-gray py-16 text-white" aria-labelledby="cta-title">
      <div class="mx-auto max-w-xl px-6 text-center">
        <h2 id="cta-title" class="mb-4 text-2xl font-black">Doe mee als team</h2>
        <p class="mb-8 text-slate-300">
          Bouw een antweight robot (max. 150 gram) en schrijf je in.
          Beginners zijn van harte welkom.
        </p>
        <RouterLink
          to="/aanmelden"
          class="inline-block rounded-lg bg-robo-orange px-8 py-3 font-bold text-white transition hover:bg-robo-orange-dark focus:outline-none focus:ring-2 focus:ring-robo-orange focus:ring-offset-2 focus:ring-offset-robo-gray"
        >
          Aanmelden
        </RouterLink>
      </div>
    </section>
  </main>
</template>
