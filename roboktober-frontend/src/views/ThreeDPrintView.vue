<script setup lang="ts">
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import headerImage from '@/assets/headers/header-bouwen.png'
import chassisRender from '@/assets/3dprint/chassis-render.png'
import chassisPcbDekselRender from '@/assets/3dprint/chassis-pcb-deksel-render.png'
import pcbFrontMetComponenten from '@/assets/pcb/pcb-v4-front-met-componenten.png'

const route = useRoute()
const actieveTab = computed<'bouwgids' | '3dprint' | 'pcb' | 'links'>(() => {
  if (route.name === 'bouwen-3dprint') return '3dprint'
  if (route.name === 'bouwen-pcb') return 'pcb'
  if (route.name === 'bouwen-links') return 'links'
  return 'bouwgids'
})

const heroStyle = {
  backgroundImage: `url(${headerImage})`,
  backgroundSize: 'cover',
  backgroundPosition: 'center',
}

const printSettings = [
  { label: 'Materiaal', value: 'PLA of PETG' },
  { label: 'Nozzle', value: '0.4 mm' },
  { label: 'Laaghoogte', value: '0.20 mm (0.16 mm voor mooier topvlak)' },
  { label: 'Perimeters', value: '3 tot 4' },
  { label: 'Top/Bottom lagen', value: '5 tot 7' },
  { label: 'Infill', value: '20% gyroid of grid' },
  { label: 'Support', value: 'Alleen waar nodig, vooral bij motor-openingen' },
  { label: 'Schaal', value: '100% (nooit fit-to-page of autoscale)' },
]

const montageChecks = [
  'N20 motoren passen vlak in de motorhouders zonder wringen.',
  'Wielen draaien vrij zonder de wand of bodemplaat te raken.',
  'LiPo ligt vast met foam tape of klittenband en kan niet schuiven.',
  'Schakelaar blijft bereikbaar vanaf buitenzijde.',
  'M2 montagegaten lijnen op met de v4 print/deksel.',
]
</script>

