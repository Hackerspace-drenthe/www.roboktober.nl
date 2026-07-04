<script setup lang="ts">
// Statische pagina — BOM en bouwstappen voor de Roboktober-kit
import headerImage from '@/assets/headers/header-bouwen.png'
import stap1Image from '@/assets/headers/header-bouwen.png'
import stap2Image from '@/assets/headers/header-build-hub.png'
import stap3Image from '@/assets/headers/header-team-detail.png'
import stap4Image from '@/assets/headers/header-programma.png'
import stap5Image from '@/assets/headers/header-teams.png'
import stap6Image from '@/assets/headers/header-walter.png'

const heroStyle = {
  backgroundImage: `url(${headerImage})`,
  backgroundSize: 'cover',
  backgroundPosition: 'center',
}

interface VideoItem {
  titel: string
  beschrijving: string
  embedUrl: string
  watchUrl: string
}

interface BouwStap {
  nummer: number
  titel: string
  doel: string
  duur: string
  afbeelding: string
  alt: string
  materialen: string[]
  acties: string[]
  veiligheid: string
}

const bouwStappen: BouwStap[] = [
  {
    nummer: 1,
    titel: 'Kies en print je chassis',
    doel: 'Maak een stevige basis waarin motoren en elektronica strak passen.',
    duur: '30-60 min',
    afbeelding: stap1Image,
    alt: 'Een antweight chassis op de werkbank met onderdelen ernaast',
    materialen: ['Chassis STL of tekening', 'PLA/PETG of acryl/multiplex', 'M2 boutjes en afstandsbusjes'],
    acties: [
      'Download een antweight chassis of teken je eigen basis.',
      'Print of snijd de plaatdelen en test of de motoren passen.',
      'Werk scherpe randen weg zodat bedrading niet beschadigt.',
    ],
    veiligheid: 'Draag veiligheidsbril bij lasercutten en ontbraam alle randen.',
  },
  {
    nummer: 2,
    titel: 'Monteer motoren en wielen',
    doel: 'Zet de aandrijving mechanisch vast zonder speling.',
    duur: '20-40 min',
    afbeelding: stap2Image,
    alt: 'Twee N20 motoren en wielen die in een robotchassis worden geplaatst',
    materialen: ['2x N20 motor', '2x wiel 32-40 mm', 'Tie-wrap of motorhouder'],
    acties: [
      'Plaats beide N20 motoren in dezelfde rijrichting.',
      'Druk de wielen recht op de assen en controleer vrije loop.',
      'Fixeer motoren stevig zodat ze niet draaien in het chassis.',
    ],
    veiligheid: 'Test met de hand of niets blokkeert voordat je spanning aansluit.',
  },
  {
    nummer: 3,
    titel: 'Sluit driver, accu en schakelaar aan',
    doel: 'Maak een betrouwbare basisbedrading voor links/rechts motorsturing.',
    duur: '30-50 min',
    afbeelding: stap3Image,
    alt: 'Bedradingsschema met motordriver, accu en schakelaar voor een antweight robot',
    materialen: ['DRV8833 of TB6612FNG', '1S LiPo', 'Silicone draad 24AWG', 'Aan/uit schakelaar'],
    acties: [
      'Verbind elke motor met een eigen output van de motordriver.',
      'Plaats de schakelaar tussen accu plus en driver voedingsingang.',
      'Houd kabels kort en zet ze vast met tie-wrap of tape.',
    ],
    veiligheid: 'Controleer polariteit twee keer voordat je de LiPo koppelt.',
  },
  {
    nummer: 4,
    titel: 'Programmeer de ESP32-C3',
    doel: 'Laat de robot reageren op stuurinput via Wi-Fi, BLE of ESP-NOW.',
    duur: '20-45 min',
    afbeelding: stap4Image,
    alt: 'ESP32-C3 controller die via USB wordt geflasht op een laptop',
    materialen: ['ESP32-C3 board', 'USB-kabel', 'Arduino IDE of PlatformIO'],
    acties: [
      'Upload een basis sketch met links/rechts motorregeling.',
      'Koppel de juiste GPIO pinnen aan je motordriver.',
      'Test of vooruit, achteruit en draaien afzonderlijk werken.',
    ],
    veiligheid: 'Laat wielen vrij draaien of zet de robot op een standaard tijdens softwaretest.',
  },
  {
    nummer: 5,
    titel: 'Kalibreer en maak je testritten',
    doel: 'Zorg dat de robot recht rijdt, goed remt en betrouwbaar reageert.',
    duur: '20-30 min',
    afbeelding: stap5Image,
    alt: 'Antweight robot die op een testbaan wordt bestuurd',
    materialen: ['Vlakke testvloer', 'Keukenweegschaal', 'Reserve accu'],
    acties: [
      'Rij korte rondes en corrigeer links/rechts motorverschil in software.',
      'Meet totaalgewicht en blijf onder 150 gram.',
      'Check of de robot na 3 minuten nog stabiel bestuurbaar is.',
    ],
    veiligheid: 'Test eerst op lage snelheid en houd toeschouwers op afstand.',
  },
  {
    nummer: 6,
    titel: 'Werk af en bereid battle setup voor',
    doel: 'Maak je robot battle-ready met veilige afwerking en snelle service-toegang.',
    duur: '20-40 min',
    afbeelding: stap6Image,
    alt: 'Afgewerkte antweight robot met beschermkap en duidelijk bereikbare schakelaar',
    materialen: ['Topcover', 'Foam tape of klittenband', 'Naamsticker / teamlabel'],
    acties: [
      'Bescherm elektronica met een lichte cover en zorg voor airflow.',
      'Maak accu en schakelaar snel bereikbaar voor pit-stop wissels.',
      'Label robotnaam en voer een laatste volledige functietest uit.',
    ],
    veiligheid: 'Controleer dat er geen losse onderdelen of scherpe uitstekende delen zijn.',
  },
]

