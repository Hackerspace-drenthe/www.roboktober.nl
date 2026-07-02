<script setup lang="ts">
// Statische 404-pagina — geen API-aanroepen
import headerImage from '@/assets/headers/header-404.png'

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
      <div class="relative z-10 mx-auto max-w-3xl px-6 text-center">
        <p class="mb-2 text-8xl font-black text-robo-orange" aria-hidden="true">404</p>
        <h1 class="mb-4 text-3xl font-black">Pagina niet gevonden</h1>
        <p class="mx-auto max-w-md text-slate-300">
          De pagina die je zoekt bestaat niet of is verplaatst.
        </p>
      </div>
    </section>

    <section class="bg-white px-6 py-16 text-center">
      <RouterLink
        to="/"
        class="inline-block rounded-lg bg-robo-orange px-8 py-3 font-bold text-white transition hover:bg-robo-orange-dark focus:outline-none focus:ring-2 focus:ring-robo-orange focus:ring-offset-2"
      >
        Terug naar home
      </RouterLink>
    </section>
  </main>
</template>
