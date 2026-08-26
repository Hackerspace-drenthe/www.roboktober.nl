<script setup lang="ts">
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import headerImage from '@/assets/headers/header-bouwen.png'
import pcbFrontMetComponenten from '@/assets/pcb/pcb-v4-front-met-componenten.png'
import pcbAchterMetComponenten from '@/assets/pcb/pcb-v4-achter-met-componenten.png'
import pcbFrontKaal from '@/assets/pcb/pcb-v4-front-kaal.png'
import pcbAchterKaal from '@/assets/pcb/pcb-v4-achter-kaal.png'

const route = useRoute()
const actieveTab = computed<'bouwgids' | 'pcb' | 'links'>(() => {
  if (route.name === 'bouwen-links') return 'links'
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
          De print is de bron van waarheid. Deze pagina legt de actuele controllerprint uit, gebaseerd op de geteste v3-fix lay-out en de v4-variant als praktische voortzetting.
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
            De PCB is niet zomaar een idee op papier; het is de bron van waarheid. Als iets in een schema of document afwijkt van de print, dan geldt de print als de juiste referentie.
          </p>
        </div>

        <div class="grid gap-8 md:grid-cols-2">
          <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6">
            <h3 class="mb-3 text-xl font-black text-robo-dark">Hoofdcomponenten</h3>
            <ul class="space-y-2 text-slate-700">
              <li>• ESP32-C3 SuperMini</li>
              <li>• DRV8833 motor driver</li>
              <li>• batterij- en power-rails</li>
              <li>• motorconnectors links/rechts</li>
              <li>• jumper voor 1S/2S voedingskeuze</li>
              <li>• uitbreidingsheaders voor accessoires</li>
            </ul>
          </div>

          <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6">
            <h3 class="mb-3 text-xl font-black text-robo-dark">Belangrijkste regel</h3>
            <p class="text-slate-700">
              De standaardpraktijk voor deze robot is een 1S LiPo direct op de ESP-voedingsrail. De v4 is gebouwd op de bewezen v3-fix lay-out en houdt die praktische default zo veel mogelijk intact.
            </p>
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
              De ESP32-C3 krijgt zijn 5V-voeding via de expliciete ESP_5V_IN-rail. Die is de relevante bron voor de controller. De 3.3V-regulering gebeurt intern in de ESP-module; dat is geen aparte board-ontwerpregel.
            </p>
          </section>

          <section>
            <h3 class="mb-3 text-2xl font-black text-robo-dark">Motoren</h3>
            <p class="text-slate-700">
              Op de print zijn de motoruitgangen duidelijk gescheiden voor links en rechts. De DRV8833 stuurt beide motoren onafhankelijk aan. Als je firmware of schema afwijkt van de PCB, dan geldt de PCB als juiste bron.
            </p>
          </section>

          <section>
            <h3 class="mb-3 text-2xl font-black text-robo-dark">Jumper en pinning</h3>
            <p class="text-slate-700">
              De JP1-jumper heeft 3 pinnen, van boven naar onder: <strong>"2S/5VREG"</strong> (uitgang van de 5V-spanningsregelaar), <strong>"5V"</strong> (de gemeenschappelijke voeding naar de ESP32) en <strong>"1S/3.5-4.2V"</strong> (rechtstreeks vanaf de accu). Zet de jumpercap over "5V" + "1S/3.5-4.2V" voor een 1S-accu (rechtstreekse voeding, geen regelaar), of over "2S/5VREG" + "5V" voor een 2S-accu (dan loopt de voeding via de spanningsregelaar naar 5V). Gebruik alleen de geteste standaardconfiguratie en controleer de jumperstand altijd voordat je een nieuwe batterijconfiguratie inzet. GPIO5 blijft de sleep-/control line voor de DRV8833.
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
                  <img src="@/assets/pcb/stap-esp-headers-leeg.png" alt="Lege headers voor de ESP32, bovenkant van de print" class="w-full object-contain" loading="lazy" />
                  <figcaption class="border-t border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600">ESP32-headers (bovenkant) — hier komt de ESP32 straks in</figcaption>
                </figure>
                <figure class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                  <img src="@/assets/pcb/stap-drv-headers-leeg.png" alt="Lege headers voor de DRV8833, onderkant van de print" class="w-full object-contain" loading="lazy" />
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
                  <img src="@/assets/pcb/stap-esp-geplaatst.png" alt="ESP32 geplaatst in de headers aan de bovenkant" class="w-full object-contain" loading="lazy" />
                  <figcaption class="border-t border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600">ESP32 in de headers — bovenkant</figcaption>
                </figure>
                <figure class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                  <img src="@/assets/pcb/stap-drv-geplaatst.png" alt="DRV8833 geplaatst in de headers aan de onderkant" class="w-full object-contain" loading="lazy" />
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
        </div>
      </div>
    </section>
  </main>
</template>
