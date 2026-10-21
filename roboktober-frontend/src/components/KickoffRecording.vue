<script setup lang="ts">
withDefaults(defineProps<{ headingLevel?: 'h2' | 'h3' | 'h4' }>(), {
  headingLevel: 'h2',
})
</script>

<template>
  <div class="rounded-2xl border border-robo-orange/30 bg-robo-dark p-6 text-white md:p-8">
    <p class="mb-3 text-xs font-bold uppercase tracking-widest text-robo-orange">Presentatie op YouTube</p>
    <component :is="headingLevel" class="text-2xl font-black">Kickoff terugkijken</component>
    <p class="mt-3 max-w-2xl leading-relaxed text-slate-300">
      De kickoff gemist of de presentatie nog eens bekijken?
      De opname van de Roboktober-kickoff is beschikbaar op YouTube.
    </p>
    <a
      href="https://www.youtube.com/live/qtz0UkJw4DU"
      target="_blank"
      rel="noopener noreferrer"
      class="mt-5 inline-flex rounded-lg bg-robo-orange px-5 py-3 text-sm font-bold text-white transition hover:bg-robo-orange-dark focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white"
    >
      Bekijk de kickoff op YouTube
      <span class="sr-only"> (opent in nieuw venster)</span>
    </a>
  </div>
</template>
