<script setup lang="ts">
/**
 * Home page — visueel-first landing page voor Roboktober.
 *
 * Doelgroep: middelbare school (12-18 jaar), B1 taalgebruik
 * @see PLAN.md §6.1 — Home page design
 * @see PLAN.md §4   — doelgroepen
 */
import { onMounted, onUnmounted, ref } from 'vue'
import { getPosts } from '@/api'
import type { Post } from '@/types/api'
import { applySeoMeta, removeJsonLd, upsertJsonLd } from '@/utils/seo'
import headerImage from '@/assets/headers/header-home.png'
import storyCommunityImage from '@/assets/headers/header-aanmelden.png'
import storyTechImage from '@/assets/headers/header-build-hub.png'
import storyArenaImage from '@/assets/headers/header-programma.png'

const posts = ref<Post[]>([])
const ladenPosts = ref(true)

const heroStyle = {
  backgroundImage: `url(${headerImage})`,
  backgroundSize: 'cover',
  backgroundPosition: 'center',
}

interface VerhaalInfographic {
  titel: string
  intro: string
  punten: string[]
  afbeelding: string
  to: string
  knop: string
}

const verhaalInfographics: VerhaalInfographic[] = [
  {
    titel: 'Samen bouwen met vrienden en familie',
    intro: 'Bij Hackerspace Drenthe bouw je laagdrempelig je eerste robot, samen met je gezin, vrienden of klasgenoten.',
    punten: [
      'Beginner-vriendelijk en stap voor stap uitgelegd.',
      'Werkplaats met tools, begeleiding en ruimte om te testen.',
      'Leren en plezier gaan hier altijd samen.',
    ],
    afbeelding: storyCommunityImage,
    to: '/aanmelden',
    knop: 'Sluit je aan',
  },
  {
    titel: 'Educatief: elektronica en software',
    intro: 'Van motoren en batterijen tot code en besturing: je leert hoe robots echt werken, zonder ingewikkelde instap.',
    punten: [
      'Praktisch leren met elektronica, gereedschap en veilige opstellingen.',
      'Software en besturing via duidelijke voorbeelden en Wallieonline.',
      'Perfect voor nieuwsgierige makers in Drenthe.',
    ],
    afbeelding: storyTechImage,
    to: '/bouwen/links',
    knop: 'Bekijk de Build Hub',
  },
  {
    titel: 'Van werkplaats naar robotwars arena',
    intro: 'Je bouwt eerst in de werkplaats en beleeft daarna de spanning in de arena tijdens Roboktober antweight robotwars.',
    punten: [
      'Workshop, assembly en teststation in een logische flow.',
      'Veilige en spectaculaire arena-gevechten met publiek.',
      'Community van Hackerspace Drenthe en Wallieonline.',
    ],
    afbeelding: storyArenaImage,
    to: '/programma',
    knop: 'Bekijk het programma',
  },
]

// Countdown naar kickoff: begin oktober 2026 (1 oktober 09:00)
const KICKOFF = new Date('2026-10-01T09:00:00')

interface Countdown {
  dagen: number
  uren: number
  minuten: number
  seconden: number
  verstreken: boolean
}

function berekenCountdown(): Countdown {
  const nu = Date.now()
  const diff = KICKOFF.getTime() - nu
  if (diff <= 0) return { dagen: 0, uren: 0, minuten: 0, seconden: 0, verstreken: true }
  const dagen = Math.floor(diff / 86400000)
  const uren = Math.floor((diff % 86400000) / 3600000)
  const minuten = Math.floor((diff % 3600000) / 60000)
  const seconden = Math.floor((diff % 60000) / 1000)
  return { dagen, uren, minuten, seconden, verstreken: false }
}

const countdown = ref<Countdown>(berekenCountdown())
let timer: ReturnType<typeof setInterval> | null = null

function updateHomeStructuredData(): void {
  upsertJsonLd('home-core', {
    '@context': 'https://schema.org',
    '@graph': [
      {
        '@type': 'Organization',
        '@id': 'https://www.roboktober.nl/#organization',
        name: 'Roboktober',
        url: 'https://www.roboktober.nl/',
        parentOrganization: {
          '@type': 'Organization',
          name: 'Hackerspace Drenthe',
        },
      },
      {
        '@type': 'WebSite',
        '@id': 'https://www.roboktober.nl/#website',
        url: 'https://www.roboktober.nl/',
        name: 'Roboktober',
        publisher: {
          '@id': 'https://www.roboktober.nl/#organization',
        },
      },
      {
        '@type': 'Event',
        '@id': 'https://www.roboktober.nl/#event',
        name: 'Roboktober 2026',
        eventAttendanceMode: 'https://schema.org/OfflineEventAttendanceMode',
        eventStatus: 'https://schema.org/EventScheduled',
        startDate: '2026-10-01T09:00:00+02:00',
        endDate: '2026-10-31T21:00:00+01:00',
        organizer: {
          '@id': 'https://www.roboktober.nl/#organization',
        },
        location: {
          '@type': 'Place',
          name: 'Hackerspace Drenthe',
          address: {
            '@type': 'PostalAddress',
            addressRegion: 'Drenthe',
            addressCountry: 'NL',
          },
        },
      },
    ],
  })
}

