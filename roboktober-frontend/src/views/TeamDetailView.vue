<script setup lang="ts">
import { applyForTeamMembership, getTeam, voteRobot } from '@/api'
import { useAuth } from '@/composables/useAuth'
import type { Team } from '@/types/api'
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import headerImage from '@/assets/headers/header-team-detail.png'

const route = useRoute()
const router = useRouter()
const auth = useAuth()
const team = ref<Team | null>(null)
const laden = ref(true)
const fout = ref<string | null>(null)
const aanvraagMelding = ref('')
const aanvraagStatus = ref<'idle' | 'opslaan'>('idle')
const aanvraagFout = ref('')
const aanvraagSucces = ref('')
const voteStatus = ref<Record<number, 'idle' | 'saving'>>({})
const voteError = ref<Record<number, string>>({})
const voteSuccess = ref<Record<number, string>>({})
const sterren = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]

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

const captainFoto = computed(() => team.value?.captain.foto ?? team.value?.captain_foto ?? null)

const captainInitialen = computed(() => {
  const naam = team.value?.captain?.naam?.trim() ?? ''
  if (naam === '') {
    return 'RC'
  }

  const delen = naam.split(/\s+/).filter(Boolean)
  if (delen.length === 1) {
    return (delen[0] ?? 'RC').slice(0, 2).toUpperCase()
  }

  const eerste = delen[0]?.[0] ?? 'R'
  const laatste = delen[delen.length - 1]?.[0] ?? 'C'

  return (eerste + laatste).toUpperCase()
})

function formatDatum(iso: string | null): string {
  if (!iso) return ''
  return new Date(iso).toLocaleDateString('nl-NL', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  })
}

