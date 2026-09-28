<script setup lang="ts">
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import headerImage from '@/assets/headers/header-bouwen.png'

const route = useRoute()
const actieveTab = computed<'bouwgids' | '3dprint' | 'pcb' | 'firmware' | 'links'>(() => {
  if (route.name === 'bouwen-3dprint') return '3dprint'
  if (route.name === 'bouwen-pcb') return 'pcb'
  if (route.name === 'bouwen-firmware') return 'firmware'
  if (route.name === 'bouwen-links') return 'links'
  return 'bouwgids'
})

const heroStyle = {
  backgroundImage: `url(${headerImage})`,
  backgroundSize: 'cover',
  backgroundPosition: 'center',
}

const wallieBronnen = [
  {
    titel: 'WORC Robot Controller guide (EN)',
    omschrijving: 'Originele Wallie pagina met onderdelen, webflash en WebUI uitleg.',
    href: 'https://wallieonline.nl/blogs/worc-robot-controller-en.html',
    knop: 'Open guide',
  },
  {
    titel: 'Wallie robot-controller firmware repo',
    omschrijving: 'Originele upstream firmware bron voor WORC robot controller.',
    href: 'https://github.com/wallieonline/worc-robot-controller-esp32c3',
    knop: 'Open GitHub repo',
  },
  {
    titel: 'Wallie remote-controller firmware repo',
    omschrijving: 'Originele bron voor de ESP-NOW remote controller app.',
    href: 'https://github.com/wallieonline/worc-remote-controller-esp32',
    knop: 'Open remote repo',
  },
  {
    titel: 'Webflasher manifest',
    omschrijving: 'Manifest dat gebruikt wordt door de Wallie webflasher.',
    href: 'https://wallieonline.nl/software/worc-robot-controller-manifest.json',
    knop: 'Open manifest JSON',
  },
]

const onderdelen = [
  '1x ESP32-C3 Super Mini development board',
  '1x DRV8833 Breakout Board (16-pin versie)',
  '3x male pin headers (2.54mm, 8-pin)',
  '1x battery connector voor voeding',
  'Gereedschap: soldeerbout, tin en USB datakabel',
]

const webflashStappen = [
  'Soldeer eerst de controller en controleer alle verbindingen.',
  'Open de Wallie guide in Chrome of Edge (Firefox/Safari werken niet voor deze flasher).',
  'Sluit de controller aan met een USB datakabel (geen laad-only kabel).',
  'Klik op Flash Firmware, kies poort, klik Connect en daarna Install.',
  'Wacht tot 100% en haal de kabel niet los tijdens flashen.',
]

const webUiStappen = [
  'Na flashen maakt de controller een wifi-netwerk met naam die start met worc-.',
  'Verbind met dat netwerk met wachtwoord: worc1234.',
  'Open daarna in je browser: 192.168.4.1.',
]

const remoteStappen = [
  'Open de originele Wallie remote-controller repository.',
  'Lees het MAC-adres van je robotcontroller uit met readmac.ino.',
  'Vul dat MAC-adres in bij de remote-controller configuratie.',
  'Upload de remote firmware naar je ESP32-zender en test veilig met wielen vrij.',
]

const troubleshooting = [
  'Webflasher opent niet: gebruik Chrome of Edge.',
  'Controller wordt niet gezien: controleer of je USB-kabel data ondersteunt.',
  'Poort ontbreekt: installeer zo nodig CH340 of ESP32-C3 USB driver.',
  'Geen besturing: check MAC-adres, voeding en ESP-NOW instellingen.',
]
</script>

