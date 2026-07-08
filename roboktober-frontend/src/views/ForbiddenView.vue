<template>
  <main class="min-h-screen bg-robo-dark py-16 text-white">
    <section class="mx-auto max-w-2xl rounded-xl border border-red-400/30 bg-red-500/10 p-8 text-center">
      <p class="text-sm font-semibold uppercase tracking-wide text-red-300">HTTP 403</p>
      <h1 class="mt-2 text-3xl font-black">Geen toegang</h1>
      <p class="mt-4 text-slate-200">
        Je bent ingelogd, maar je account heeft geen rechten voor deze admin pagina.
      </p>
      <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
        <RouterLink
          to="/"
          class="rounded-lg bg-robo-orange px-5 py-3 font-bold text-white transition hover:bg-robo-orange-dark"
        >
          Terug naar home
        </RouterLink>
        <RouterLink
          to="/aanmelding/wijzigen"
          class="rounded-lg border border-white/20 px-5 py-3 font-semibold text-slate-100 transition hover:bg-white/10"
        >
          Naar mijn accountflow
        </RouterLink>
      </div>
    </section>
  </main>
</template>
