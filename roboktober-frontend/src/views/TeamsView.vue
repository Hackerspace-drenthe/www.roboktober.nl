<script setup lang="ts">
import { getTeams } from '@/api'
import type { Team } from '@/types/api'
import { onMounted, ref } from 'vue'
import headerImage from '@/assets/headers/header-teams.png'

const teams = ref<Team[]>([])
const laden = ref(true)
const fout = ref<string | null>(null)

const heroStyle = {
  backgroundImage: `url(${headerImage})`,
  backgroundSize: 'cover',
  backgroundPosition: 'center',
}

onMounted(async () => {
  try {
    teams.value = await getTeams()
  } catch {
    fout.value = 'Teams konden niet worden geladen. Probeer het later opnieuw.'
  } finally {
    laden.value = false
  }
})
</script>

<template>
  <main id="main-content">
    <section class="relative overflow-hidden py-20 text-white" :style="heroStyle">
      <div class="absolute inset-0 bg-robo-dark/75" aria-hidden="true" />
      <div class="relative z-10 mx-auto max-w-3xl px-6 text-center">
        <h1 class="mb-4 text-4xl font-black md:text-5xl">Teams</h1>
        <p class="text-lg text-slate-300">
          Dit zijn de ingeschreven teams voor Roboktober 2026. Meer teams volgen.
        </p>
      </div>
    </section>

    <section class="bg-white py-20" aria-labelledby="teams-title">
      <div class="mx-auto max-w-4xl px-6">
        <h2 id="teams-title" class="sr-only">Teamoverzicht</h2>

        <!-- Laden -->
        <div v-if="laden" class="grid gap-6 md:grid-cols-2" aria-busy="true" aria-label="Teams laden">
          <div
            v-for="n in 2"
            :key="n"
            class="animate-pulse rounded-xl border border-slate-200 bg-slate-100 p-6"
            aria-hidden="true"
          >
            <div class="mb-3 h-5 w-1/2 rounded bg-slate-200" />
            <div class="h-4 w-3/4 rounded bg-slate-200" />
          </div>
        </div>

        <!-- Foutmelding -->
        <div v-else-if="fout" role="alert" class="rounded-xl border border-red-200 bg-red-50 p-6 text-red-700">
          {{ fout }}
        </div>

        <!-- Lege staat -->
        <div v-else-if="teams.length === 0" class="py-12 text-center text-slate-500">
          <p class="text-lg">Nog geen teams ingeschreven.</p>
          <RouterLink to="/aanmelden" class="mt-4 inline-block font-medium text-robo-orange hover:underline">
            Schrijf je in als eerste team &rarr;
          </RouterLink>
        </div>

        <!-- Teams -->
        <ul v-else class="grid gap-6 md:grid-cols-2" role="list">
          <li v-for="team in teams" :key="team.id">
            <RouterLink
              :to="`/teams/${team.id}`"
              class="block rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-robo-orange/40 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-robo-orange"
            >
              <h3 class="mb-2 text-lg font-bold text-robo-dark">{{ team.naam }}</h3>
              <p v-if="team.robots.length" class="text-sm text-slate-500">
                {{ team.robots.length }} robot{{ team.robots.length !== 1 ? 's' : '' }}
              </p>
              <p v-else class="text-sm text-slate-400">Nog geen robots ingeschreven</p>
            </RouterLink>
          </li>
        </ul>

        <!-- CTA -->
        <div class="mt-12 text-center">
          <RouterLink
            to="/aanmelden"
            class="inline-block rounded-lg bg-robo-orange px-8 py-3 font-bold text-white transition hover:bg-robo-orange-dark focus:outline-none focus:ring-2 focus:ring-robo-orange focus:ring-offset-2"
          >
            Meld jouw team aan
          </RouterLink>
        </div>
      </div>
    </section>
  </main>
</template>
