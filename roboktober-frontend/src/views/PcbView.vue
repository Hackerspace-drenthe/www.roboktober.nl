<script setup lang="ts">
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import headerImage from '@/assets/headers/header-pcb.png'
import pcbFrontMetComponenten from '@/assets/pcb/pcb-v4-front-met-componenten.png'
import pcbAchterMetComponenten from '@/assets/pcb/pcb-v4-achter-met-componenten.png'
import pcbFrontKaal from '@/assets/pcb/pcb-v4-front-kaal.png'
import pcbAchterKaal from '@/assets/pcb/pcb-v4-achter-kaal.png'
import sponsorPcbwayImage from '@/assets/sponsors/pcbway-printplaat.jpg'

const route = useRoute()
const actieveTab = computed<'bouwgids' | '3dprint' | 'pcb' | 'links'>(() => {
  if (route.name === 'bouwen-links') return 'links'
  if (route.name === 'bouwen-3dprint') return '3dprint'
  if (route.name === 'bouwen-pcb') return 'pcb'
  return 'bouwgids'
})

const heroStyle = {
  backgroundImage: `url(${headerImage})`,
  backgroundSize: 'cover',
  backgroundPosition: 'center',
}
</script>

<template>
  <main id="main-content">
    <section class="relative overflow-hidden py-20 text-white" :style="heroStyle">
      <div class="absolute inset-0 bg-robo-dark/75" aria-hidden="true" />
      <div class="relative z-10 mx-auto max-w-4xl px-6 text-center">
        <p class="mb-3 text-sm font-bold uppercase tracking-widest text-robo-orange">PCB</p>
        <h1 class="mb-4 text-4xl font-black md:text-5xl">PCB v4 / print-architectuur</h1>
        <p class="mx-auto max-w-2xl text-lg text-slate-300">
          De print is de bron van waarheid: dit is de echte controllerprint van je robot, stap voor stap uitgelegd. Ook als je nog nooit gesoldeerd hebt.
        </p>

        <div class="mx-auto mt-5 max-w-2xl rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm text-slate-200">
          <p class="font-semibold text-white">Printplaat gesponsord door PCBWay.</p>
          <img
            :src="sponsorPcbwayImage"
            alt="PCBWay sponsorbanner"
            class="mt-3 h-auto w-full max-w-xs rounded-md border border-white/20 bg-white p-2"
            loading="lazy"
          />
        </div>

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

    <section class="bg-white py-20" aria-labelledby="pcb-overview-title">
      <div class="mx-auto max-w-5xl px-6">
        <div class="mb-10 space-y-4">
          <h2 id="pcb-overview-title" class="text-3xl font-black text-robo-dark">Wat zit er op de print?</h2>
          <p class="max-w-3xl text-slate-600">
            De PCB is niet zomaar een idee op papier; het is de bron van waarheid. Als iets in een schema of document afwijkt van de print, dan geldt de print als de juiste referentie: de echte print heeft altijd gelijk.
          </p>
        </div>

        <div class="mb-10 rounded-2xl border border-slate-200 bg-white p-6">
          <h3 class="mb-3 text-xl font-black text-robo-dark">Even snel: wat betekenen deze woorden?</h3>
          <dl class="grid gap-4 sm:grid-cols-2">
            <div>
              <dt class="font-bold text-robo-dark">PCB</dt>
              <dd class="text-slate-700">De groene print zelf: een plaatje met kopersporen erin, waar je onderdelen op solderen kan. PCB staat voor <em>printed circuit board</em>.</dd>
            </div>
            <div>
              <dt class="font-bold text-robo-dark">GPIO</dt>
              <dd class="text-slate-700">Een aansluitpin van de ESP32 die je zelf mag gebruiken, bijvoorbeeld om een motor aan te sturen.</dd>
            </div>
            <div>
              <dt class="font-bold text-robo-dark">Driver (DRV8833)</dt>
              <dd class="text-slate-700">Een klein chipje dat als tussenpersoon werkt: de ESP32 geeft een klein seintje, en de driver zet dat om in genoeg stroom om een motor echt te laten draaien.</dd>
            </div>
            <div>
              <dt class="font-bold text-robo-dark">Jumper</dt>
              <dd class="text-slate-700">Een klein steek-verbindinkje (jumpercap) waarmee je met de hand kiest welke twee pinnetjes met elkaar verbonden worden — hier gebruikt om 1S of 2S te kiezen.</dd>
            </div>
          </dl>
        </div>

        <div class="grid gap-8 md:grid-cols-2">
          <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6">
            <h3 class="mb-3 text-xl font-black text-robo-dark">Hoofdcomponenten</h3>
            <ul class="space-y-2 text-slate-700">
              <li>• <strong>ESP32-C3 SuperMini</strong> — het "brein": een klein computertje dat de robot bestuurt</li>
              <li>• <strong>DRV8833 motor driver</strong> — geeft de motoren genoeg stroom om te draaien</li>
              <li>• <strong>batterij- en power-rails</strong> — de "stroomdraden" van de print</li>
              <li>• <strong>motorconnectors links/rechts</strong> — hier sluit je de twee motoren op aan</li>
              <li>• <strong>jumper voor 1S/2S voedingskeuze</strong> — kiest hoeveel accu-spanning je gebruikt</li>
              <li>• <strong>uitbreidingsheaders voor accessoires</strong> — extra pinnen voor later, bijvoorbeeld sensoren</li>
            </ul>
          </div>

          <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6">
            <h3 class="mb-3 text-xl font-black text-robo-dark">Belangrijkste regels</h3>
            <ul class="space-y-3 text-slate-700">
              <li><strong>De print heeft altijd gelijk.</strong> Twijfel je tussen een schema/document en de echte print? Volg de print.</li>
              <li><strong>Gebruik altijd een accu met bescherming.</strong> Een 1S LiPo met ingebouwde protectie, of een losse protection-module ertussen — nooit een kale accu zonder bescherming rechtstreeks aansluiten.</li>
              <li><strong>GPIO5 stuurt de DRV8833 aan.</strong> Dat is met opzet zo gekozen; verander dit niet zomaar in je firmware.</li>
              <li><strong>Houd motorstroom en ESP-stroom gescheiden.</strong> Zo blijft de kleine computer veilig, ook als de motoren veel stroom trekken.</li>
            </ul>
          </div>
        </div>

        <div class="mt-12 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
          <h3 class="mb-4 text-2xl font-black text-robo-dark">1S is de default, 2S is optioneel</h3>
          <div class="grid gap-6 md:grid-cols-3">
            <div class="rounded-xl bg-robo-orange/5 p-4">
              <p class="text-sm font-bold uppercase tracking-wide text-robo-orange">1S standaard</p>
              <p class="mt-2 text-slate-700">Licht, compact, makkelijk te bouwen en goed voor eerste tests.</p>
            </div>
            <div class="rounded-xl bg-slate-100 p-4">
              <p class="text-sm font-bold uppercase tracking-wide text-slate-700">2S met regelaar</p>
              <p class="mt-2 text-slate-700">Meer vermogen en snelheid, maar extra aandacht nodig op voeding en jumper.</p>
            </div>
            <div class="rounded-xl bg-slate-100 p-4">
              <p class="text-sm font-bold uppercase tracking-wide text-slate-700">UPS / boost</p>
              <p class="mt-2 text-slate-700">Meer runtime, maar zwaarder en complexer. Niet de standaard default.</p>
            </div>
          </div>
        </div>

        <div class="mt-12 space-y-6">
          <h3 class="text-2xl font-black text-robo-dark">De print in beeld (v4)</h3>
          <p class="max-w-3xl text-slate-700">
            Hieronder zie je de v4-print zowel kaal (zonder componenten) als volledig bestukt, van voor- en achterkant. Zo herken je de onderdelen makkelijker terug op je eigen board.
          </p>
          <div class="grid gap-6 sm:grid-cols-2">
            <figure class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
              <img :src="pcbFrontKaal" alt="PCB v4 voorkant zonder componenten" class="w-full object-contain" loading="lazy" />
              <figcaption class="border-t border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600">Voorkant — zonder componenten</figcaption>
            </figure>
            <figure class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
              <img :src="pcbAchterKaal" alt="PCB v4 achterkant zonder componenten" class="w-full object-contain" loading="lazy" />
              <figcaption class="border-t border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600">Achterkant — zonder componenten</figcaption>
            </figure>
            <figure class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
              <img :src="pcbFrontMetComponenten" alt="PCB v4 voorkant met componenten" class="w-full object-contain" loading="lazy" />
              <figcaption class="border-t border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600">Voorkant — met componenten</figcaption>
            </figure>
            <figure class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
              <img :src="pcbAchterMetComponenten" alt="PCB v4 achterkant met componenten" class="w-full object-contain" loading="lazy" />
              <figcaption class="border-t border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600">Achterkant — met componenten</figcaption>
            </figure>
          </div>
        </div>

        <div class="mt-12 space-y-8">
          <section>
            <h3 class="mb-3 text-2xl font-black text-robo-dark">Voeding: wat gaat er naar de ESP?</h3>
            <p class="text-slate-700">
              De ESP32-C3 krijgt stroom binnen via de rail met de naam <strong>ESP_5V_IN</strong> (een 5V-lijn op de print). De ESP32-chip zet die 5V zelf, intern, om naar de 3.3V die hij nodig heeft — jij hoeft daar niets voor te doen of te bouwen.
            </p>
          </section>

          <section>
            <h3 class="mb-3 text-2xl font-black text-robo-dark">Motoren</h3>
            <p class="text-slate-700">
              Op de print zijn de motoruitgangen duidelijk gescheiden voor links en rechts. De DRV8833 (de driver-chip) stuurt beide motoren onafhankelijk aan: de ESP32 geeft alleen kleine stuursignalen, de driver levert de echte motorstroom. Zo blijft de kleine computer beschermd tegen te veel stroom.
            </p>
          </section>

          <section>
            <h3 class="mb-3 text-2xl font-black text-robo-dark">Jumper en pinning</h3>
            <p class="text-slate-700">
              De JP1-jumper heeft 3 pinnetjes op een rijtje. Met het steek-verbindinkje (jumpercap) kies je welke twee van de drie met elkaar verbonden worden:
            </p>
            <ul class="list-disc space-y-1 pl-5 text-slate-700">
              <li><strong>"2S/5VREG"</strong> (bovenste) — uitgang van de 5V-spanningsregelaar</li>
              <li><strong>"5V"</strong> (midden) — de gemeenschappelijke voeding naar de ESP32</li>
              <li><strong>"1S/3.5-4.2V"</strong> (onderste) — rechtstreeks vanaf de accu</li>
            </ul>
            <p class="text-slate-700">
              Gebruik je een <strong>1S-accu</strong>? Zet de jumpercap over de onderste twee: "5V" + "1S/3.5-4.2V". Gebruik je een <strong>2S-accu</strong>? Zet de jumpercap over de bovenste twee: "2S/5VREG" + "5V" (dan loopt de voeding via de spanningsregelaar). Controleer de jumperstand altijd voordat je een nieuwe batterij aansluit. GPIO5 blijft in beide gevallen de vaste sleep-/control-pin voor de DRV8833.
            </p>
          </section>
        </div>

        <div class="mt-14 space-y-10">
          <div class="space-y-3">
            <h3 class="text-3xl font-black text-robo-dark">Stap voor stap: zo bouw je de print</h3>
            <p class="max-w-3xl text-slate-700">
              Deze uitleg is voor iedereen vanaf ongeveer 12 jaar. Ga rustig te werk, stap voor stap. Gebruik je nog nooit een soldeerbout? Vraag dan een ouder, docent of begeleider om erbij te blijven. Een soldeerbout wordt heet en dat kan pijn doen als je hem aanraakt.
            </p>
          </div>

          <!-- Stap 1 -->
          <div class="grid gap-6 rounded-2xl border border-slate-200 bg-slate-50 p-6 md:grid-cols-[auto_1fr]">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-robo-orange text-lg font-black text-white md:h-12 md:w-12 md:text-xl">1</div>
            <div class="space-y-4">
              <h4 class="text-xl font-black text-robo-dark">Soldeer eerst de headers (pinnetjes) voor de ESP32 en de DRV8833</h4>
              <p class="text-slate-700">
                De print heeft twee plekken met rijen gaatjes: één plek aan de <strong>bovenkant</strong> voor de ESP32-computer, en één plek aan de <strong>onderkant</strong> voor de DRV8833 (de motorsturing). Op deze plekken solderen we eerst lege pin-headers (rechte pinnetjes). Daarna kun je de ESP32 en de DRV8833 er straks instekken, net als bij een breadboard.
              </p>
              <ul class="list-disc space-y-1 pl-5 text-slate-700">
                <li>Steek de header eerst los in de gaatjes, zodat hij mooi recht staat.</li>
                <li>Soldeer om en om een paar pinnen vast, en controleer telkens of alles recht blijft staan.</li>
                <li>Doe dit voor beide plekken: de ESP-headers boven en de DRV8833-headers onder.</li>
              </ul>
              <div class="grid gap-4 sm:grid-cols-2">
                <figure class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                  <div class="aspect-[25/19] w-full">
                    <img src="@/assets/pcb/stap-esp-headers-leeg.png" alt="Lege headers voor de ESP32, bovenkant van de print" class="h-full w-full object-contain" loading="lazy" />
                  </div>
                  <figcaption class="border-t border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600">ESP32-headers (bovenkant) — hier komt de ESP32 straks in</figcaption>
                </figure>
                <figure class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                  <div class="aspect-[25/19] w-full">
                    <img src="@/assets/pcb/stap-drv-headers-leeg.png" alt="Lege headers voor de DRV8833, onderkant van de print" class="h-full w-full object-contain" loading="lazy" />
                  </div>
                  <figcaption class="border-t border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600">DRV8833-headers (onderkant) — hier komt de motorsturing straks in</figcaption>
                </figure>
              </div>
            </div>
          </div>

          <!-- Stap 2 -->
          <div class="grid gap-6 rounded-2xl border border-slate-200 bg-slate-50 p-6 md:grid-cols-[auto_1fr]">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-robo-orange text-lg font-black text-white md:h-12 md:w-12 md:text-xl">2</div>
            <div class="space-y-4">
              <h4 class="text-xl font-black text-robo-dark">Plaats de ESP32 boven en de DRV8833 onder in de headers</h4>
              <p class="text-slate-700">
                Nu de headers zijn gesoldeerd, kun je de twee modules erin steken. Let goed op: dit gaat maar op één manier goed passen.
              </p>
              <ul class="list-disc space-y-1 pl-5 text-slate-700">
                <li>De <strong>ESP32-C3 SuperMini</strong> komt aan de <strong>bovenkant</strong> van de print, in de headers die je net hebt gesoldeerd.</li>
                <li>De <strong>DRV8833</strong> (motorsturing) komt aan de <strong>onderkant</strong> van de print.</li>
                <li>Duw beide modules voorzichtig en recht in de headers, tot ze goed vast zitten.</li>
                <li>Je hoeft de modules zelf niet vast te solderen: ze klikken/steken in de headers.</li>
              </ul>
              <div class="grid gap-4 sm:grid-cols-2">
                <figure class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                  <div class="aspect-[25/19] w-full">
                    <img src="@/assets/pcb/stap-esp-geplaatst.png" alt="ESP32 geplaatst in de headers aan de bovenkant" class="h-full w-full object-contain" loading="lazy" />
                  </div>
                  <figcaption class="border-t border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600">ESP32 in de headers — bovenkant</figcaption>
                </figure>
                <figure class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                  <div class="aspect-[25/19] w-full">
                    <img src="@/assets/pcb/stap-drv-geplaatst.png" alt="DRV8833 geplaatst in de headers aan de onderkant" class="h-full w-full object-contain" loading="lazy" />
                  </div>
                  <figcaption class="border-t border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600">DRV8833 in de headers — onderkant</figcaption>
                </figure>
              </div>
            </div>
          </div>

          <!-- Stap 3 -->
          <div class="grid gap-6 rounded-2xl border border-slate-200 bg-slate-50 p-6 md:grid-cols-[auto_1fr]">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-robo-orange text-lg font-black text-white md:h-12 md:w-12 md:text-xl">3</div>
            <div class="space-y-4">
              <h4 class="text-xl font-black text-robo-dark">Soldeer de accu-connector (LiPo)</h4>
              <p class="text-slate-700">
                Zoek op de print de twee pads met het label <strong>LiPo</strong> (batterij). Soldeer hier de accu-connector op vast, zodat je straks veilig een batterij kunt aansluiten en loskoppelen.
              </p>
              <ul class="list-disc space-y-1 pl-5 text-slate-700">
                <li>Let goed op de + en - kant: die moet je niet omdraaien.</li>
                <li>Sluit nog geen accu aan tijdens het solderen. Doe dat pas als alles klaar is.</li>
              </ul>
              <figure class="max-w-xs overflow-hidden rounded-xl border border-slate-200 bg-white">
                <img src="@/assets/pcb/stap-accu-connector.png" alt="Plek voor de accu-connector op de print" class="w-full object-contain" loading="lazy" />
                <figcaption class="border-t border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600">Hier komt de accu-connector</figcaption>
              </figure>
            </div>
          </div>

          <!-- Stap 4 -->
          <div class="grid gap-6 rounded-2xl border border-slate-200 bg-slate-50 p-6 md:grid-cols-[auto_1fr]">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-robo-orange text-lg font-black text-white md:h-12 md:w-12 md:text-xl">4</div>
            <div class="space-y-4">
              <h4 class="text-xl font-black text-robo-dark">Soldeer de aan/uit-schakelaar</h4>
              <p class="text-slate-700">
                De schakelaar (switch) zorgt ervoor dat je de robot makkelijk aan en uit kunt zetten, zonder steeds de accu los te maken. Soldeer de schakelaar op de daarvoor bedoelde plek.
              </p>
              <figure class="max-w-xs overflow-hidden rounded-xl border border-slate-200 bg-white">
                <img src="@/assets/pcb/stap-switch.png" alt="Plek voor de aan/uit-schakelaar op de print" class="w-full object-contain" loading="lazy" />
                <figcaption class="border-t border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600">Hier komt de aan/uit-schakelaar</figcaption>
              </figure>
            </div>
          </div>

          <!-- Stap 5 -->
          <div class="grid gap-6 rounded-2xl border border-slate-200 bg-slate-50 p-6 md:grid-cols-[auto_1fr]">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-robo-orange text-lg font-black text-white md:h-12 md:w-12 md:text-xl">5</div>
            <div class="space-y-4">
              <h4 class="text-xl font-black text-robo-dark">Soldeer de motorconnectors en de motordraden</h4>
              <p class="text-slate-700">
                Er zijn twee motoraansluitingen: één voor de linkermotor en één voor de rechtermotor. Soldeer eerst de connectors op de print, en soldeer daarna de draden van je motoren aan de juiste connector.
              </p>
              <ul class="list-disc space-y-1 pl-5 text-slate-700">
                <li>Verwissel de linker- en rechtermotor niet: anders rijdt je robot de verkeerde kant op.</li>
                <li>Controleer de motordraden twee keer voordat je verder gaat.</li>
              </ul>
              <figure class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                <img src="@/assets/pcb/stap-motorconnectors.png" alt="Motorconnectors links en rechts op de print" class="w-full object-contain" loading="lazy" />
                <figcaption class="border-t border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600">Motorconnectors: links en rechts, naast de DRV8833</figcaption>
              </figure>
            </div>
          </div>

          <!-- Stap 6 -->
          <div class="grid gap-6 rounded-2xl border border-robo-orange/30 bg-robo-orange/5 p-6 md:grid-cols-[auto_1fr]">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-robo-orange text-lg font-black text-white md:h-12 md:w-12 md:text-xl">6</div>
            <div class="space-y-4">
              <h4 class="text-xl font-black text-robo-dark">Kies 1S of 2S: zet de jumper goed</h4>
              <p class="text-slate-700">
                Als allerlaatste stap kies je met de jumper (JP1) welke batterij je gebruikt. Dit doe je met een klein steek-verbindinkje (jumpercap) over twee van de drie pinnetjes. Op de print staat dit ook gewoon in tekst:
              </p>
              <ul class="list-disc space-y-1 pl-5 text-slate-700">
                <li><strong>Gebruik je een 1S-batterij</strong> (de standaardkeuze)? Zet de jumper over <strong>"5V" en "1S/3.5-4.2V"</strong> (de onderste twee pinnetjes).</li>
                <li><strong>Gebruik je een 2S-batterij met spanningsregelaar</strong>? Zet de jumper over <strong>"2S/5VREG" en "5V"</strong> (de bovenste twee pinnetjes).</li>
                <li>Weet je het niet zeker? Kies dan gewoon 1S: dat is de simpelste en veiligste optie om mee te beginnen.</li>
              </ul>
              <figure class="max-w-xs overflow-hidden rounded-xl border border-slate-200 bg-white">
                <img src="@/assets/pcb/stap-jumper-1s-2s.png" alt="Jumper JP1 met de labels 2S/5VREG, 5V en 1S/3.5-4.2V" class="w-full object-contain" loading="lazy" />
                <figcaption class="border-t border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600">Jumper JP1 — kies hier tussen 1S en 2S</figcaption>
              </figure>
              <p class="text-slate-700">
                Klaar! Test je robot eerst rustig zonder dat de wielen ergens tegenaan duwen, voordat je op volle snelheid gaat rijden.
              </p>
            </div>
          </div>

          <!-- Stap 7 -->
          <div class="grid gap-6 rounded-2xl border border-robo-orange/30 bg-robo-orange/5 p-6 md:grid-cols-[auto_1fr]">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-robo-orange text-lg font-black text-white md:h-12 md:w-12 md:text-xl">7</div>
            <div class="space-y-4">
              <h4 class="text-xl font-black text-robo-dark">Eerste test: eerst met USB, dan pas met de accu</h4>
              <p class="text-slate-700">
                Voordat je met een volle accu gaat rijden, test je de print het liefst rustig in twee stappen. Zo zie je snel of alles goed werkt, zonder risico.
              </p>

              <div class="space-y-2">
                <p class="font-bold text-robo-dark">Test 1: met een USB-kabel (zonder accu)</p>
                <ul class="list-disc space-y-1 pl-5 text-slate-700">
                  <li>Zorg dat de <strong>accu losgekoppeld</strong> is.</li>
                  <li>Sluit een USB-kabel aan op de ESP32-C3. De print krijgt dan gewoon stroom via USB.</li>
                  <li>Controleer of de ESP32 opstart (bijvoorbeeld een lampje dat aangaat) en test je firmware.</li>
                  <li>Haal de USB-kabel er weer af voordat je de accu aansluit.</li>
                </ul>
              </div>

              <div class="space-y-2">
                <p class="font-bold text-robo-dark">Test 2: met de accu en de aan/uit-schakelaar</p>
                <ul class="list-disc space-y-1 pl-5 text-slate-700">
                  <li>Zorg dat de <strong>USB-kabel losgekoppeld</strong> is.</li>
                  <li>Zet de aan/uit-schakelaar eerst op <strong>uit</strong>, sluit dan pas de accu aan.</li>
                  <li>Zet de schakelaar op <strong>aan</strong> en controleer of de ESP32 netjes opstart.</li>
                  <li>Test eerst rustig zonder dat de wielen ergens tegenaan duwen, voordat je op volle snelheid rijdt.</li>
                </ul>
              </div>

              <p class="rounded-lg bg-white p-3 text-sm text-slate-700">
                <strong>Belangrijk:</strong> gebruik nooit USB en accu tegelijk. Als beide voedingen tegelijk aangesloten zijn, kan dat de print in de war brengen. Koppel dus altijd eerst de ene voeding los, voordat je de andere aansluit.
              </p>
            </div>
          </div>
        </div>

        <div class="mt-14 space-y-8">
          <div class="space-y-3">
            <h3 class="text-3xl font-black text-robo-dark">Troubleshooting: problemen oplossen</h3>
            <p class="max-w-3xl text-slate-700">
              Werkt de robot niet meteen zoals verwacht? Geen paniek, dat hoort erbij. <strong>Meten is weten:</strong> met een multimeter kun je gewoon nameten wat er écht gebeurt, in plaats van te gokken. Zet de multimeter op DC-volt (spanning meten), het rode meetsnoertje op +, het zwarte op -.
            </p>
          </div>

          <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6">
            <h4 class="mb-3 text-xl font-black text-robo-dark">Spanningen om te checken (wat is normaal?)</h4>
            <div class="overflow-x-auto">
              <table class="w-full min-w-[520px] border-collapse text-left text-sm text-slate-700">
                <thead>
                  <tr class="border-b border-slate-300 text-xs font-bold uppercase tracking-wide text-slate-500">
                    <th class="py-2 pr-4">Waar meet je?</th>
                    <th class="py-2 pr-4">Wat verwacht je?</th>
                    <th class="py-2">Wat als het afwijkt?</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                  <tr>
                    <td class="py-2 pr-4">Accu los, tussen + en -</td>
                    <td class="py-2 pr-4">1S LiPo: ongeveer <strong>3,7 - 4,2V</strong></td>
                    <td class="py-2">Veel lager? Accu is (bijna) leeg of kapot — laad of vervang hem.</td>
                  </tr>
                  <tr>
                    <td class="py-2 pr-4">ESP_5V_IN, jumper op <strong>1S</strong></td>
                    <td class="py-2 pr-4">Zelfde als de accu: <strong>3,7 - 4,2V</strong></td>
                    <td class="py-2">0V? Check schakelaar, jumperstand en soldeerpunten van de accu-connector.</td>
                  </tr>
                  <tr>
                    <td class="py-2 pr-4">ESP_5V_IN, jumper op <strong>2S</strong></td>
                    <td class="py-2 pr-4">Rond de <strong>5V</strong> (via de regelaar)</td>
                    <td class="py-2">Sterk afwijkend? Check de regelaar en de jumperstand nog eens.</td>
                  </tr>
                  <tr>
                    <td class="py-2 pr-4">Tussen twee GND-punten</td>
                    <td class="py-2 pr-4"><strong>0V</strong> verschil</td>
                    <td class="py-2">Wel verschil? Ergens zit een slechte massaverbinding (koude soldeerplek).</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <p class="mt-3 text-sm text-slate-600">
              Twijfel je over een meting? Vergelijk met een print waarvan je zeker weet dat hij goed werkt, of vraag een begeleider om mee te kijken.
            </p>
          </div>

          <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6">
            <h4 class="mb-3 text-xl font-black text-robo-dark">Veelvoorkomende problemen</h4>
            <ul class="space-y-4 text-slate-700">
              <li>
                <p class="font-bold text-robo-dark">De robot doet helemaal niets, geen lampje.</p>
                <p>Meet de accuspanning. Is die goed? Controleer dan de schakelaar (staat hij op aan?), de jumperstand van JP1, en of de accu-connector goed vastgesoldeerd is.</p>
              </li>
              <li>
                <p class="font-bold text-robo-dark">De ESP32 start steeds opnieuw op (knippert/reset herhaaldelijk).</p>
                <p>Vaak een teken van te weinig spanning onder belasting of een wankele soldeerverbinding. Meet ESP_5V_IN terwijl de robot "aan" staat: zakt de spanning sterk weg, dan is er ergens een slecht contact of een (bijna) lege accu.</p>
              </li>
              <li>
                <p class="font-bold text-robo-dark">Eén motor draait niet, of draait de verkeerde kant op.</p>
                <p>Controleer eerst welke motor op welke connector zit (links/rechts kunnen verwisseld zijn). Wiebel voorzichtig aan de motordraden: beweegt het beeld/geluid mee, dan is er een los soldeerpunt.</p>
              </li>
              <li>
                <p class="font-bold text-robo-dark">De print ruikt raar, wordt (te) warm, of er komt rook vanaf.</p>
                <p><strong>Koppel direct de accu los</strong> en zet de schakelaar uit. Dit is een teken van kortsluiting. Laat een begeleider meekijken voordat je verder test.</p>
              </li>
            </ul>
          </div>

          <div class="rounded-xl border border-robo-orange/30 bg-robo-orange/5 p-4 text-sm text-slate-700">
            <strong>Tip:</strong> zoek je een fout? Koppel de accu even los en test rustig stap voor stap terug (eerst USB-test, dan pas accu), net als in stap 7 hierboven. Dat is veiliger dan verder proberen met een fout die je nog niet snapt.
          </div>
        </div>
      </div>
    </section>
  </main>
</template>