<template>
  <main id="main-content">
    <section class="relative overflow-hidden py-20 text-white" :style="heroStyle">
      <div class="absolute inset-0 bg-robo-dark/75" aria-hidden="true" />
      <div class="relative z-10 mx-auto max-w-4xl px-6 text-center">
        <p class="mb-3 text-sm font-bold uppercase tracking-widest text-robo-orange">Wallie firmware + remote app</p>
        <h1 class="mb-4 text-4xl font-black md:text-5xl">Firmware en remote app bouwen</h1>
        <p class="mx-auto max-w-2xl text-lg text-slate-300">
          Samengevoegde tab: alle stappen en links via de originele Wallie bronnen.
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
            to="/bouwen/firmware"
            role="tab"
            :aria-selected="actieveTab === 'firmware'"
            class="rounded-lg px-5 py-2 text-sm font-semibold transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-robo-orange"
            :class="actieveTab === 'firmware' ? 'bg-white text-robo-dark shadow-sm' : 'text-slate-200 hover:bg-white/10'"
          >
            Firmware
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

    <section class="bg-white py-16" aria-labelledby="wallie-bronnen-title">
      <div class="mx-auto max-w-5xl px-6">
        <h2 id="wallie-bronnen-title" class="text-3xl font-black text-robo-dark">Originele Wallie links en referenties</h2>
        <p class="mt-2 max-w-3xl text-slate-600">
          In deze tab staan alleen originele Wallie bronnen. Geen lokale codevoorbeelden.
        </p>

        <div class="mt-8 grid gap-4 md:grid-cols-2">
          <article v-for="item in wallieBronnen" :key="item.href" class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
            <h3 class="text-lg font-black text-robo-dark">{{ item.titel }}</h3>
            <p class="mt-2 text-sm text-slate-700">{{ item.omschrijving }}</p>
            <a
              :href="item.href"
              target="_blank"
              rel="noopener noreferrer"
              class="mt-4 inline-flex rounded-lg bg-robo-orange px-4 py-2 text-sm font-bold text-white transition hover:bg-robo-orange-dark"
            >
              {{ item.knop }}
            </a>
          </article>
        </div>
      </div>
    </section>

    <section class="bg-slate-50 py-16" aria-labelledby="controller-build-title">
      <div class="mx-auto max-w-5xl px-6">
        <div class="grid gap-6 lg:grid-cols-2">
          <article class="rounded-2xl border border-slate-200 bg-white p-6">
            <h2 id="controller-build-title" class="text-2xl font-black text-robo-dark">WORC controller bouwen</h2>
            <ul class="mt-4 space-y-2 text-slate-700">
              <li v-for="item in onderdelen" :key="item" class="flex items-start gap-2">
                <span class="mt-2 inline-block h-2 w-2 rounded-full bg-robo-orange" aria-hidden="true" />
                <span>{{ item }}</span>
              </li>
            </ul>
          </article>

          <article class="rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-2xl font-black text-robo-dark">Webflash stappen</h2>
            <ul class="mt-4 space-y-2 text-slate-700">
              <li v-for="item in webflashStappen" :key="item" class="flex items-start gap-2">
                <span class="mt-2 inline-block h-2 w-2 rounded-full bg-robo-orange" aria-hidden="true" />
                <span>{{ item }}</span>
              </li>
            </ul>
          </article>

          <article class="rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-2xl font-black text-robo-dark">WebUI verbinden</h2>
            <ul class="mt-4 space-y-2 text-slate-700">
              <li v-for="item in webUiStappen" :key="item" class="flex items-start gap-2">
                <span class="mt-2 inline-block h-2 w-2 rounded-full bg-robo-orange" aria-hidden="true" />
                <span>{{ item }}</span>
              </li>
            </ul>
          </article>

          <article class="rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-2xl font-black text-robo-dark">Remote app koppelen</h2>
            <ul class="mt-4 space-y-2 text-slate-700">
              <li v-for="item in remoteStappen" :key="item" class="flex items-start gap-2">
                <span class="mt-2 inline-block h-2 w-2 rounded-full bg-robo-orange" aria-hidden="true" />
                <span>{{ item }}</span>
              </li>
            </ul>
            <a
              href="https://raw.githubusercontent.com/wallieonline/worc-robot-controller-esp32c3/master/readmac.ino"
              target="_blank"
              rel="noopener noreferrer"
              class="mt-4 inline-flex rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-100"
            >
              Open readmac.ino (origineel)
            </a>
          </article>
        </div>
      </div>
    </section>

    <section class="bg-white py-16" aria-labelledby="troubleshooting-title">
      <div class="mx-auto max-w-5xl px-6">
        <div class="rounded-2xl border border-robo-orange/30 bg-robo-orange/10 p-6">
          <h2 id="troubleshooting-title" class="text-2xl font-black text-robo-dark">Troubleshooting</h2>
          <ul class="mt-4 space-y-2 text-slate-700">
            <li v-for="item in troubleshooting" :key="item" class="flex items-start gap-2">
              <span class="mt-2 inline-block h-2 w-2 rounded-full bg-robo-orange" aria-hidden="true" />
              <span>{{ item }}</span>
            </li>
          </ul>
        </div>
      </div>
    </section>
  </main>
</template>
