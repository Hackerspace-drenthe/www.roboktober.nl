<script setup lang="ts">
// Geen API-aanroep nodig — programma is statische content tot het gepubliceerd wordt
import headerImage from '@/assets/headers/header-programma.png'

const heroStyle = {
  backgroundImage: `url(${headerImage})`,
  backgroundSize: 'cover',
  backgroundPosition: 'center',
}
</script>

<template>
  <main id="main-content">
    <!-- Hero -->
    <section class="relative overflow-hidden py-20 text-white" :style="heroStyle">
      <div class="absolute inset-0 bg-robo-dark/75" aria-hidden="true" />
      <div class="relative z-10 mx-auto max-w-3xl px-6 text-center">
        <h1 class="mb-4 text-4xl font-black md:text-5xl">Programma</h1>
        <p class="text-lg text-slate-300">
          Roboktober vindt plaats in <strong>oktober 2026</strong> bij Hackerspace Drenthe.
          Het volledige programma wordt binnenkort bekendgemaakt.
        </p>
      </div>
    </section>

    <!-- Geplande momenten -->
    <section class="bg-white py-20" aria-labelledby="programma-title">
      <div class="mx-auto max-w-3xl px-6">
        <h2 id="programma-title" class="mb-10 text-2xl font-black text-robo-dark">
          Twee momenten om op te letten
        </h2>
        <ol class="relative border-l-2 border-robo-orange/30">
          <li class="mb-10 ml-8">
            <span
              class="absolute -left-3 flex h-6 w-6 items-center justify-center rounded-full bg-robo-orange text-xs font-black text-white"
              aria-hidden="true"
            >1</span>
            <h3 class="mb-1 text-xl font-bold text-robo-dark">Kickoff-avond</h3>
            <time class="mb-2 block text-sm font-medium text-robo-orange">Begin oktober 2026</time>
            <p class="text-slate-600">
              Introductie in de wereld van combat robotics. Rondleiding door de werkplaats,
              eerste blik op de arena en kennismaking met de deelnemende teams.
              Open voor iedereen — ook kijkers zijn welkom.
            </p>
            <div class="mt-4 rounded-lg bg-slate-50 border border-slate-200 p-4">
              <h4 class="mb-1 font-bold text-robo-dark">Presentatie door Walter</h4>
              <p class="text-sm text-slate-600">
                Walter (Team Upstart) geeft een presentatie over zijn robots en legt uit wat er
                nodig is om er zelf een te bouwen — van idee tot rijdende machine. Geschikt voor
                beginners en nieuwsgierigen van alle leeftijden.
              </p>
              <div class="mt-3 rounded-md bg-robo-orange/10 px-4 py-3">
                <p class="text-sm font-medium text-robo-dark">
                  Startkitjes
                </p>
                <p class="mt-1 text-sm text-slate-600">
                  Er zijn <strong>Roboktober-kits</strong> beschikbaar met de minimaal noodzakelijke
                  onderdelen: N20-motortjes, accu, motordriver en een <strong>ESP32-C3</strong>
                  microcontroller. Of er ook mooie PCB's bij zitten is nog niet zeker — dat moeten
                  we nog regelen. Meer nodig heb je in ieder geval niet voor je eerste antweight.
                </p>
              </div>
            </div>
          </li>
          <li class="ml-8">
            <span
              class="absolute -left-3 flex h-6 w-6 items-center justify-center rounded-full bg-robo-orange text-xs font-black text-white"
              aria-hidden="true"
            >2</span>
            <h3 class="mb-1 text-xl font-bold text-robo-dark">Battle Day</h3>
            <time class="mb-2 block text-sm font-medium text-robo-orange">Eind oktober 2026</time>
            <p class="text-slate-600">
              De dag van de gevechten. Robots worden gekeurd, ingeschaald en laten het
              dan los in de arena. Publiek welkom — gratis toegang.
            </p>
          </li>
        </ol>

        <div class="mt-12 rounded-xl border border-robo-orange/30 bg-orange-50 p-6">
          <p class="text-sm text-slate-600">
            <strong class="text-robo-dark">Let op:</strong> Exacte data, tijden en locatiedetails
            worden binnenkort gepubliceerd. Volg onze
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
