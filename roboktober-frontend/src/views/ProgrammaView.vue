<script setup lang="ts">
import { getEditionProgrammaItems, getEditions } from '@/api'
import KickoffRecording from '@/components/KickoffRecording.vue'
import { useAuth } from '@/composables/useAuth'
import type { Edition, ProgrammaItem } from '@/types/api'
import { computed, onMounted, ref } from 'vue'

const mediaUrl = (file: string) => `${import.meta.env.BASE_URL}media/evenement/${file}`
const sfeerFotos = [
  {
    file: 'mensenbezig/5945232600760783556.webp',
    alt: 'Een deelnemer werkt aan elektronica bij de soldeerwerkplek',
    titel: 'Samen aan de werkbank',
    tekst: 'Van losse onderdelen naar je eigen robot. Samen bouwen en kennis delen staan centraal.',
  },
  {
    file: 'robots/5945232600760783560.webp',
    alt: 'Vier verschillende robotchassis met elektronica op een werkbank',
    titel: 'Elk ontwerp is anders',
    tekst: 'Een eigen chassis, andere wielen of slimme elektronica: bekijk wat er in de werkplaats ontstaat.',
  },
]

const editie = ref<Edition | null>(null)
const programmaItems = ref<ProgrammaItem[]>([])
const loading = ref(false)
const error = ref('')
const auth = useAuth()
const joinCtaPath = computed(() => (auth.isAuthenticated.value ? '/aanmelden' : '/registreren'))
const joinCtaLabel = computed(() => (auth.isAuthenticated.value ? 'Aanmelden' : 'Maak account aan en meld je aan'))

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
  const query = editieLocatieZoektekst(editie.value)

  if (!query) {
    return ''
  }

  return `https://maps.google.com/maps?q=${encodeURIComponent(query)}&t=&z=15&ie=UTF8&iwloc=&output=embed`
})

const mapFallbackUrl = computed(() => {
  const query = editieLocatieZoektekst(editie.value)
  return query ? `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(query)}` : 'https://www.google.com/maps'
})