<template>
  <main id="main-content">
    <section class="relative overflow-hidden py-20 text-white" :style="heroStyle">
      <div class="absolute inset-0 bg-robo-dark/75" aria-hidden="true" />
      <div class="relative z-10 mx-auto max-w-4xl px-6 text-center">
        <p class="mb-3 text-sm font-bold uppercase tracking-widest text-robo-orange">Bouwplatform</p>
        <h1 class="mb-4 text-4xl font-black md:text-5xl">3D print chassis</h1>
        <p class="mx-auto max-w-2xl text-lg text-slate-300">
          Print je chassis, monteer motoren en LiPo, en gebruik de v4 PCB-footprint als deksel.
          Deze pagina is de snelle route van STL naar een rijklare basis.
        </p>

        <div class="mx-auto mt-8 inline-flex rounded-xl border border-white/15 bg-robo-dark/70 p-1" role="tablist" aria-label="Bouwen tabs">
          <RouterLink
            to="/bouwen/bouwgids"
            role="tab"
            :aria-selected="actieveTab === 'bouwgids'"
            class="rounded-lg px-5 py-2 text-sm font-semibold transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-robo-orange"
            :class="actieveTab === 'bouwgids' ? 'bg-white text-robo-dark shadow-sm' : 'text-slate-200 hover:bg-white/10'"
          >
            Bouwgids
          </RouterLink>
          <RouterLink
            to="/bouwen/3dprint"
            role="tab"
            :aria-selected="actieveTab === '3dprint'"
            class="rounded-lg px-5 py-2 text-sm font-semibold transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-robo-orange"
            :class="actieveTab === '3dprint' ? 'bg-white text-robo-dark shadow-sm' : 'text-slate-200 hover:bg-white/10'"
          >
            3D print
          </RouterLink>
          <RouterLink
            to="/bouwen/pcb"
            role="tab"
            :aria-selected="actieveTab === 'pcb'"
            class="rounded-lg px-5 py-2 text-sm font-semibold transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-robo-orange"
            :class="actieveTab === 'pcb' ? 'bg-white text-robo-dark shadow-sm' : 'text-slate-200 hover:bg-white/10'"
          >
            PCB
          </RouterLink>
          <RouterLink
            to="/bouwen/links"
            role="tab"
            :aria-selected="actieveTab === 'links'"
            class="rounded-lg px-5 py-2 text-sm font-semibold transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-robo-orange"
            :class="actieveTab === 'links' ? 'bg-white text-robo-dark shadow-sm' : 'text-slate-200 hover:bg-white/10'"
          >
            Build Hub
          </RouterLink>
        </div>
      </div>
    </section>

    <section class="bg-white py-20" aria-labelledby="print-assets-title">
      <div class="mx-auto max-w-5xl px-6">
        <div class="mb-10 space-y-4">
          <h2 id="print-assets-title" class="text-3xl font-black text-robo-dark">STL en deksel-concept</h2>
          <p class="max-w-3xl text-slate-600">
            Het chassis komt uit STL. De deksel volgt de footprint van de v4 print:
            dezelfde buitenmaat en dezelfde montagegaten (onder voorbehoud van correcte print-schaal en toleranties).
          </p>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
          <figure class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
            <img
              :src="chassisRender"
              alt="Render van het 3D geprinte chassis"
              class="w-full object-cover"
              loading="lazy"
            />
            <figcaption class="border-t border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600">
              Chassis STL
            </figcaption>
          </figure>

          <figure class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
            <img
              :src="chassisPcbDekselRender"
              alt="Render van chassis met deksel op basis van de v4 PCB top"
              class="w-full object-cover"
              loading="lazy"
            />
            <figcaption class="border-t border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600">
              Chassis met KiCad v4 deksel
            </figcaption>
          </figure>
        </div>

        <figure class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
          <img
            :src="pcbFrontMetComponenten"
            alt="KiCad render van de v4 PCB met componenten"
            class="w-full object-contain"
            loading="lazy"
          />
          <figcaption class="border-t border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600">
            Referentie: v4 PCB top met componenten (deksel-visual)
          </figcaption>
        </figure>
      </div>
    </section>

    <section class="bg-slate-50 py-16" aria-labelledby="print-settings-title">
      <div class="mx-auto max-w-5xl px-6">
        <h2 id="print-settings-title" class="mb-6 text-2xl font-black text-robo-dark">Aanbevolen printinstellingen</h2>

        <div class="mb-6 rounded-xl border border-robo-orange/30 bg-robo-orange/10 p-4 text-sm text-slate-700">
          Vragen over slicer instellingen of passing? Stel ze in het Roboktober Telegram kanaal:
          <a
            href="https://t.me/+HL-bBBahRJJlMGQ0"
            target="_blank"
            rel="noopener noreferrer"
            class="ml-1 font-bold text-robo-orange underline decoration-robo-orange/60 underline-offset-4 hover:text-robo-orange-dark"
          >
            Open Telegram
            <span class="sr-only"> (opent in nieuw venster)</span>
          </a>
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
          <table class="min-w-full text-sm">
            <thead class="bg-slate-100 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
              <tr>
                <th scope="col" class="px-4 py-3">Parameter</th>
                <th scope="col" class="px-4 py-3">Waarde</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="item in printSettings" :key="item.label" class="hover:bg-slate-50">
                <td class="px-4 py-3 font-semibold text-robo-dark">{{ item.label }}</td>
                <td class="px-4 py-3 text-slate-700">{{ item.value }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>

    <section class="bg-white py-16" aria-labelledby="montage-check-title">
      <div class="mx-auto max-w-5xl px-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
          <h2 id="montage-check-title" class="mb-4 text-2xl font-black text-robo-dark">Snelle montage-checklist</h2>
          <ul class="space-y-3 text-slate-700">
            <li v-for="check in montageChecks" :key="check" class="flex items-start gap-3">
              <span class="mt-1 inline-block h-2.5 w-2.5 rounded-full bg-robo-orange" aria-hidden="true" />
              <span>{{ check }}</span>
            </li>
          </ul>

          <div class="mt-6 rounded-xl bg-robo-orange/10 p-4 text-sm text-slate-700">
            Als de gaten net niet uitlijnen: controleer eerst slicer scale (moet 100% zijn), daarna
            printkrimp en gatdiameter-compensatie. Pas pas daarna het model aan.
          </div>

          <div class="mt-6 flex flex-wrap gap-3">
            <RouterLink
              to="/bouwen/pcb"
              class="rounded-lg bg-robo-orange px-4 py-2.5 text-sm font-bold text-white transition hover:bg-robo-orange-dark"
            >
              Verder met PCB opbouw
            </RouterLink>
            <RouterLink
              to="/bouwen/links"
              class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
            >
              Onderdelen en tools
            </RouterLink>
          </div>
        </div>
      </div>
    </section>
  </main>
</template>