onMounted(async () => {
  applySeoMeta({
    title: 'Roboktober — Combat robots bij Hackerspace Drenthe',
    description: 'Bouw je eigen gevechtsrobot bij Roboktober in Hackerspace Drenthe. Antweight robotwars, workshops en competitie.',
    canonicalPath: '/',
  })
  updateHomeStructuredData()

  timer = setInterval(() => { countdown.value = berekenCountdown() }, 1000)
  try {
    const response = await getPosts({ per_page: 3 })
    posts.value = response.data
  } catch {
    // Silent fail — home page werkt ook zonder nieuws
  } finally {
    ladenPosts.value = false
  }
})

onUnmounted(() => {
  if (timer) {
    clearInterval(timer)
  }

  removeJsonLd('home-core')
})
</script>

<template>
  <!-- Hero sectie -->
  <section
    class="relative flex min-h-screen items-center justify-center overflow-hidden text-white"
    :style="heroStyle"
    aria-labelledby="hero-title"
  >
    <div class="absolute inset-0 bg-robo-dark/75" aria-hidden="true" />
    <div class="relative z-10 mx-auto max-w-4xl px-6 text-center">
      <p class="mb-4 text-sm font-semibold uppercase tracking-widest text-robo-orange">
        Hackerspace Drenthe · Oktober 2026
      </p>
      <h1
        id="hero-title"
        class="mb-6 text-5xl font-black tracking-tight md:text-7xl"
      >
        Robo<span class="text-robo-orange">ktober</span>
      </h1>
      <p class="mx-auto mb-10 max-w-2xl text-xl text-slate-300">
        Bouw je eigen gevechtsrobot en laat hem strijden!
        Antweight klasse — maximaal 150 gram. Voor beginners én gevorderden.
      </p>

      <!-- Countdown -->
      <div
        v-if="!countdown.verstreken"
        class="mb-10 flex justify-center gap-4"
        aria-label="Aftelling tot kickoff"
        role="timer"
      >
        <div v-for="(val, label) in { Dagen: countdown.dagen, Uren: countdown.uren, Minuten: countdown.minuten, Seconden: countdown.seconden }" :key="label"
          class="flex w-20 flex-col items-center rounded-xl border border-white/10 bg-white/5 py-3"
        >
          <span class="text-3xl font-black tabular-nums text-robo-orange">{{ String(val).padStart(2, '0') }}</span>
          <span class="mt-1 text-xs uppercase tracking-widest text-slate-400">{{ label }}</span>
        </div>
      </div>
      <p v-else class="mb-10 text-robo-orange font-bold text-lg">De kickoff is begonnen! 🤖</p>

      <div class="flex flex-col gap-4 sm:flex-row sm:justify-center">
        <RouterLink
          to="/aanmelden"
          class="rounded-lg bg-robo-orange px-8 py-4 text-lg font-bold text-white transition hover:bg-robo-orange-dark focus:outline-none focus:ring-4 focus:ring-robo-orange/50"
        >
          Meld je team aan
        </RouterLink>
        <RouterLink
          to="/programma"
          class="rounded-lg border border-white/20 px-8 py-4 text-lg font-bold text-white transition hover:border-white/60 focus:outline-none focus:ring-4 focus:ring-white/20"
        >
          Bekijk het programma
        </RouterLink>
      </div>
    </div>
  </section>

  <!-- Verhaal infographics -->
  <section
    class="bg-white py-24"
    aria-labelledby="verhaal-title"
  >
    <div class="mx-auto max-w-6xl px-6">
      <p class="mb-3 text-center text-sm font-semibold uppercase tracking-widest text-robo-orange">
        Nieuw op de voorpagina
      </p>
      <h2
        id="verhaal-title"
        class="mb-4 text-center text-3xl font-black text-robo-dark md:text-4xl"
      >
        Het hele Roboktober-verhaal in 3 infographics
      </h2>
      <p class="mx-auto mb-12 max-w-3xl text-center text-lg text-slate-600">
        Samen bouwen, leren en plezier maken. Van eerste onderdelen en software tot robotwars in de arena van Hackerspace Drenthe.
      </p>

      <div class="grid gap-8 lg:grid-cols-3">
        <article
          v-for="item in verhaalInfographics"
          :key="item.titel"
          class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-xl"
        >
          <div class="relative h-56 overflow-hidden">
            <img
              :src="item.afbeelding"
              :alt="`Infographic ${item.titel}`"
              class="h-full w-full object-cover"
              loading="lazy"
            />
            <div class="absolute inset-0 bg-robo-dark/40" aria-hidden="true" />
            <p class="absolute left-4 top-4 rounded-full bg-robo-orange/95 px-3 py-1 text-xs font-bold uppercase tracking-wider text-white">
              Infographic
            </p>
          </div>

          <div class="p-6">
            <h3 class="mb-3 text-2xl font-black text-robo-dark">{{ item.titel }}</h3>
            <p class="mb-5 text-slate-600">{{ item.intro }}</p>

            <ul class="mb-6 space-y-2 text-sm text-slate-600">
              <li
                v-for="punt in item.punten"
                :key="punt"
                class="flex items-start gap-2"
              >
                <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-robo-orange" aria-hidden="true" />
                <span>{{ punt }}</span>
              </li>
            </ul>

            <RouterLink
              :to="item.to"
              class="inline-flex rounded-lg bg-robo-dark px-4 py-2 text-sm font-bold text-white transition hover:bg-robo-orange"
            >
              {{ item.knop }} →
            </RouterLink>
          </div>
        </article>
      </div>
    </div>
  </section>

  <!-- Gewichtsklassen -->
  <section
    class="bg-robo-gray py-24 text-white"
    aria-labelledby="klassen-title"
  >
    <div class="mx-auto max-w-5xl px-6">
      <h2
        id="klassen-title"
        class="mb-12 text-center text-3xl font-black"
      >
        Gewichtsklasse voor Roboktober 2026
      </h2>
      <div class="mx-auto max-w-lg">
        <article class="rounded-xl border border-robo-orange/40 bg-white/5 p-8 text-center">
          <span class="mb-4 inline-block rounded-full bg-robo-orange/20 px-4 py-1 text-sm font-bold uppercase tracking-widest text-robo-orange">Enige klasse dit jaar</span>
          <h3 class="mb-2 text-2xl font-black">Antweight</h3>
          <p class="mb-4 text-5xl font-black text-robo-orange">≤ 150 g</p>
          <p class="text-slate-300">
            We starten klein en veilig. Antweights zijn goedkoop te bouwen, makkelijker te transporteren en perfect voor een eerste editie. Klein formaat, groot spektakel!
          </p>
          <p class="mt-4 text-slate-300">
            Een basisrobot hoeft niet meer te kosten dan <strong class="text-white">€15 à €20</strong>. Wil je verder gaan met leds, geluid of een beter wapen? Dat kan altijd nog.
          </p>
          <p class="mt-4 text-slate-300">
            Leuk om samen met vrienden of kinderen te doen. Leer omgaan met elektronica, software en 3D-printen — op een educatieve maar vooral <em>leuke</em> manier.
          </p>
          <p class="mt-4 text-sm text-slate-400">
            Zwaardere klassen worden overwogen voor toekomstige edities.
          </p>
        </article>
      </div>
    </div>
  </section>

  <!-- Nieuws -->
  <section
    class="bg-white py-24"
    aria-labelledby="nieuws-title"
  >
    <div class="mx-auto max-w-5xl px-6">
      <div class="mb-12 flex items-center justify-between">
        <h2
          id="nieuws-title"
          class="text-3xl font-black"
        >
          Laatste nieuws
        </h2>
        <RouterLink
          to="/nieuws"
          class="font-semibold text-robo-orange hover:underline"
        >
          Alle berichten →
        </RouterLink>
      </div>

      <div
        v-if="ladenPosts"
        class="grid gap-6 md:grid-cols-3"
        aria-busy="true"
        aria-label="Nieuws wordt geladen"
      >
        <div
          v-for="n in 3"
          :key="n"
          class="h-64 animate-pulse rounded-xl bg-slate-200"
        />
      </div>

      <div
        v-else-if="posts.length"
        class="grid gap-6 md:grid-cols-3"
      >
        <article
          v-for="post in posts"
          :key="post.id"
          class="overflow-hidden rounded-xl border border-slate-200 transition hover:shadow-lg"
        >
          <img
            v-if="post.featured"
            :src="post.featured.url"
            :alt="post.featured.alt_tekst ?? post.titel"
            class="aspect-video w-full object-cover"
            loading="lazy"
          />
          <div class="p-5">
            <p
              v-if="post.published_at"
              class="mb-2 text-sm text-slate-500"
            >
              {{ new Date(post.published_at).toLocaleDateString('nl-NL') }}
            </p>
            <h3 class="mb-2 text-lg font-bold">{{ post.titel }}</h3>
            <p
              v-if="post.excerpt"
              class="mb-4 line-clamp-3 text-slate-600"
            >
              {{ post.excerpt }}
            </p>
            <RouterLink
              :to="`/nieuws/${post.slug}`"
              class="font-semibold text-robo-orange hover:underline"
            >
              Lees meer →
            </RouterLink>
          </div>
        </article>
      </div>

      <p
        v-else
        class="text-center text-slate-500"
      >
        Nog geen nieuwsberichten.
      </p>
    </div>
  </section>
</template>