async function openRouteInGoogleMaps(): Promise<void> {
  if (!editie.value) {
    return
  }

  const destinationQuery = editieLocatieZoektekst(editie.value)

  if (!destinationQuery) {
    window.open('https://www.google.com/maps', '_blank', 'noopener,noreferrer')
    return
  }

  if (!('geolocation' in navigator)) {
    window.open(mapFallbackUrl.value, '_blank', 'noopener,noreferrer')
    return
  }

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
  const routeUrl = `https://www.google.com/maps/dir/?api=1&origin=${encodeURIComponent(origin)}&destination=${encodeURIComponent(destinationQuery)}&travelmode=driving`
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
  } catch {
    error.value = 'Programma-informatie laden mislukt.'
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <main id="main-content">
    <section class="overflow-hidden bg-robo-dark py-14 text-white md:py-20">
      <div class="mx-auto grid max-w-6xl items-center gap-10 px-6 lg:grid-cols-2">
        <div>
          <p class="mb-4 text-sm font-bold uppercase tracking-widest text-robo-orange">Samen bouwen. Uitproberen. Verbeteren.</p>
          <h1 class="mb-5 text-4xl font-black md:text-6xl">Programma</h1>
          <p class="max-w-xl text-lg leading-relaxed text-slate-300">
            Van de eerste onderdelen tot een rijdende robot. Ontdek de geplande momenten
            en bouw mee bij Roboktober.
          </p>
          <dl v-if="editie" class="mt-7 grid gap-4 border-t border-white/15 pt-6 sm:grid-cols-2">
            <div>
              <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Wanneer</dt>
              <dd class="mt-2 font-semibold">{{ formatEditiePeriode(editie) }}</dd>
            </div>
            <div>
              <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Waar</dt>
              <dd class="mt-2 font-semibold">{{ editieLocatieTitel(editie) }}</dd>
            </div>
          </dl>
          <div class="mt-8 flex flex-wrap gap-3">
            <a href="#programma-title" class="rounded-lg bg-robo-orange px-5 py-3 font-bold text-white transition hover:bg-robo-orange-dark focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white">Bekijk het programma &darr;</a>
            <a href="#locatie-title" class="rounded-lg border border-white/30 px-5 py-3 font-bold transition hover:bg-white/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-robo-orange">Locatie en route</a>
          </div>
        </div>
        <figure class="overflow-hidden rounded-3xl border border-white/15 bg-white/5 shadow-xl">
          <img
            :src="mediaUrl('mensenbezig/5945232600760783558.webp')"
            alt="Een gele antweight robot met paarse printplaat en witte wielen op de bouwtafel"
            width="1280"
            height="964"
            fetchpriority="high"
            class="aspect-4/3 w-full object-cover"
          />
          <figcaption class="px-5 py-4 text-sm text-slate-300">Van onderdelen naar een eigen robot bij Hackerspace Drenthe.</figcaption>
        </figure>
      </div>
    </section>

    <section class="bg-white py-10" aria-label="Kickoff-presentatie">
      <div class="mx-auto max-w-6xl px-6">
        <KickoffRecording />
      </div>
    </section>

    <section class="bg-slate-50 py-16 md:py-20" aria-labelledby="programma-title">
      <div class="mx-auto max-w-6xl px-6">
        <p class="mb-3 text-sm font-bold uppercase tracking-widest text-robo-orange">De agenda</p>
        <h2 id="programma-title" class="scroll-mt-28 text-3xl font-black text-robo-dark">
          Programma per dag
        </h2>
        <p class="mb-10 mt-3 max-w-2xl text-slate-600">Alle geplande activiteiten op een rij. Bekijk per dag wat er op het programma staat.</p>

        <div class="grid items-start gap-10 lg:grid-cols-[minmax(0,1fr)_20rem]">
          <div class="min-w-0">
            <p v-if="loading" role="status" class="rounded-2xl border border-slate-200 bg-white p-6 text-slate-600">Programma laden...</p>
            <p v-else-if="error" role="alert" class="rounded-2xl border border-red-200 bg-red-50 p-6 text-red-700">{{ error }}</p>
            <p v-else-if="gegroepeerdeItems.length === 0" class="rounded-2xl border border-slate-200 bg-white p-6 text-slate-600">
              Er zijn nog geen programma-items gepubliceerd voor deze editie.
            </p>

            <div v-else class="space-y-8">
              <section v-for="groep in gegroepeerdeItems" :key="groep.datum" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <h3 class="border-b border-slate-200 bg-robo-dark px-6 py-4 text-lg font-black capitalize text-white">{{ groep.datum }}</h3>

                <ol class="m-6 border-l-2 border-robo-orange/25">
                  <li v-for="(item, index) in groep.items" :key="item.id" class="relative ml-6 min-w-0" :class="index < groep.items.length - 1 ? 'pb-9' : ''">
                    <span
                      class="absolute -left-[2.25rem] flex h-6 w-6 items-center justify-center rounded-full bg-robo-orange text-xs font-black text-white ring-4 ring-white"
                      aria-hidden="true"
                    >{{ index + 1 }}</span>

                    <time :datetime="item.start_at" class="mb-3 inline-block rounded-md bg-robo-orange/10 px-2.5 py-1 text-sm font-bold text-robo-dark">{{ formatTijdRange(item.start_at, item.end_at) }}</time>
                    <h4 class="mb-3 text-xl font-black text-robo-dark">{{ item.titel }}</h4>

                    <div class="prose prose-slate max-w-none break-words text-sm text-slate-700">
                      <!-- eslint-disable-next-line vue/no-v-html -->
                      <div v-html="item.beschrijving_rendered" />
                    </div>
                  </li>
                </ol>
              </section>
            </div>

            <div class="mt-8 rounded-xl border border-robo-orange/30 bg-orange-50 p-5">
              <p class="text-sm text-slate-600">
                <strong class="text-robo-dark">Let op:</strong> Exacte data, tijden en locatiedetails
                kunnen nog wijzigen. Volg onze
                <a
                  href="https://hackerspace-drenthe.nl"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="font-medium text-robo-orange underline hover:text-robo-orange-dark"
                >website</a>
                of schrijf je in als deelnemer om op de hoogte te blijven.
              </p>
            </div>
          </div>

          <aside aria-label="Sfeer uit de werkplaats" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-1">
            <figure v-for="foto in sfeerFotos" :key="foto.file" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
              <img :src="mediaUrl(foto.file)" :alt="foto.alt" width="1280" height="964" loading="lazy" class="aspect-4/3 w-full object-cover" />
              <figcaption class="p-5">
                <p class="font-black text-robo-dark">{{ foto.titel }}</p>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ foto.tekst }}</p>
              </figcaption>
            </figure>
            <RouterLink to="/nieuws#galerij" class="rounded-xl border border-slate-300 px-5 py-3 text-center text-sm font-bold text-robo-dark transition hover:border-robo-orange hover:bg-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-robo-orange sm:col-span-2 lg:col-span-1">Meer foto's en video's &rarr;</RouterLink>
          </aside>
        </div>
      </div>
    </section>

    <section class="bg-white py-16" aria-labelledby="locatie-title">
      <div class="mx-auto max-w-6xl px-6">
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-slate-50 lg:grid lg:grid-cols-2">
          <div class="p-6 md:p-8">
            <p class="mb-3 text-sm font-bold uppercase tracking-widest text-robo-orange">Hier zien we elkaar</p>
            <h2 id="locatie-title" class="scroll-mt-28 text-3xl font-black text-robo-dark">Locatie en route</h2>
            <p v-if="loading" class="mt-4 text-slate-600">Locatie laden...</p>
            <p v-else-if="editie?.location" class="mt-5 text-slate-700">
              <strong class="block text-lg text-robo-dark">{{ editieLocatieTitel(editie) }}</strong>
              <span class="mt-2 block">{{ editieLocatieSubtitel(editie) }}</span>
            </p>
            <p v-else class="mt-4 text-slate-600">{{ error ? 'Locatie-informatie kon niet worden geladen.' : 'De locatie wordt nog bekendgemaakt.' }}</p>
            <p v-if="editie?.location?.instructions" class="mt-4 whitespace-pre-line text-sm leading-relaxed text-slate-600">{{ editie.location.instructions }}</p>
            <button
              type="button"
              class="mt-6 rounded-lg bg-robo-orange px-4 py-3 text-sm font-bold text-white transition hover:bg-robo-orange-dark focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-robo-orange focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
              :disabled="!mapEmbedUrl"
              @click="openRouteInGoogleMaps"
            >Open route in Google Maps</button>
          </div>
          <iframe
            v-if="mapEmbedUrl"
            title="Locatiekaart Roboktober"
            :src="mapEmbedUrl"
            class="h-72 w-full border-t border-slate-200 lg:h-full lg:min-h-80 lg:border-l lg:border-t-0"
            loading="lazy"
          />
          <div v-else class="flex min-h-48 items-center justify-center border-t border-slate-200 p-8 text-sm text-slate-500 lg:border-l lg:border-t-0">
            {{ loading ? 'Kaart laden...' : 'De kaart verschijnt zodra de locatie beschikbaar is.' }}
          </div>
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
          :to="joinCtaPath"
          class="inline-block rounded-lg bg-robo-orange px-8 py-3 font-bold text-white transition hover:bg-robo-orange-dark focus:outline-none focus:ring-2 focus:ring-robo-orange focus:ring-offset-2 focus:ring-offset-robo-gray"
        >
          {{ joinCtaLabel }}
        </RouterLink>
      </div>
    </section>
  </main>
</template>
