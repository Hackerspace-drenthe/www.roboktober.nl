<script setup lang="ts">
import { getEditionCompetitionLeaderboard, getEditions, getTeams } from '@/api'
import { useAnalytics } from '@/composables/useAnalytics'
import type { Edition, EditionCompetitionLeaderboard, Team } from '@/types/api'
import { computed, onMounted, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import headerImage from '@/assets/headers/header-teams.png'

const route = useRoute()
const router = useRouter()
const analytics = useAnalytics()

const actieveTab = computed<'teams' | 'competitie'>(() => {
  return route.name === 'teams-competitie' ? 'competitie' : 'teams'
})

const teams = ref<Team[]>([])
const laden = ref(true)
const fout = ref<string | null>(null)
const zoekterm = ref('')
const statusFilter = ref<'all' | 'approved' | 'pending' | 'rejected'>('all')
const gewichtFilter = ref<'all' | 'antweight' | 'beetleweight' | 'featherweight'>('all')
const sortering = ref<'naam-asc' | 'naam-desc' | 'robots-desc'>('naam-asc')

const editions = ref<Edition[]>([])
const selectedEditionId = ref<number | null>(null)
const leaderboard = ref<EditionCompetitionLeaderboard | null>(null)
const leaderboardCache = ref<Record<number, EditionCompetitionLeaderboard>>({})
const competitionLoading = ref(false)
const competitionError = ref('')

const heroStyle = {
  backgroundImage: `url(${headerImage})`,
  backgroundSize: 'cover',
  backgroundPosition: 'center',
}

function teamVisualMedia(team: Team) {
  if (team.foto) {
    return team.foto
  }

  const robotMetFoto = team.robots.find((robot) => robot.foto !== null)
  return robotMetFoto?.foto ?? null
}

function robotsMetFoto(team: Team) {
  return team.robots.filter((robot) => robot.foto !== null)
}

const gefilterdeTeams = computed(() => {
  const zoekWaarde = zoekterm.value.trim().toLowerCase()

  const basis = teams.value.filter((team) => {
    const statusMatch = statusFilter.value === 'all' || team.status === statusFilter.value

    const gewichtMatch = gewichtFilter.value === 'all'
      || team.robots.some((robot) => robot.gewichtsklasse === gewichtFilter.value)

    const zoekMatch = zoekWaarde === ''
      || team.naam.toLowerCase().includes(zoekWaarde)
      || team.robots.some((robot) => robot.naam.toLowerCase().includes(zoekWaarde))

    return statusMatch && gewichtMatch && zoekMatch
  })

  return [...basis].sort((a, b) => {
    if (sortering.value === 'naam-desc') {
      return b.naam.localeCompare(a.naam, 'nl')
    }

    if (sortering.value === 'robots-desc') {
      return b.robots.length - a.robots.length
    }

    return a.naam.localeCompare(b.naam, 'nl')
  })
})

function formatDatumTijd(iso: string | null): string {
  if (!iso) {
    return 'Tijd volgt nog'
  }

  return new Date(iso).toLocaleString('nl-NL', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

async function loadTeams(): Promise<void> {
  try {
    teams.value = await getTeams()
  } catch {
    fout.value = 'Teams konden niet worden geladen. Probeer het later opnieuw.'
  } finally {
    laden.value = false
  }
}

async function loadEditions(): Promise<void> {
  try {
    editions.value = await getEditions()
    const eersteEditie = editions.value[0]

    if (eersteEditie && selectedEditionId.value === null) {
      selectedEditionId.value = eersteEditie.id
    }
  } catch {
    competitionError.value = 'Edities laden mislukt.'
  }
}

async function loadLeaderboard(): Promise<void> {
  if (!selectedEditionId.value) {
    leaderboard.value = null
    return
  }

  const cachedResult = leaderboardCache.value[selectedEditionId.value]
  if (cachedResult) {
    leaderboard.value = cachedResult
    competitionError.value = ''
    return
  }

  competitionLoading.value = true
  competitionError.value = ''

  try {
    const result = await getEditionCompetitionLeaderboard(selectedEditionId.value)
    leaderboard.value = result
    leaderboardCache.value[selectedEditionId.value] = result
  } catch {
    competitionError.value = 'Competitieklassement laden mislukt.'
    leaderboard.value = null
  } finally {
    competitionLoading.value = false
  }
}

async function refreshLeaderboard(): Promise<void> {
  if (!selectedEditionId.value) {
    return
  }

  void analytics.track('click', {
    eventName: 'refresh_leaderboard',
    pagePath: route.path,
    routeName: typeof route.name === 'string' ? route.name : undefined,
    payload: {
      edition_id: selectedEditionId.value,
    },
  }).catch(() => {
    // Analytics is best-effort and should not block interactions.
  })

  delete leaderboardCache.value[selectedEditionId.value]
  await loadLeaderboard()
}

async function openCompetitieTab(): Promise<void> {
  void analytics.track('click', {
    eventName: 'open_competitie_tab',
    pagePath: route.path,
    routeName: typeof route.name === 'string' ? route.name : undefined,
  }).catch(() => {
    // Analytics is best-effort and should not block interactions.
  })

  await router.push('/teams/competitie')
}

watch(selectedEditionId, async () => {
  if (actieveTab.value === 'competitie') {
    await loadLeaderboard()
  }
})

watch(actieveTab, async (tab, previousTab) => {
  if (tab !== previousTab) {
    void analytics.track('tab_switch', {
      eventName: `${previousTab ?? 'unknown'}->${tab}`,
      pagePath: route.path,
      routeName: typeof route.name === 'string' ? route.name : undefined,
      payload: {
        from: previousTab ?? null,
        to: tab,
      },
    }).catch(() => {
      // Analytics is best-effort and should not block interactions.
    })
  }

  if (tab === 'competitie' && !leaderboard.value && !competitionLoading.value) {
    await loadLeaderboard()
  }
})

onMounted(async () => {
  await Promise.all([loadTeams(), loadEditions()])

  if (actieveTab.value === 'competitie') {
    await loadLeaderboard()
  }
})
</script>

<template>
  <main id="main-content">
    <section class="relative overflow-hidden py-20 text-white" :style="heroStyle">
      <div class="absolute inset-0 bg-robo-dark/75" aria-hidden="true" />
      <div class="relative z-10 mx-auto max-w-4xl px-6 text-center">
        <h1 class="mb-4 text-4xl font-black md:text-5xl">Teams & Competitie</h1>
        <p v-if="actieveTab === 'teams'" class="text-lg text-slate-300">
          Bekijk alle ingeschreven teams en robots van Roboktober.
        </p>
        <p v-else class="text-lg text-slate-300">
          Volg de actuele stand per editie, categorie en robot.
        </p>

        <div class="mx-auto mt-8 inline-flex rounded-xl border border-white/15 bg-robo-dark/70 p-1" role="tablist" aria-label="Teams en competitie tabs">
          <RouterLink
            to="/teams"
            role="tab"
            :aria-selected="actieveTab === 'teams'"
            class="rounded-lg px-5 py-2 text-sm font-semibold transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-robo-orange"
            :class="actieveTab === 'teams' ? 'bg-white text-robo-dark shadow-sm' : 'text-slate-200 hover:bg-white/10'"
          >
            Teams
          </RouterLink>
          <RouterLink
            to="/teams/competitie"
            role="tab"
            :aria-selected="actieveTab === 'competitie'"
            class="rounded-lg px-5 py-2 text-sm font-semibold transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-robo-orange"
            :class="actieveTab === 'competitie' ? 'bg-white text-robo-dark shadow-sm' : 'text-slate-200 hover:bg-white/10'"
          >
            Competitie
          </RouterLink>
        </div>
      </div>
    </section>

    <Transition name="tab-fade" mode="out-in">
      <section v-if="actieveTab === 'teams'" key="teams" class="bg-white py-20" aria-labelledby="teams-title">
      <div class="mx-auto max-w-4xl px-6">
        <h2 id="teams-title" class="sr-only">Teamoverzicht</h2>

        <div class="mb-8 rounded-xl border border-robo-orange/20 bg-robo-orange/5 p-4 text-center">
          <p class="text-sm text-slate-700">
            Wil je direct de stand bekijken?
            <button
              type="button"
              class="ml-1 font-semibold text-robo-orange underline decoration-transparent transition hover:decoration-robo-orange"
              @click="openCompetitieTab"
            >
              Open Competitie-tab
            </button>
          </p>
        </div>

        <div class="mb-8 grid gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4 md:grid-cols-4">
          <input
            v-model="zoekterm"
            type="search"
            placeholder="Zoek team of robot"
            class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700"
          />

          <select v-model="statusFilter" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700">
            <option value="all">Alle statussen</option>
            <option value="approved">Goedgekeurd</option>
            <option value="pending">In beoordeling</option>
            <option value="rejected">Afgewezen</option>
          </select>

          <select v-model="gewichtFilter" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700">
            <option value="all">Alle gewichtsklassen</option>
            <option value="antweight">Antweight</option>
            <option value="beetleweight">Beetleweight</option>
            <option value="featherweight">Featherweight</option>
          </select>

          <select v-model="sortering" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700">
            <option value="naam-asc">Naam A-Z</option>
            <option value="naam-desc">Naam Z-A</option>
            <option value="robots-desc">Meeste robots</option>
          </select>
        </div>

        <p class="mb-6 text-sm text-slate-500">{{ gefilterdeTeams.length }} team(s) gevonden</p>

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

        <div v-else-if="fout" role="alert" class="rounded-xl border border-red-200 bg-red-50 p-6 text-red-700">
          {{ fout }}
        </div>

        <div v-else-if="gefilterdeTeams.length === 0" class="py-12 text-center text-slate-500">
          <p class="text-lg">Nog geen teams ingeschreven.</p>
          <RouterLink to="/aanmelden" class="mt-4 inline-block font-medium text-robo-orange hover:underline">
            Schrijf je in als eerste team &rarr;
          </RouterLink>
        </div>

        <ul v-else class="grid gap-6 md:grid-cols-2" role="list">
          <li v-for="team in gefilterdeTeams" :key="team.id">
            <RouterLink
              :to="`/teams/${team.id}`"
              class="block overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition hover:border-robo-orange/40 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-robo-orange"
            >
              <img
                v-if="teamVisualMedia(team)"
                :src="teamVisualMedia(team)?.url ?? ''"
                :alt="teamVisualMedia(team)?.alt_tekst ?? `Team- of robotfoto van ${team.naam}`"
                class="h-48 w-full object-cover"
                loading="lazy"
              />
              <div
                v-else
                class="flex h-48 w-full items-center justify-center bg-slate-100 text-sm font-medium text-slate-400"
              >
                Geen team- of robotfoto
              </div>

              <div class="p-6">
                <h3 class="mb-2 text-lg font-bold text-robo-dark">{{ team.naam }}</h3>
                <p v-if="team.robots.length" class="text-sm text-slate-500">
                  {{ team.robots.length }} robot{{ team.robots.length !== 1 ? 's' : '' }}
                </p>
                <p v-else class="text-sm text-slate-400">Nog geen robots ingeschreven</p>

                <ul v-if="robotsMetFoto(team).length" class="mt-4 flex items-center gap-2" role="list">
                  <li
                    v-for="robot in robotsMetFoto(team).slice(0, 3)"
                    :key="`robot-thumb-${team.id}-${robot.id}`"
                    class="h-10 w-10 overflow-hidden rounded-full border border-slate-200"
                  >
                    <img
                      :src="robot.foto?.url ?? ''"
                      :alt="robot.foto?.alt_tekst ?? `Robotfoto van ${robot.naam}`"
                      class="h-full w-full object-cover"
                      loading="lazy"
                    />
                  </li>
                  <li
                    v-if="robotsMetFoto(team).length > 3"
                    class="rounded-full border border-slate-200 bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-500"
                  >
                    +{{ robotsMetFoto(team).length - 3 }}
                  </li>
                </ul>
              </div>
            </RouterLink>
          </li>
        </ul>

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

      <section v-else key="competitie" class="bg-robo-dark py-16">
      <div class="mx-auto max-w-6xl px-6">
        <section class="mb-6 rounded-xl border border-white/10 bg-robo-dark/60 p-4">
          <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div class="w-full">
              <label for="edition_id" class="mb-2 block text-sm font-semibold text-slate-200">Editie</label>
              <select
                id="edition_id"
                v-model.number="selectedEditionId"
                class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white outline-none ring-robo-orange/70 transition focus:ring-2"
              >
                <option v-for="edition in editions" :key="edition.id" :value="edition.id">
                  {{ edition.naam }} · {{ edition.location?.full_address ?? edition.location?.name ?? '-' }}
                </option>
              </select>
            </div>

            <button
              type="button"
              class="rounded-lg border border-white/20 bg-white/5 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-robo-orange"
              @click="refreshLeaderboard"
            >
              Ververs klassement
            </button>
          </div>
        </section>

        <p v-if="competitionError" class="mb-6 rounded border border-red-500/40 bg-red-950/30 px-3 py-2 text-sm text-red-200">
          {{ competitionError }}
        </p>

        <section v-if="competitionLoading" class="space-y-4" aria-busy="true" aria-label="Klassement laden">
          <div class="animate-pulse rounded-xl border border-white/10 bg-robo-dark/60 p-5">
            <div class="mb-3 h-6 w-1/3 rounded bg-white/10" />
            <div class="h-4 w-2/3 rounded bg-white/10" />
          </div>
          <div class="grid gap-4 md:grid-cols-2">
            <div v-for="n in 2" :key="n" class="animate-pulse rounded-xl border border-white/10 bg-robo-dark/60 p-5">
              <div class="mb-3 h-5 w-1/2 rounded bg-white/10" />
              <div class="h-4 w-3/4 rounded bg-white/10" />
            </div>
          </div>
        </section>

        <template v-else-if="leaderboard">
          <section class="mb-6 rounded-xl border border-white/10 bg-robo-dark/60 p-5">
            <h2 class="text-xl font-black text-white">Totaalklassement</h2>
            <p v-if="leaderboard.overall.length === 0" class="mt-2 text-slate-300">
              Nog geen punten geregistreerd voor deze editie.
            </p>

            <ol v-else class="mt-4 grid gap-2 sm:grid-cols-2">
              <li v-for="item in leaderboard.overall" :key="item.robot.id" class="rounded-lg border border-white/10 bg-slate-900/50 px-3 py-2">
                <p class="font-semibold text-white">#{{ item.positie }} · {{ item.robot.naam }}</p>
                <p class="text-sm text-slate-300">{{ item.robot.team?.naam ?? 'Onbekend team' }} · {{ item.punten }} punten</p>
              </li>
            </ol>
          </section>

          <section class="grid gap-4 md:grid-cols-2">
            <article v-for="category in leaderboard.categories" :key="category.id" class="rounded-xl border border-white/10 bg-robo-dark/60 p-5">
              <h3 class="text-lg font-black text-white">{{ category.naam }}</h3>
              <p v-if="category.omschrijving" class="mt-1 text-sm text-slate-300">{{ category.omschrijving }}</p>
              <p class="mt-2 text-xs text-slate-400">Battles: {{ category.battles_count }}</p>

              <ul v-if="category.battles.length > 0" class="mt-3 space-y-1 text-xs text-slate-300">
                <li v-for="battle in category.battles" :key="battle.id">
                  {{ battle.naam }} ({{ battle.battle_mode }}) · {{ formatDatumTijd(battle.scheduled_at) }}
                </li>
              </ul>
              <p v-else class="mt-3 text-xs text-slate-400">Nog geen wedstrijdtijden ingepland.</p>

              <div class="mt-3 rounded-lg border border-amber-500/30 bg-amber-500/10 px-3 py-2">
                <p class="text-xs uppercase tracking-wide text-amber-200">Beste robot</p>
                <p v-if="category.winner" class="font-semibold text-white">
                  {{ category.winner.robot.naam }} · {{ category.winner.punten }} punten
                </p>
                <p v-else class="text-sm text-slate-300">Nog geen uitslag</p>
              </div>

              <ol v-if="category.ranking.length > 0" class="mt-3 space-y-2">
                <li v-for="item in category.ranking" :key="item.robot.id" class="rounded-md border border-white/10 bg-slate-900/40 px-3 py-2 text-sm">
                  <span class="font-semibold text-white">#{{ item.positie }} {{ item.robot.naam }}</span>
                  <span class="text-slate-300"> · {{ item.punten }} punten</span>
                </li>
              </ol>
              <p v-else class="mt-3 text-sm text-slate-300">Nog geen scores in deze categorie.</p>
            </article>
          </section>
        </template>
      </div>
      </section>
    </Transition>
  </main>
</template>

<style scoped>
.tab-fade-enter-active,
.tab-fade-leave-active {
  transition: opacity 180ms ease, transform 180ms ease;
}

.tab-fade-enter-from,
.tab-fade-leave-to {
  opacity: 0;
  transform: translateY(8px);
}
</style>
