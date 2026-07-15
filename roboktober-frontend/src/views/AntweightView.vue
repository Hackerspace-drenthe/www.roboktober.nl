<script setup lang="ts">
import { computed } from 'vue'
import headerTeams from '@/assets/headers/header-teams.png'
import { useAuth } from '@/composables/useAuth'

const auth = useAuth()
const joinCtaPath = computed(() => (auth.isAuthenticated.value ? '/aanmelden' : '/registreren'))
const joinCtaLabel = computed(() => (auth.isAuthenticated.value ? 'Meld je team aan' : 'Maak account aan en meld je team aan'))

const heroStyle = {
  // First image is the Gemini output; second image is fallback background.
  backgroundImage: `url(/antweight-ai/hero-combat.png), url(${headerTeams})`,
  backgroundSize: 'cover',
  backgroundPosition: 'center',
}

const aiImages = {
  workbench: {
    png: '/antweight-ai/workbench-build.png',
    svg: '/antweight-ai/workbench-build.png.svg',
  },
  styles: {
    png: '/antweight-ai/styles-collage.png',
    svg: '/antweight-ai/styles-collage.png.svg',
  },
  exploded: {
    png: '/antweight-ai/exploded-tech.png',
    svg: '/antweight-ai/exploded-tech.png.svg',
  },
  beauty: {
    png: '/antweight-ai/battle-beauty.png',
    svg: '/antweight-ai/battle-beauty.png.svg',
  },
  community: {
    png: '/antweight-ai/community-lab.png',
    svg: '/antweight-ai/community-lab.png.svg',
  },
} as const

function useSvgFallback(event: Event, fallback: string): void {
  const image = event.target as HTMLImageElement
  if (image.src.endsWith('.svg')) return
  image.src = fallback
}

const typeCards = [
  {
    titel: 'Combat antweights',
    subtitle: 'Snel, agressief en tactisch',
    beschrijving: 'Gebouwd voor de arena: rammen, duwen, flippen of spinnen. Alles draait om controle, grip en slim rijden.',
    kenmerken: ['Arena-veilig ontwerp', 'Focus op aandrijving en balans', 'Battle-ready in kleine schaal'],
    accent: 'from-rose-500/20 to-orange-400/20 border-rose-300/40',
  },
  {
    titel: 'Design & showbots',
    subtitle: 'Esthetiek en creativiteit',
    beschrijving: 'Niet alles draait om gevecht. Denk aan unieke body-shapes, thema-builds en robots met een sterke visuele impact.',
    kenmerken: ['Unieke 3D-prints', 'Persoonlijke stijl en thema', 'Perfect voor demo en presentatie'],
    accent: 'from-sky-500/20 to-cyan-400/20 border-sky-300/40',
  },
  {
    titel: 'Beginner builds',
    subtitle: 'Toegankelijk, betaalbaar, effectief',
    beschrijving: 'De ideale eerste robot. Met standaard onderdelen en een doordacht chassis bouw je snel een werkend prototype.',
    kenmerken: ['Lage kosten', 'Snelle leercurve', 'Geschikt voor alle niveaus'],
    accent: 'from-emerald-500/20 to-lime-400/20 border-emerald-300/40',
  },
  {
    titel: 'Geavanceerde builds',
    subtitle: 'Voor makers die optimaliseren',
    beschrijving: 'Met telemetrie, custom firmware en geoptimaliseerde mechanica haal je het maximale uit 150 gram.',
    kenmerken: ['Custom elektronica en code', 'Gewichtsoptimalisatie op detailniveau', 'Competitieve performance tuning'],
    accent: 'from-violet-500/20 to-fuchsia-400/20 border-violet-300/40',
  },
]

const wowItems = [
  {
    titel: 'Van concept naar werkend prototype',
    tekst: 'Begin met een chassis-ontwerp en bouw een functionele robot die reageert op jouw besturing.',
  },
  {
    titel: 'Praktische technische ervaring',
    tekst: 'Mechanica, elektronica en software komen samen. Experimenteren, testen en optimaliseren maken het leerproces effectief.',
  },
  {
    titel: 'Community en competitie',
    tekst: 'Bouw, test en verbeter samen met andere makers. Deel kennis, krijg feedback en neem deel aan events.',
  },
  {
    titel: 'Modulair en uitbreidbaar',
    tekst: 'Begin met een basis-setup en breid later uit met LEDs, geluid, aparte besturingscontroller en speciale features.',
  },
]
</script>