onMounted(async () => {
  const id = Number(route.params.id)

  if (!auth.initialized.value) {
    await auth.initAuth()
  }

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

async function verstuurLidmaatschapAanvraag(): Promise<void> {
  if (!team.value) {
    return
  }

  aanvraagStatus.value = 'opslaan'
  aanvraagFout.value = ''
  aanvraagSucces.value = ''

  try {
    await applyForTeamMembership(team.value.id, aanvraagMelding.value.trim() || undefined)
    aanvraagSucces.value = 'Aanvraag verstuurd. De teamcaptain kan deze nu beoordelen.'
    aanvraagMelding.value = ''
  } catch (error: unknown) {
    if (
      error !== null &&
      typeof error === 'object' &&
      'response' in error &&
      error.response !== null &&
      typeof error.response === 'object' &&
      'data' in error.response &&
      error.response.data !== null &&
      typeof error.response.data === 'object' &&
      'message' in error.response.data &&
      typeof error.response.data.message === 'string'
    ) {
      aanvraagFout.value = error.response.data.message
    } else {
      aanvraagFout.value = 'Aanvraag versturen mislukt. Controleer je rechten of probeer later opnieuw.'
    }
  } finally {
    aanvraagStatus.value = 'idle'
  }
}

async function stemOpRobot(robotId: number, stars: number): Promise<void> {
  if (!team.value) {
    return
  }

  voteStatus.value[robotId] = 'saving'
  voteError.value[robotId] = ''
  voteSuccess.value[robotId] = ''

  try {
    const result = await voteRobot(robotId, stars)
    const robot = team.value.robots.find((item) => item.id === robotId)

    if (robot) {
      robot.awesomeness_score = result.awesomeness_score
      robot.awesomeness_votes_count = result.awesomeness_votes_count
    }

    voteSuccess.value[robotId] = `Je stem (${result.my_stars} sterren) is opgeslagen.`
  } catch (error: unknown) {
    if (
      error !== null &&
      typeof error === 'object' &&
      'response' in error &&
      error.response !== null &&
      typeof error.response === 'object' &&
      'data' in error.response &&
      error.response.data !== null &&
      typeof error.response.data === 'object' &&
      'message' in error.response.data &&
      typeof error.response.data.message === 'string'
    ) {
      voteError.value[robotId] = error.response.data.message
    } else {
      voteError.value[robotId] = 'Stem opslaan mislukt. Probeer het opnieuw.'
    }
  } finally {
    voteStatus.value[robotId] = 'idle'
  }
}
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
      <section class="relative overflow-hidden text-white" :style="heroStyle">
        <div class="absolute inset-0 bg-gradient-to-br from-robo-dark/90 via-robo-dark/80 to-black/75" aria-hidden="true" />
        <div class="relative z-10 mx-auto max-w-6xl px-6 py-16 md:py-20">
          <RouterLink to="/teams" class="mb-6 inline-block text-sm font-semibold text-slate-300 hover:text-white">
            &larr; Alle teams
          </RouterLink>

          <div class="grid gap-10 lg:grid-cols-[1.4fr_0.9fr] lg:items-end">
            <div>
              <div class="mb-5 flex flex-wrap items-center gap-3">
                <span
                  class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                  :class="statusKleur[team.status] ?? 'bg-slate-100 text-slate-700'"
                >
                  {{ team.status_label }}
                </span>
                <span class="inline-flex rounded-full border border-white/25 bg-white/10 px-3 py-1 text-xs font-semibold text-slate-200">
                  {{ team.leden.totaal }} teamleden
                </span>
                <span class="inline-flex rounded-full border border-white/25 bg-white/10 px-3 py-1 text-xs font-semibold text-slate-200">
                  {{ team.robots.length }} robots
                </span>
              </div>

              <h1 class="text-4xl font-black leading-tight md:text-6xl">{{ team.naam }}</h1>

              <p v-if="team.beschrijving" class="mt-4 max-w-2xl whitespace-pre-wrap text-slate-200/95">
                {{ team.beschrijving }}
              </p>
              <p v-else class="mt-4 max-w-2xl text-slate-300/90">
                Dit team werkt hard aan hun robots en bouwt stap voor stap richting de arena.
              </p>
            </div>

            <div class="rounded-2xl border border-white/20 bg-white/10 p-5 backdrop-blur">
              <div class="flex items-center gap-4">
                <img
                  v-if="captainFoto"
                  :src="captainFoto.url"
                  :alt="captainFoto.alt_tekst ?? `Captain van ${team.naam}`"
                  class="h-16 w-16 rounded-full border border-white/35 object-cover"
                  loading="lazy"
                />
                <div
                  v-else
                  class="flex h-16 w-16 items-center justify-center rounded-full border border-white/35 bg-white/15 text-sm font-black text-white"
                >
                  {{ captainInitialen }}
                </div>
                <div>
                  <p class="text-xs uppercase tracking-wide text-slate-300">Team captain</p>
                  <p class="text-lg font-bold text-white">{{ team.captain.naam }}</p>
                </div>
              </div>

              <dl class="mt-5 space-y-2 text-sm text-slate-100">
                <div class="flex justify-between gap-3"><dt>Volwassenen</dt><dd class="font-semibold">{{ team.leden.volwassenen }}</dd></div>
                <div class="flex justify-between gap-3"><dt>Kinderen</dt><dd class="font-semibold">{{ team.leden.kinderen }}</dd></div>
                <div class="flex justify-between gap-3 border-t border-white/20 pt-2"><dt>Totaal</dt><dd class="font-semibold">{{ team.leden.totaal }}</dd></div>
              </dl>
            </div>
          </div>
        </div>
      </section>

      <section class="bg-white py-14">
        <div class="mx-auto grid max-w-6xl gap-8 px-6 lg:grid-cols-[1.1fr_1fr]">
          <article class="rounded-2xl border border-slate-200 p-6 shadow-sm">
            <h2 class="mb-3 text-xl font-black text-robo-dark">Word lid van dit team</h2>

            <p v-if="!auth.isAuthenticated.value" class="text-slate-600">
              Log in om een lidmaatschapsaanvraag naar de teamcaptain te sturen.
            </p>

            <form v-else class="space-y-3" @submit.prevent="verstuurLidmaatschapAanvraag">
              <textarea
                v-model="aanvraagMelding"
                rows="3"
                maxlength="500"
                placeholder="Korte motivatie (optioneel)"
                class="w-full rounded-xl border border-slate-300 px-3 py-2 text-slate-800"
              />

              <p v-if="aanvraagFout" class="rounded border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                {{ aanvraagFout }}
              </p>
              <p v-if="aanvraagSucces" class="rounded border border-green-200 bg-green-50 px-3 py-2 text-sm text-green-700">
                {{ aanvraagSucces }}
              </p>

              <button
                type="submit"
                :disabled="aanvraagStatus === 'opslaan'"
                class="rounded-lg bg-robo-orange px-4 py-2 font-semibold text-white hover:bg-robo-orange-dark disabled:opacity-60"
              >
                {{ aanvraagStatus === 'opslaan' ? 'Versturen...' : 'Lidmaatschap aanvragen' }}
              </button>
            </form>
          </article>

          <article class="rounded-2xl border border-slate-200 p-6 shadow-sm">
            <h2 class="mb-3 text-xl font-black text-robo-dark">Teamgalerij</h2>
            <div v-if="team.foto" class="mb-4 overflow-hidden rounded-xl border border-slate-200">
              <img
                :src="team.foto.url"
                :alt="team.foto.alt_tekst ?? `Teamfoto van ${team.naam}`"
                class="h-56 w-full object-cover"
              />
            </div>

            <p v-if="!team.leden_fotos.length" class="text-slate-500">
              Nog geen ledenfoto's beschikbaar.
            </p>

            <ul v-else class="grid grid-cols-2 gap-3 sm:grid-cols-3" role="list">
              <li v-for="foto in team.leden_fotos" :key="foto.id" class="overflow-hidden rounded-lg border border-slate-200">
                <img
                  :src="foto.url"
                  :alt="foto.alt_tekst ?? `Ledenfoto van ${team.naam}`"
                  class="h-28 w-full object-cover transition hover:scale-105"
                  loading="lazy"
                />
              </li>
            </ul>
          </article>
        </div>
      </section>

      <section class="bg-slate-50 py-16" aria-labelledby="robots-title">
        <div class="mx-auto max-w-6xl px-6">
          <h2 id="robots-title" class="mb-8 text-3xl font-black text-robo-dark">Robots in de pit</h2>

          <p v-if="!team.robots.length" class="text-slate-500">
            Dit team heeft nog geen robots ingeschreven.
          </p>

          <ul v-else class="grid gap-6 md:grid-cols-2" role="list">
            <li
              v-for="robot in team.robots"
              :key="robot.id"
              class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
            >
              <div class="mb-3 flex flex-wrap items-center gap-2">
                <h3 class="text-2xl font-black text-robo-dark">{{ robot.naam }}</h3>
                <span class="rounded-full bg-robo-orange/10 px-3 py-0.5 text-xs font-bold text-robo-orange">
                  {{ robot.gewichtsklasse_label }}
                </span>
                <span class="rounded-full bg-slate-100 px-3 py-0.5 text-xs font-medium text-slate-600">
                  {{ robotStatusLabel[robot.status] ?? robot.status }}
                </span>
              </div>

              <p v-if="robot.beschrijving" class="text-slate-600">{{ robot.beschrijving }}</p>

              <div class="mt-5 rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-sm font-semibold text-robo-dark">
                  Awesomeness score:
                  <span v-if="robot.awesomeness_votes_count > 0">{{ robot.awesomeness_score.toFixed(1) }}/10</span>
                  <span v-else>Nog geen stemmen</span>
                </p>
                <p class="text-xs text-slate-500">{{ robot.awesomeness_votes_count }} stem(men)</p>

                <div v-if="auth.isAuthenticated.value" class="mt-3">
                  <p class="mb-2 text-sm text-slate-700">Geef deze robot 1-10 sterren:</p>
                  <div class="flex flex-wrap gap-2">
                    <button
                      v-for="star in sterren"
                      :key="star"
                      type="button"
                      :disabled="voteStatus[robot.id] === 'saving'"
                      class="rounded-md border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:border-robo-orange hover:text-robo-orange disabled:cursor-not-allowed disabled:opacity-50"
                      @click="stemOpRobot(robot.id, star)"
                    >
                      {{ star }}★
                    </button>
                  </div>

                  <p v-if="voteError[robot.id]" class="mt-2 text-sm text-red-700">{{ voteError[robot.id] }}</p>
                  <p v-if="voteSuccess[robot.id]" class="mt-2 text-sm text-green-700">{{ voteSuccess[robot.id] }}</p>
                </div>

                <p v-else class="mt-3 text-sm text-slate-600">
                  Log in om op deze robot te stemmen.
                </p>
              </div>
            </li>
          </ul>
        </div>
      </section>

      <section class="bg-white py-16" aria-labelledby="team-updates-title">
        <div class="mx-auto max-w-6xl px-6">
          <h2 id="team-updates-title" class="mb-8 text-2xl font-black text-robo-dark">Voortgang van het team</h2>

          <p v-if="!team.updates?.length" class="text-slate-500">
            Dit team heeft nog geen voortgangsberichten geplaatst.
          </p>

          <ul v-else class="space-y-6" role="list">
            <li v-for="update in team.updates" :key="update.id" class="rounded-xl border border-slate-200 bg-white p-6">
              <header class="mb-4">
                <h3 class="text-xl font-bold text-robo-dark">{{ update.titel }}</h3>
                <p v-if="update.published_at" class="mt-1 text-sm text-slate-500">
                  {{ formatDatum(update.published_at) }}
                </p>
                <p v-if="update.excerpt" class="mt-2 text-slate-600">{{ update.excerpt }}</p>
              </header>

              <!-- eslint-disable-next-line vue/no-v-html -->
              <div
                v-if="update.content_format === 'html'"
                class="prose prose-slate max-w-none prose-a:text-robo-orange"
                v-html="update.content"
              />
              <pre v-else class="whitespace-pre-wrap font-sans text-slate-700">{{ update.content }}</pre>

              <ul v-if="update.afbeeldingen.length" class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2" role="list">
                <li v-for="afbeelding in update.afbeeldingen" :key="afbeelding.id">
                  <img
                    :src="afbeelding.url"
                    :alt="afbeelding.alt_tekst ?? `Afbeelding bij ${update.titel}`"
                    class="h-48 w-full rounded-lg border border-slate-200 object-cover"
                    loading="lazy"
                  />
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </section>
    </template>
  </main>
</template>