const bouwVideos: VideoItem[] = [
  {
    titel: 'Building ESP32 robot controllers',
    beschrijving: 'Stap-voor-stap bouw van de WORC robot controller met ESP32-C3.',
    embedUrl: 'https://www.youtube.com/embed/p5JXhyKQbI0',
    watchUrl: 'https://www.youtube.com/watch?v=p5JXhyKQbI0',
  },
  {
    titel: 'Building ESP-NOW based remote controllers',
    beschrijving: 'Bouwvideo van de afstandsbediening op basis van ESP-NOW.',
    embedUrl: 'https://www.youtube.com/embed/kkPaScOB8mg',
    watchUrl: 'https://www.youtube.com/watch?v=kkPaScOB8mg',
  },
]

const actieVideos: VideoItem[] = [
  {
    titel: 'INDI Robot Games — Battle 1',
    beschrijving: 'Arena-actie en rijgedrag onder druk.',
    embedUrl: 'https://www.youtube.com/embed/pA1xxyYNpXY',
    watchUrl: 'https://www.youtube.com/watch?v=pA1xxyYNpXY',
  },
  {
    titel: 'INDI Robot Games — Battle 2',
    beschrijving: 'Impact, positionering en timing van aanvallen.',
    embedUrl: 'https://www.youtube.com/embed/8G0j4PpP0UM',
    watchUrl: 'https://www.youtube.com/watch?v=8G0j4PpP0UM',
  },
  {
    titel: 'INDI Robot Games — Battle 3',
    beschrijving: 'Strategie in de arena en omgaan met schade.',
    embedUrl: 'https://www.youtube.com/embed/RdMRKflSdfU',
    watchUrl: 'https://www.youtube.com/watch?v=RdMRKflSdfU',
  },
]
</script>