<template>
  <main id="main-content">
    <section class="relative overflow-hidden py-24 text-white" :style="heroStyle">
      <div class="absolute inset-0 bg-gradient-to-br from-robo-dark/90 via-robo-dark/80 to-black/70" aria-hidden="true" />
      <div class="relative z-10 mx-auto max-w-5xl px-6 text-center">
        <p class="mb-3 text-sm font-bold uppercase tracking-[0.2em] text-robo-orange">Robotwars op zakformaat</p>
        <h1 class="text-4xl font-black leading-tight md:text-6xl">Wat is een antweight robot?</h1>
        <p class="mx-auto mt-6 max-w-3xl text-lg text-slate-200 md:text-xl">
          Een antweight is een superlichte combat robot van maximaal <strong class="text-white">150 gram</strong>.
          Klein van formaat, groots in techniek, creativiteit en spektakel.
        </p>

        <div class="mx-auto mt-10 grid max-w-3xl gap-3 sm:grid-cols-3">
          <div class="rounded-xl border border-white/20 bg-white/10 p-4 backdrop-blur">
            <p class="text-xs uppercase tracking-wide text-slate-300">Gewichtsklasse</p>
            <p class="mt-1 text-2xl font-black text-white">150 g max</p>
          </div>
          <div class="rounded-xl border border-white/20 bg-white/10 p-4 backdrop-blur">
            <p class="text-xs uppercase tracking-wide text-slate-300">Instapniveau</p>
            <p class="mt-1 text-2xl font-black text-white">Beginner-vriendelijk</p>
          </div>
          <div class="rounded-xl border border-white/20 bg-white/10 p-4 backdrop-blur">
            <p class="text-xs uppercase tracking-wide text-slate-300">Toegankelijkheid</p>
            <p class="mt-1 text-2xl font-black text-white">Hoog</p>
          </div>
        </div>
      </div>
    </section>

    <section class="bg-white py-20" aria-labelledby="antweight-uitleg-title">
      <div class="mx-auto grid max-w-6xl gap-10 px-6 lg:grid-cols-2 lg:items-center">
        <div>
          <h2 id="antweight-uitleg-title" class="text-3xl font-black text-robo-dark md:text-4xl">Compact, betaalbaar en uitdagend</h2>
          <p class="mt-5 text-slate-700">
            Antweights combineren technische diepgang met praktische toegankelijkheid. Door hun compacte formaat en lage kosten
            kun je snel experimenteren met verschillende concepten: van eenvoudige wedge tot geavanceerde spinner.
          </p>
          <p class="mt-4 text-slate-700">
            Je leert direct welke principes werken: tractie, snelheid, stabiliteit, bescherming en besturingstechniek.
            Deze combinatie maakt de klasse populair bij makers van alle niveaus en achtergronden.
          </p>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 shadow-sm">
          <img
            :src="aiImages.workbench.png"
            alt="Antweight robot onderdelen en bouwmaterialen op een werkbank"
            class="h-full w-full object-cover"
            loading="lazy"
            @error="useSvgFallback($event, aiImages.workbench.svg)"
          />
        </div>
      </div>
    </section>

    <section class="bg-slate-950 py-20 text-white" aria-labelledby="soorten-title">
      <div class="mx-auto max-w-6xl px-6">
        <h2 id="soorten-title" class="text-3xl font-black md:text-4xl">Verschillende soorten antweight robots</h2>
        <p class="mt-4 max-w-3xl text-slate-300">
          Er is niet één perfecte robot. Jouw stijl bepaalt het ontwerp. Dit zijn vier populaire richtingen om mee te starten.
        </p>

        <div class="mt-10 grid gap-6 md:grid-cols-2">
          <article
            v-for="card in typeCards"
            :key="card.titel"
            class="rounded-2xl border bg-gradient-to-br p-6"
            :class="card.accent"
          >
            <p class="text-xs font-bold uppercase tracking-wide text-slate-200">{{ card.subtitle }}</p>
            <h3 class="mt-2 text-2xl font-black">{{ card.titel }}</h3>
            <p class="mt-4 text-slate-100">{{ card.beschrijving }}</p>
            <ul class="mt-4 space-y-2 text-sm text-slate-200" role="list">
              <li v-for="kenmerk in card.kenmerken" :key="kenmerk" class="flex items-start gap-2">
                <span class="mt-1 inline-block h-2 w-2 rounded-full bg-robo-orange" />
                <span>{{ kenmerk }}</span>
              </li>
            </ul>
          </article>
        </div>

        <div class="mt-10 grid gap-6 md:grid-cols-2">
          <figure class="overflow-hidden rounded-2xl border border-white/15 shadow-sm">
            <img
              :src="aiImages.styles.png"
              alt="Collage met verschillende antweight stijlen"
              class="h-full w-full object-cover"
              loading="lazy"
              @error="useSvgFallback($event, aiImages.styles.svg)"
            />
          </figure>
          <figure class="overflow-hidden rounded-2xl border border-white/15 shadow-sm">
            <img
              :src="aiImages.exploded.png"
              alt="Exploded technische visual van een antweight robot"
              class="h-full w-full object-cover"
              loading="lazy"
              @error="useSvgFallback($event, aiImages.exploded.svg)"
            />
          </figure>
        </div>
      </div>
    </section>

    <section class="bg-white py-20" aria-labelledby="wow-title">
      <div class="mx-auto max-w-6xl px-6">
        <h2 id="wow-title" class="text-3xl font-black text-robo-dark md:text-4xl">Wat je leert met antweight robotica</h2>

        <div class="mt-10 grid gap-6 md:grid-cols-2">
          <article
            v-for="item in wowItems"
            :key="item.titel"
            class="rounded-xl border border-slate-200 bg-slate-50 p-6"
          >
            <h3 class="text-xl font-black text-robo-dark">{{ item.titel }}</h3>
            <p class="mt-3 text-slate-700">{{ item.tekst }}</p>
          </article>
        </div>

        <div class="mt-12 grid gap-6 md:grid-cols-2">
          <figure class="overflow-hidden rounded-2xl border border-slate-200 shadow-sm">
            <img
              :src="aiImages.community.png"
              alt="Start van een antweight build met chassis en onderdelen"
              class="h-full w-full object-cover"
              loading="lazy"
              @error="useSvgFallback($event, aiImages.community.svg)"
            />
            <figcaption class="bg-white px-4 py-3 text-sm text-slate-600">
              Samen bouwen in de makerspace: inspiratie, hulp en snelle progressie.
            </figcaption>
          </figure>

          <figure class="overflow-hidden rounded-2xl border border-slate-200 shadow-sm">
            <img
              :src="aiImages.beauty.png"
              alt="Battle-ready antweight robot met beschermde elektronica"
              class="h-full w-full object-cover"
              loading="lazy"
              @error="useSvgFallback($event, aiImages.beauty.svg)"
            />
            <figcaption class="bg-white px-4 py-3 text-sm text-slate-600">
              Klaar voor de arena: compact, veilig en snel.
            </figcaption>
          </figure>
        </div>
      </div>
    </section>

    <section class="relative overflow-hidden bg-robo-gray py-20 text-white" aria-labelledby="cta-title">
      <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(249,115,22,0.25),transparent_40%),radial-gradient(circle_at_80%_80%,rgba(251,191,36,0.2),transparent_45%)]" aria-hidden="true" />
      <div class="relative z-10 mx-auto max-w-4xl px-6 text-center">
        <h2 id="cta-title" class="text-3xl font-black md:text-4xl">Begin met je eigen build</h2>
        <p class="mx-auto mt-4 max-w-2xl text-slate-200">
          Kies je aanpak, verzamel de onderdelen en start met bouwen. Binnen enkele dagen test je jouw eigen antweight.
        </p>

        <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
          <RouterLink
            to="/bouwen/bouwgids"
            class="rounded-lg bg-robo-orange px-6 py-3 font-bold text-white transition hover:bg-robo-orange-dark focus:outline-none focus:ring-2 focus:ring-robo-orange focus:ring-offset-2 focus:ring-offset-robo-gray"
          >
            Start met bouwen
          </RouterLink>
          <RouterLink
            :to="joinCtaPath"
            class="rounded-lg border border-white/30 bg-white/10 px-6 py-3 font-bold text-white transition hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-white/60"
          >
            {{ joinCtaLabel }}
          </RouterLink>
        </div>
      </div>
    </section>
  </main>
</template>
