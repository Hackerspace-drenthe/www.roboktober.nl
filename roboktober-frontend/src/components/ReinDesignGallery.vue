<script setup lang="ts">
import designs from '@/data/rein-designs.json'

const assetUrl = (file: string) => `${import.meta.env.BASE_URL}downloads/designs/rein/${file}`
</script>

<template>
  <section class="bg-slate-50 py-16" aria-labelledby="rein-designs-title">
    <div class="mx-auto max-w-6xl px-6">
      <div class="mb-8 max-w-3xl space-y-4">
        <p class="text-sm font-bold uppercase tracking-widest text-robo-orange">Ontwerpen uit de community</p>
        <h2 id="rein-designs-title" class="text-3xl font-black text-robo-dark">De ontwerpen van Rein van Hackerspace Drenthe</h2>
        <p class="text-slate-600">
          Naast het originele WallieOnline-ontwerp op MakerWorld heeft Rein van Hackerspace Drenthe ook eigen
          ontwerpen publiek beschikbaar gemaakt. Bekijk de 3D-previews en download een ontwerp
          om zelf mee aan de slag te gaan. Meerdere versies staan bij elkaar; de nieuwste staat bovenaan.
        </p>
      </div>

      <section class="mb-8 rounded-2xl border border-robo-orange/30 bg-white p-6 md:p-8" aria-labelledby="openscad-title">
        <h3 id="openscad-title" class="text-2xl font-black text-robo-dark">Van SCAD-ontwerp naar een printbaar STL-bestand</h3>
        <div class="mt-5 grid gap-8 lg:grid-cols-2">
          <div class="space-y-4 text-sm leading-relaxed text-slate-700">
            <p>
              <strong>Wat is OpenSCAD?</strong> OpenSCAD is een gratis, open-source programma
              voor het maken en aanpassen van 3D-modellen. In plaats van met de muis te tekenen,
              beschrijf je vormen en maten met code. Het programma maakt daar een 3D-model van.
              Door bijvoorbeeld een maat in het bestand te veranderen, kun je een ontwerp aanpassen.
            </p>
            <p>
              Reins <strong>SCAD-bestanden zijn de aanpasbare ontwerpen</strong>.
              Je hoeft niet zelf een heel model te programmeren: open een bestand, kies het
              onderdeel dat je wilt printen en laat OpenSCAD het omzetten naar STL.
              Een STL-bestand bevat de vorm die je slicer nodig heeft.
            </p>
            <a
              href="https://openscad.org/downloads.html"
              target="_blank"
              rel="noopener noreferrer"
              class="inline-flex rounded-lg bg-robo-dark px-4 py-3 font-bold text-white transition hover:bg-robo-orange-dark focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-robo-orange focus-visible:ring-offset-2"
            >Download OpenSCAD gratis<span class="sr-only"> (opent in nieuw venster)</span></a>
            <p class="text-xs text-slate-600">Beschikbaar voor Windows, macOS en Linux via de officiële OpenSCAD-website.</p>
          </div>
          <div>
            <h4 class="font-black text-robo-dark">Zo maak je een STL</h4>
            <ol class="mt-3 list-decimal space-y-3 pl-5 text-sm leading-relaxed text-slate-700 marker:font-bold marker:text-robo-orange">
              <li><strong>Installeer OpenSCAD.</strong> Download de versie voor jouw besturingssysteem via de knop hiernaast.</li>
              <li><strong>Download een SCAD-bestand hieronder.</strong> Staat er een extra benodigd bestand bij? Download dat ook en bewaar beide in dezelfde map.</li>
              <li><strong>Open het bestand in OpenSCAD.</strong> Kies via <code>part</code> het losse onderdeel, bijvoorbeeld <code>tub</code> (onderkant) of <code>lid</code> (deksel). De beschikbare namen staan bij <code>part</code> in het bestand; ze verschillen per ontwerp.</li>
              <li><strong>Bekijk en bereken het model.</strong> Gebruik F5 voor een snelle preview en F6 om de definitieve vorm te berekenen (renderen). Dit kan even duren. Je vindt deze acties ook in het menu Design.</li>
              <li><strong>Exporteer als STL.</strong> Kies File → Export → Export as STL en sla het bestand op. Herhaal dit voor elk onderdeel dat je nodig hebt.</li>
              <li><strong>Open de STL in je slicer.</strong> Kies je printinstellingen, controleer de oriëntatie, eventuele supports en het geschatte gewicht, en maak het printbestand voor jouw printer.</li>
            </ol>
          </div>
        </div>
        <div class="mt-6 rounded-xl bg-robo-orange/10 p-4 text-sm leading-relaxed text-slate-700">
          <p><strong>Let op:</strong> <code>assembly</code> is een overzicht van de gemonteerde robot, soms inclusief voorbeeldonderdelen zoals motoren en elektronica. Exporteer voor het printen de losse onderdelen, niet dit complete overzicht.</p>
          <p class="mt-2">Heb je al een STL gedownload? Dan kun je OpenSCAD overslaan en direct naar je slicer. Dit zijn ontwerpen in ontwikkeling: controleer maten, passing en gewicht en maak eerst een testprint.</p>
        </div>
      </section>

      <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        <article
          v-for="design in designs"
          :key="design.id"
          :aria-labelledby="`rein-design-${design.id}`"
          class="flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:border-robo-orange/50 hover:shadow-md"
        >
          <figure class="border-b border-slate-100 bg-slate-100">
            <img
              :src="assetUrl(`${design.id}.png`)"
              :alt="`3D-preview van ${design.title} van Rein van Hackerspace Drenthe`"
              width="960"
              height="720"
              loading="lazy"
              class="aspect-4/3 w-full object-contain"
            />
            <figcaption class="px-5 pb-3 text-xs text-slate-600">
              {{ design.id === 'mostly-harmless' ? 'Preview van het chassis' : `Preview ${design.versions[0]?.label}` }}
            </figcaption>
          </figure>
          <div class="flex flex-1 flex-col p-5">
            <h3 :id="`rein-design-${design.id}`" class="text-xl font-black text-robo-dark">{{ design.title }}</h3>
            <p class="mb-5 mt-2 text-sm text-slate-600">{{ design.description }}</p>
            <ul class="mt-auto space-y-2" :aria-label="`Downloads voor ${design.title}`">
              <li v-for="version in design.versions" :key="version.file">
                <a
                  :href="assetUrl(version.file)"
                  :download="version.file"
                  :aria-label="`Download ${design.title} ${version.label} (${version.file.endsWith('.stl') ? 'STL' : 'SCAD'})`"
                  class="flex items-center justify-between gap-3 rounded-lg border border-slate-200 px-3 py-2 text-sm font-bold text-robo-dark transition hover:border-robo-orange hover:bg-robo-orange/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-robo-orange"
                >
                  <span>{{ version.label }}</span>
                  <span class="text-xs text-slate-600">Download {{ version.file.endsWith('.stl') ? 'STL' : 'SCAD' }} &darr;</span>
                </a>
                <p v-if="version.dependency" class="mt-2 px-1 text-xs text-slate-600">
                  Download ook
                  <a
                    :href="assetUrl(version.dependency)"
                    :download="version.dependency"
                    class="break-all font-semibold underline underline-offset-2 hover:text-robo-orange-dark"
                  >{{ version.dependency }}</a>
                  en bewaar beide bestanden in dezelfde map.
                </p>
              </li>
            </ul>
          </div>
        </article>
      </div>
    </div>
  </section>
</template>
