<script setup lang="ts">
import { getTeam } from '@/api'
import type { Team } from '@/types/api'
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import headerImage from '@/assets/headers/header-team-detail.png'

const route = useRoute()
const router = useRouter()
const team = ref<Team | null>(null)
const laden = ref(true)
const fout = ref<string | null>(null)

const statusKleur: Record<string, string> = {
  approved: 'bg-green-100 text-green-800',
  pending: 'bg-yellow-100 text-yellow-800',
  rejected: 'bg-red-100 text-red-800',
}

const robotStatusLabel: Record<string, string> = {
  in_ontwikkeling: 'In ontwikkeling',
  gereed: 'Gereed',
  battle_ready: 'Battle ready',
}

const heroStyle = {
  backgroundImage: `url(${headerImage})`,
  backgroundSize: 'cover',
  backgroundPosition: 'center',
}

onMounted(async () => {
  const id = Number(route.params.id)
  if (isNaN(id)) {
    router.replace('/niet-gevonden')
    return
  }
  try {
    team.value = await getTeam(id)
  } catch {
    fout.value = 'Team niet gevonden.'
  } finally {
    laden.value = false
  }
})
</script>

<template>
  <main id="main-content">
    <!-- Laden -->
    <div v-if="laden" class="mx-auto max-w-3xl px-6 py-24" aria-busy="true" aria-label="Team laden">
      <div class="animate-pulse space-y-4">
        <div class="h-8 w-1/2 rounded bg-slate-200" />
        <div class="h-4 w-3/4 rounded bg-slate-200" />
        <div class="h-4 w-2/3 rounded bg-slate-200" />
      </div>
    </div>

    <!-- Fout -->
    <div v-else-if="fout" class="mx-auto max-w-3xl px-6 py-24">
      <div role="alert" class="rounded-xl border border-red-200 bg-red-50 p-6 text-red-700">
        {{ fout }}
      </div>
      <RouterLink to="/teams" class="mt-6 inline-block font-medium text-robo-orange hover:underline">
        &larr; Terug naar teams
      </RouterLink>
    </div>

    <template v-else-if="team">
      <!-- Hero -->
      <section class="relative overflow-hidden py-20 text-white" :style="heroStyle">
        <div class="absolute inset-0 bg-robo-dark/75" aria-hidden="true" />
        <div class="relative z-10 mx-auto max-w-3xl px-6">
          <RouterLink to="/teams" class="mb-6 inline-block text-sm text-slate-400 hover:text-white">
            &larr; Alle teams
          </RouterLink>
          <div class="flex items-start gap-4">
            <div>
              <h1 class="mb-2 text-4xl font-black md:text-5xl">{{ team.naam }}</h1>
              <span
                class="inline-block rounded-full px-3 py-1 text-xs font-semibold"
                :class="statusKleur[team.status] ?? 'bg-slate-100 text-slate-700'"
              >
                {{ team.status_label }}
              </span>
            </div>
          </div>
        </div>
      </section>

      <!-- Robots -->
      <section class="bg-white py-16" aria-labelledby="robots-title">
        <div class="mx-auto max-w-3xl px-6">
          <h2 id="robots-title" class="mb-8 text-2xl font-black text-robo-dark">Robots</h2>

          <p v-if="!team.robots.length" class="text-slate-500">
            Dit team heeft nog geen robots ingeschreven.
          </p>

          <ul v-else class="space-y-6" role="list">
            <li
              v-for="robot in team.robots"
              :key="robot.id"
              class="rounded-xl border border-slate-200 p-6"
            >
              <div class="mb-2 flex flex-wrap items-center gap-3">
                <h3 class="text-xl font-bold text-robo-dark">{{ robot.naam }}</h3>
                <span class="rounded-full bg-robo-orange/10 px-3 py-0.5 text-xs font-bold text-robo-orange">
                  {{ robot.gewichtsklasse_label }}
                </span>
                <span class="rounded-full bg-slate-100 px-3 py-0.5 text-xs font-medium text-slate-600">
                  {{ robotStatusLabel[robot.status] ?? robot.status }}
                </span>
              </div>
              <p v-if="robot.beschrijving" class="text-slate-600">{{ robot.beschrijving }}</p>
            </li>
          </ul>
        </div>
      </section>
    </template>
  </main>
</template>
