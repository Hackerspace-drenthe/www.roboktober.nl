<script setup lang="ts">
// Statische pagina — alle info komt van wallieonline.nl en Dutch Robot Games
import headerImage from '@/assets/headers/header-walter.png'

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
      <div class="relative z-10 mx-auto max-w-3xl px-6">
        <p class="mb-3 text-sm font-bold uppercase tracking-widest text-robo-orange">Uw gastheer</p>
        <h1 class="mb-4 text-4xl font-black md:text-5xl">Walter — Wallieonline</h1>
        <p class="max-w-2xl text-lg text-slate-300">
          Roboktober bestaat dankzij Walter. Als team captain van Team Upstart en gevechtrobot-veteraan
          deelt hij zijn kennis, netwerk en passie met iedereen bij Hackerspace Drenthe.
        </p>
      </div>
    </section>

    <!-- Intro -->
    <section class="bg-white py-16" aria-labelledby="walter-intro-title">
      <div class="mx-auto max-w-3xl px-6">
        <h2 id="walter-intro-title" class="mb-6 text-2xl font-black text-robo-dark">Wie is Walter?</h2>
        <div class="prose prose-slate max-w-none prose-a:text-robo-orange prose-headings:font-black">
          <p>
            Tijdens een lunchpauze in 2013 werden de eerste plannen voor een gevechtsrobot gemaakt.
            Wat begon als een idee groeide uit tot <strong>Team Upstart</strong> —
            een team dat actief deelneemt aan toernooien in Nederland, Duitsland en België.
          </p>
          <p>
            Walter, alias <strong>Wallieonline</strong>, heeft een brede interesse voor techniek.
            Zoals hij het zelf omschrijft:
          </p>
          <blockquote>
            "In het bouwen van een robot komen alle aspecten van techniek samen: werktuigbouw,
            elektrotechniek, computer-aided design, software en materiaalkennis. Als alle componenten
            samenwerken en het werkt zoals je bedacht had… Dat gevoel… Onbeschrijfelijk."
          </blockquote>
          <p>
            Naast wedstrijden zet Team Upstart zich in om leerlingen te enthousiasmeren voor een
            technische opleiding en een technisch beroep — door het verzorgen van mini robot games
            op evenementen in de regio.
          </p>
        </div>
      </div>
    </section>

    <!-- Robots -->
    <section class="bg-robo-gray py-16 text-white" aria-labelledby="robots-title">
      <div class="mx-auto max-w-3xl px-6">
        <h2 id="robots-title" class="mb-8 text-2xl font-black">Robots van Team Upstart</h2>
        <ul class="space-y-6" role="list">
          <li class="rounded-xl border border-white/10 bg-white/5 p-6">
            <div class="mb-2 flex flex-wrap items-center gap-3">
              <h3 class="text-xl font-bold">Cookiemonster</h3>
              <span class="rounded-full bg-robo-orange/20 px-3 py-0.5 text-xs font-bold text-robo-orange">Featherweight</span>
            </div>
            <p class="text-slate-300">
              De vlaggenschip-robot van Team Upstart. Gebouwd met liefde, toewijding en vele uren
              hard werken. De naam past bij de sfeer: humor, koffie en koekjes.
              Cookiemonster is te zien op toernooien door heel Europa.
            </p>
          </li>
          <li class="rounded-xl border border-white/10 bg-white/5 p-6">
            <div class="mb-2 flex flex-wrap items-center gap-3">
              <h3 class="text-xl font-bold">TLC</h3>
              <span class="rounded-full bg-robo-orange/20 px-3 py-0.5 text-xs font-bold text-robo-orange">Beetleweight</span>
            </div>
            <p class="text-slate-300">
              TLC staat voor <em>Tough Little Cookie</em> — om toch in het koekjesuniversum te blijven.
              Na een gevecht heeft een robot soms wat TLC (tender loving care) nodig.
            </p>
          </li>
          <li class="rounded-xl border border-white/10 bg-white/5 p-6">
            <div class="mb-2 flex flex-wrap items-center gap-3">
              <h3 class="text-xl font-bold">Kecil</h3>
              <span class="rounded-full bg-robo-orange/20 px-3 py-0.5 text-xs font-bold text-robo-orange">Antweight</span>
            </div>
            <p class="text-slate-300">
              Kecil is de antweight van Team Upstart — maximaal 150 gram, net als de robots die
              tijdens Roboktober 2026 strijden. Klein maar fijn.
            </p>
          </li>
        </ul>
      </div>
    </section>

    <!-- Roboktober connectie -->
    <section class="bg-white py-16" aria-labelledby="roboktober-connectie-title">
      <div class="mx-auto max-w-3xl px-6">
        <h2 id="roboktober-connectie-title" class="mb-6 text-2xl font-black text-robo-dark">
          De man achter Roboktober
        </h2>
        <p class="mb-4 text-slate-600">
          Walter brengt zijn jarenlange ervaring mee naar Hackerspace Drenthe. Hij begeleidt
          nieuwe bouwers, deelt zijn kennis over constructie en elektronica, en zorgt dat de
          arena en veiligheidskeuring op orde zijn. Roboktober is zijn initiatief —
          de hackerspace bouwt graag mee.
        </p>
        <p class="text-slate-600">
          Wil je meer weten over combat robotics of hulp bij je eerste bouw?
          Walter is de aangewezen persoon. Loop langs op een open avond bij Hackerspace Drenthe
          of neem contact op via zijn website.
        </p>
        <div class="mt-6">
          <RouterLink
            to="/bouwen"
            class="inline-block rounded-lg bg-robo-orange px-6 py-3 font-bold text-white transition hover:bg-robo-orange-dark focus:outline-none focus:ring-2 focus:ring-robo-orange focus:ring-offset-2"
          >
            Bekijk de bouwgids &amp; onderdelen&shy;lijst →
          </RouterLink>
        </div>
      </div>
    </section>

    <!-- Links -->
    <section class="bg-slate-50 py-16" aria-labelledby="links-title">
      <div class="mx-auto max-w-3xl px-6">
        <h2 id="links-title" class="mb-8 text-2xl font-black text-robo-dark">Walters plekken op het web</h2>
        <ul class="space-y-4" role="list">
          <li>
            <a
              href="https://www.wallieonline.nl/"
              target="_blank"
              rel="noopener noreferrer"
              class="font-semibold text-robo-orange hover:underline focus:outline-none focus:ring-2 focus:ring-robo-orange"
            >
              wallieonline.nl <span class="sr-only">(opent in nieuw venster)</span>
            </a>
            <p class="text-sm text-slate-600">Officiële website — projecten, blogs en robot-updates.</p>
          </li>
          <li>
            <a
              href="https://www.youtube.com/@wallieonline"
              target="_blank"
              rel="noopener noreferrer"
              class="font-semibold text-robo-orange hover:underline focus:outline-none focus:ring-2 focus:ring-robo-orange"
            >
              YouTube: @wallieonline <span class="sr-only">(opent in nieuw venster)</span>
            </a>
            <p class="text-sm text-slate-600">Video's van gevechten en bouw-sessies.</p>
          </li>
          <li>
            <a
              href="https://www.facebook.com/upstart.nl/"
              target="_blank"
              rel="noopener noreferrer"
              class="font-semibold text-robo-orange hover:underline focus:outline-none focus:ring-2 focus:ring-robo-orange"
            >
              Facebook: Team Upstart <span class="sr-only">(opent in nieuw venster)</span>
            </a>
            <p class="text-sm text-slate-600">Nieuws en updates van het team.</p>
          </li>
          <li>
            <a
              href="https://dutchrobotgames.nl/team-upstart-cookiemonster-vol-passie-voor-techniek/"
              target="_blank"
              rel="noopener noreferrer"
              class="font-semibold text-robo-orange hover:underline focus:outline-none focus:ring-2 focus:ring-robo-orange"
            >
              Dutch Robot Games — Team Upstart profiel <span class="sr-only">(opent in nieuw venster)</span>
            </a>
            <p class="text-sm text-slate-600">Uitgebreid teamportret op de Dutch Robot Games website.</p>
          </li>
        </ul>
      </div>
    </section>
  </main>
</template>