<template>
  <main id="main-content">
    <!-- Hero -->
    <section class="relative overflow-hidden py-20 text-white" :style="heroStyle">
      <div class="absolute inset-0 bg-robo-dark/75" aria-hidden="true" />
      <div class="relative z-10 mx-auto max-w-3xl px-6">
        <p class="mb-3 text-sm font-bold uppercase tracking-widest text-robo-orange">Roboktober-kit</p>
        <h1 class="mb-4 text-4xl font-black md:text-5xl">Bouw je eigen antweight</h1>
        <p class="max-w-2xl text-lg text-slate-300">
          Alles wat je nodig hebt om te beginnen. Geen ervaring vereist —
          een basisrobot bouw je voor <strong class="text-white">€15 à €20</strong>.
        </p>
      </div>
    </section>

    <!-- Bill of Materials -->
    <section class="bg-white py-20" aria-labelledby="bom-title">
      <div class="mx-auto max-w-4xl px-6">
        <h2 id="bom-title" class="mb-2 text-2xl font-black text-robo-dark">Bill of Materials</h2>
        <p class="mb-8 text-slate-500">De minimale onderdelen voor een rijdende antweight (≤ 150 gram).</p>

        <div class="overflow-x-auto rounded-xl border border-slate-200">
          <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
              <tr>
                <th scope="col" class="px-4 py-3">Onderdeel</th>
                <th scope="col" class="px-4 py-3">Spec</th>
                <th scope="col" class="px-4 py-3">Aantal</th>
                <th scope="col" class="px-4 py-3">Prijs (ca.)</th>
                <th scope="col" class="px-4 py-3">AliExpress</th>
                <th scope="col" class="px-4 py-3">Officieel / Specs</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 font-semibold text-robo-dark">N20 micromotor</td>
                <td class="px-4 py-3 text-slate-600">6 V, 300–500 RPM geaard</td>
                <td class="px-4 py-3 text-slate-600">2×</td>
                <td class="px-4 py-3 text-slate-700">€1 – €3 <span class="text-xs text-slate-400">/stuk</span></td>
                <td class="px-4 py-3">
                  <a
                    href="https://www.aliexpress.com/w/wholesale-N20-micro-gear-motor-6v.html"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="text-robo-orange underline hover:text-robo-orange-dark"
                  >Zoek op AliExpress<span class="sr-only"> (opent in nieuw venster)</span></a>
                </td>
                <td class="px-4 py-3">
                  <a
                    href="https://www.pololu.com/category/60/micro-metal-gearmotors"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="text-robo-orange underline hover:text-robo-orange-dark"
                  >Pololu — Micro Metal Gearmotors<span class="sr-only"> (opent in nieuw venster)</span></a>
                </td>
              </tr>
              <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 font-semibold text-robo-dark">Wielen (voor N20)</td>
                <td class="px-4 py-3 text-slate-600">32–40 mm, lichtgewicht, passend op N20 as</td>
                <td class="px-4 py-3 text-slate-600">2×</td>
                <td class="px-4 py-3 text-slate-700">€1 – €4 <span class="text-xs text-slate-400">/set</span></td>
                <td class="px-4 py-3">
                  <a
                    href="https://www.aliexpress.com/w/wholesale-n20-motor-wheel.html"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="text-robo-orange underline hover:text-robo-orange-dark"
                  >Zoek op AliExpress<span class="sr-only"> (opent in nieuw venster)</span></a>
                </td>
                <td class="px-4 py-3">
                  <a
                    href="https://www.pololu.com/category/164/32x7mm-wheels"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="text-robo-orange underline hover:text-robo-orange-dark"
                  >Pololu — 32×7 mm wielen<span class="sr-only"> (opent in nieuw venster)</span></a>
                </td>
              </tr>
              <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 font-semibold text-robo-dark">LiPo accu</td>
                <td class="px-4 py-3 text-slate-600">1S 3,7 V · 500 mAh · 30C+</td>
                <td class="px-4 py-3 text-slate-600">1×</td>
                <td class="px-4 py-3 text-slate-700">€3 – €6</td>
                <td class="px-4 py-3">
                  <a
                    href="https://www.aliexpress.com/w/wholesale-1s-lipo-battery-500mah-30c.html"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="text-robo-orange underline hover:text-robo-orange-dark"
                  >Zoek op AliExpress<span class="sr-only"> (opent in nieuw venster)</span></a>
                </td>
                <td class="px-4 py-3">
                  <a
                    href="https://www.gensace.de/lipo-batteries/1s.html"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="text-robo-orange underline hover:text-robo-orange-dark"
                  >Gens Ace 1S LiPo<span class="sr-only"> (opent in nieuw venster)</span></a>
                </td>
              </tr>
              <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 font-semibold text-robo-dark">Motordriver</td>
                <td class="px-4 py-3 text-slate-600">DRV8833 of TB6612FNG breakout</td>
                <td class="px-4 py-3 text-slate-600">1×</td>
                <td class="px-4 py-3 text-slate-700">€1 – €3</td>
                <td class="px-4 py-3">
                  <a
                    href="https://www.aliexpress.com/w/wholesale-DRV8833-motor-driver-module.html"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="text-robo-orange underline hover:text-robo-orange-dark"
                  >Zoek op AliExpress<span class="sr-only"> (opent in nieuw venster)</span></a>
                </td>
                <td class="px-4 py-3">
                  <a
                    href="https://www.pololu.com/product/2130"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="text-robo-orange underline hover:text-robo-orange-dark"
                  >Pololu — DRV8833<span class="sr-only"> (opent in nieuw venster)</span></a>
                </td>
              </tr>
              <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 font-semibold text-robo-dark">Microcontroller</td>
                <td class="px-4 py-3 text-slate-600">ESP32-C3 (Wi-Fi + Bluetooth)</td>
                <td class="px-4 py-3 text-slate-600">1×</td>
                <td class="px-4 py-3 text-slate-700">€2 – €5</td>
                <td class="px-4 py-3">
                  <a
                    href="https://www.aliexpress.com/w/wholesale-ESP32-C3-mini-development-board.html"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="text-robo-orange underline hover:text-robo-orange-dark"
                  >Zoek op AliExpress<span class="sr-only"> (opent in nieuw venster)</span></a>
                </td>
                <td class="px-4 py-3">
                  <a
                    href="https://www.espressif.com/en/products/socs/esp32-c3"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="text-robo-orange underline hover:text-robo-orange-dark"
                  >Espressif — ESP32-C3 specs<span class="sr-only"> (opent in nieuw venster)</span></a>
                </td>
              </tr>
              <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 font-semibold text-robo-dark">Chassis</td>
                <td class="px-4 py-3 text-slate-600">3D-geprint (PLA/PETG) of lasergesneden (acryl/multiplex)</td>
                <td class="px-4 py-3 text-slate-600">1×</td>
                <td class="px-4 py-3 text-slate-700">€1 – €3 <span class="text-xs text-slate-400">(materiaal)</span></td>
                <td class="px-4 py-3 text-slate-400">—</td>
                <td class="px-4 py-3 text-slate-600">Hackerspace Drenthe heeft 3D-printers en een lasercutter</td>
              </tr>
              <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 font-semibold text-robo-dark">Draden &amp; bevestiging</td>
                <td class="px-4 py-3 text-slate-600">Silicone draad 24 AWG, M2-boutjes, aan/uitschakelaar</td>
                <td class="px-4 py-3 text-slate-600">div.</td>
                <td class="px-4 py-3 text-slate-700">€1 – €2</td>
                <td class="px-4 py-3">
                  <a
                    href="https://www.aliexpress.com/w/wholesale-silicone-wire-24awg.html"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="text-robo-orange underline hover:text-robo-orange-dark"
                  >Zoek op AliExpress<span class="sr-only"> (opent in nieuw venster)</span></a>
                </td>
                <td class="px-4 py-3 text-slate-400">—</td>
              </tr>
              <tr class="border-t-2 border-robo-orange/20 bg-orange-50/50 font-semibold hover:bg-orange-50">
                <td class="px-4 py-3 text-robo-dark">Totaal basis</td>
                <td class="px-4 py-3 text-slate-400" colspan="2">—</td>
                <td class="px-4 py-3 text-robo-dark">≈ €10 – €20</td>
                <td class="px-4 py-3 text-slate-400" colspan="2">—</td>
              </tr>
              <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 font-semibold text-robo-dark">
                  Afstandsbediening
                  <span class="ml-1 rounded bg-slate-100 px-1.5 py-0.5 text-xs font-normal text-slate-500">optie A</span>
                </td>
                <td class="px-4 py-3 text-slate-600">ESP32-C3 + 1S LiPo + joystick/knopjes + behuizing</td>
                <td class="px-4 py-3 text-slate-600">1×</td>
                <td class="px-4 py-3 text-slate-700">€5 – €10</td>
                <td class="px-4 py-3">
                  <a
                    href="https://www.aliexpress.com/w/wholesale-joystick-module-arduino.html"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="text-robo-orange underline hover:text-robo-orange-dark"
                  >Joystick op AliExpress<span class="sr-only"> (opent in nieuw venster)</span></a>
                </td>
                <td class="px-4 py-3 text-slate-600">Zelfde ESP32-C3 als de robot — verzendt via Wi-Fi of BLE</td>
              </tr>
              <tr class="bg-slate-50/50 hover:bg-slate-50">
                <td class="px-4 py-3 font-semibold text-robo-dark">
                  Afstandsbediening
                  <span class="ml-1 rounded bg-robo-orange/10 px-1.5 py-0.5 text-xs font-normal text-robo-orange">optie B</span>
                </td>
                <td class="px-4 py-3 text-slate-600">Android-app of Web GUI op je telefoon / laptop</td>
                <td class="px-4 py-3 text-slate-600">—</td>
                <td class="px-4 py-3 font-semibold text-green-700">Gratis</td>
                <td class="px-4 py-3 text-slate-400">—</td>
                <td class="px-4 py-3 text-slate-600">Geen extra hardware nodig. De ESP32-C3 host een webpagina of koppelt via Bluetooth met een gamepad-app.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="mt-6 rounded-xl border border-robo-orange/30 bg-orange-50 p-5">
          <p class="text-sm text-slate-700">
            <strong class="text-robo-dark">Roboktober-kit:</strong> Tijdens de kickoff-avond zijn er kant-en-klare kits beschikbaar
            met alle bovenstaande elektronica-onderdelen (behalve chassis). Of er ook PCB's bij zitten is nog niet zeker — dat wordt nader bekendgemaakt.
          </p>
        </div>
      </div>
    </section>

    <!-- Bouwstappen -->
    <section class="bg-robo-gray py-20 text-white" aria-labelledby="bouwstappen-title">
      <div class="mx-auto max-w-6xl px-6">
        <h2 id="bouwstappen-title" class="mb-3 text-2xl font-black">Bouwstappen</h2>
        <p class="mb-10 max-w-3xl text-slate-300">
          Volg deze stappen in volgorde. Elke stap heeft een visueel voorbeeld, een korte checklist en een veiligheidscheck.
          Zo bouw je gecontroleerd naar een battle-ready antweight.
        </p>

        <div class="grid gap-8 lg:grid-cols-2">
          <article
            v-for="stap in bouwStappen"
            :key="stap.nummer"
            class="overflow-hidden rounded-2xl border border-white/10 bg-white/5 shadow-lg backdrop-blur-sm"
          >
            <div class="relative h-56 overflow-hidden">
              <img :src="stap.afbeelding" :alt="stap.alt" class="h-full w-full object-cover" loading="lazy" />
              <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/25 to-transparent" aria-hidden="true" />
              <div class="absolute left-4 top-4 inline-flex items-center gap-2 rounded-full bg-robo-orange px-3 py-1 text-xs font-black uppercase tracking-wide text-white">
                Stap {{ stap.nummer }}
              </div>
              <p class="absolute bottom-4 right-4 rounded bg-black/50 px-2 py-1 text-xs font-semibold text-slate-100">
                Tijd: {{ stap.duur }}
              </p>
            </div>

            <div class="space-y-5 p-5">
              <div>
                <h3 class="text-xl font-black text-white">{{ stap.titel }}</h3>
                <p class="mt-1 text-sm text-slate-300">{{ stap.doel }}</p>
              </div>

              <div>
                <h4 class="mb-2 text-xs font-bold uppercase tracking-widest text-robo-orange">Benodigd</h4>
                <ul class="space-y-1 text-sm text-slate-200">
                  <li v-for="item in stap.materialen" :key="item" class="flex items-start gap-2">
                    <span class="mt-1 h-1.5 w-1.5 rounded-full bg-robo-orange" aria-hidden="true" />
                    <span>{{ item }}</span>
                  </li>
                </ul>
              </div>

              <div>
                <h4 class="mb-2 text-xs font-bold uppercase tracking-widest text-robo-orange">Doe dit</h4>
                <ol class="space-y-1 text-sm text-slate-200">
                  <li v-for="(actie, idx) in stap.acties" :key="actie" class="flex items-start gap-2">
                    <span class="mt-0.5 inline-flex min-w-5 items-center justify-center rounded bg-white/10 px-1 text-xs font-bold text-robo-orange">{{ idx + 1 }}</span>
                    <span>{{ actie }}</span>
                  </li>
                </ol>
              </div>

              <div class="rounded-lg border border-amber-300/40 bg-amber-200/10 p-3 text-sm text-amber-100">
                <strong class="font-bold">Veiligheidscheck:</strong>
                {{ stap.veiligheid }}
              </div>
            </div>
          </article>
        </div>

        <div class="mt-8 rounded-xl border border-robo-orange/40 bg-black/20 p-5">
          <p class="text-sm text-slate-200">
            Voor softwarevoorbeelden en extra downloads ga je naar de
            <RouterLink to="/build-hub" class="font-semibold text-robo-orange underline">
              Build Hub
            </RouterLink>.
          </p>
        </div>
      </div>
    </section>

    <!-- Inspiratievideo's -->
    <section class="bg-white py-20" aria-labelledby="inspiratie-videos-title">
      <div class="mx-auto max-w-6xl px-6">
        <h2 id="inspiratie-videos-title" class="mb-3 text-2xl font-black text-robo-dark">
          Bouw- en actievideo's van Wallieonline
        </h2>
        <p class="mb-8 max-w-3xl text-slate-600">
          Deze video's laten precies zien hoe het idee werkt: controller bouwen, robot opbouwen en rijden in de arena.
          Sommige gevechten zijn met zwaardere robots, maar de kern blijft hetzelfde voor antweight:
          besturing, betrouwbaarheid, strategie en veilig bouwen.
        </p>

        <h3 class="mb-4 text-xl font-black text-robo-dark">Bouwvideo's</h3>
        <div class="mb-12 grid gap-8 md:grid-cols-2">
          <article
            v-for="video in bouwVideos"
            :key="video.embedUrl"
            class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm"
          >
            <iframe
              class="aspect-video w-full"
              :src="video.embedUrl"
              :title="video.titel"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            />
            <div class="p-4">
              <h4 class="mb-1 font-bold text-robo-dark">{{ video.titel }}</h4>
              <p class="mb-2 text-sm text-slate-600">{{ video.beschrijving }}</p>
              <a
                :href="video.watchUrl"
                target="_blank"
                rel="noopener noreferrer"
                class="text-sm font-semibold text-robo-orange underline hover:text-robo-orange-dark"
              >
                Bekijk op YouTube<span class="sr-only"> (opent in nieuw venster)</span>
              </a>
            </div>
          </article>
        </div>

        <h3 class="mb-4 text-xl font-black text-robo-dark">Arena-actie (inspiratie)</h3>
        <div class="grid gap-8 lg:grid-cols-3">
          <article
            v-for="video in actieVideos"
            :key="video.embedUrl"
            class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm"
          >
            <iframe
              class="aspect-video w-full"
              :src="video.embedUrl"
              :title="video.titel"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen
            />
            <div class="p-4">
              <h4 class="mb-1 font-bold text-robo-dark">{{ video.titel }}</h4>
              <p class="mb-2 text-sm text-slate-600">{{ video.beschrijving }}</p>
              <a
                :href="video.watchUrl"
                target="_blank"
                rel="noopener noreferrer"
                class="text-sm font-semibold text-robo-orange underline hover:text-robo-orange-dark"
              >
                Bekijk op YouTube<span class="sr-only"> (opent in nieuw venster)</span>
              </a>
            </div>
          </article>
        </div>

        <p class="mt-6 text-sm text-slate-500">
          Meer gevechten en context:
          <a
            href="https://wallieonline.nl/blogs/verslag-indi-robot-games-2019.html"
            target="_blank"
            rel="noopener noreferrer"
            class="font-medium text-robo-orange underline hover:text-robo-orange-dark"
          >
            Verslag INDI Robot Games 2019
          </a>
        </p>
      </div>
    </section>

    <!-- CTA -->
    <section class="bg-white py-16" aria-labelledby="bouwen-cta-title">
      <div class="mx-auto max-w-xl px-6 text-center">
        <h2 id="bouwen-cta-title" class="mb-4 text-2xl font-black text-robo-dark">Klaar om mee te doen?</h2>
        <p class="mb-8 text-slate-600">
          Schrijf je team in en kom bouwen bij Hackerspace Drenthe.
        </p>
        <RouterLink
          to="/aanmelden"
          class="inline-block rounded-lg bg-robo-orange px-8 py-3 font-bold text-white transition hover:bg-robo-orange-dark focus:outline-none focus:ring-2 focus:ring-robo-orange focus:ring-offset-2"
        >
          Aanmelden
        </RouterLink>
      </div>
    </section>
  </main>
</template>
