<script setup lang="ts">
import { applyForTeamMembership, getTeam } from '@/api'
import { useAuth } from '@/composables/useAuth'
import type { Team } from '@/types/api'
import { onMounted, ref } from 'vue'
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
          <div class="flex flex-col gap-6 md:flex-row md:items-start">
            <img
              v-if="team.foto"
              :src="team.foto.url"
              :alt="team.foto.alt_tekst ?? `Teamfoto van ${team.naam}`"
              class="h-36 w-36 rounded-xl border border-white/20 object-cover shadow-lg"
            />
            <div
              v-else
              class="flex h-36 w-36 items-center justify-center rounded-xl border border-dashed border-white/30 bg-white/10 text-xs font-medium text-slate-300"
            >
              Geen teamfoto
            </div>
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

      <!-- Team info -->
      <section class="bg-white py-16" aria-labelledby="team-info-title">
        <div class="mx-auto max-w-3xl px-6">
          <h2 id="team-info-title" class="mb-8 text-2xl font-black text-robo-dark">Teaminformatie</h2>

          <article class="mb-8 rounded-xl border border-slate-200 p-6">
            <h3 class="mb-2 text-lg font-bold text-robo-dark">Lid worden van dit team</h3>

            <p v-if="!auth.isAuthenticated.value" class="text-slate-600">
              Log in om een lidmaatschapsaanvraag naar de teamcaptain te sturen.
            </p>

            <form v-else class="space-y-3" @submit.prevent="verstuurLidmaatschapAanvraag">
              <textarea
                v-model="aanvraagMelding"
                rows="3"
                maxlength="500"
                placeholder="Korte motivatie (optioneel)"
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-slate-800"
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

          <div class="grid gap-8 md:grid-cols-[1.2fr_0.8fr]">
            <article class="rounded-xl border border-slate-200 p-6">
              <h3 class="mb-3 text-lg font-bold text-robo-dark">Volledige beschrijving</h3>
              <p v-if="team.beschrijving" class="whitespace-pre-wrap text-slate-700">{{ team.beschrijving }}</p>
              <p v-else class="text-slate-500">Dit team heeft nog geen uitgebreide beschrijving geplaatst.</p>
            </article>

            <article class="space-y-4 rounded-xl border border-slate-200 p-6">
              <h3 class="text-lg font-bold text-robo-dark">Team captain</h3>
              <div class="flex items-center gap-4">
                <img
                  v-if="team.captain_foto"
                  :src="team.captain_foto.url"
                  :alt="team.captain_foto.alt_tekst ?? `Captain van ${team.naam}`"
                  class="h-16 w-16 rounded-full border border-slate-200 object-cover"
                  loading="lazy"
                />
                <div
                  v-else
                  class="flex h-16 w-16 items-center justify-center rounded-full border border-dashed border-slate-300 bg-slate-50 text-xs text-slate-500"
                >
                  Geen foto
                </div>
                <div>
                  <p class="font-semibold text-robo-dark">{{ team.captain.naam }}</p>
                  <p class="text-sm text-slate-500">Captain</p>
                </div>
              </div>

              <h3 class="pt-2 text-lg font-bold text-robo-dark">Teamleden</h3>
              <dl class="space-y-1 text-sm text-slate-700">
                <div class="flex justify-between gap-3">
                  <dt>Volwassenen</dt>
                  <dd class="font-semibold">{{ team.leden.volwassenen }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                  <dt>Kinderen</dt>
                  <dd class="font-semibold">{{ team.leden.kinderen }}</dd>
                </div>
                <div class="flex justify-between gap-3 border-t border-slate-200 pt-2">
                  <dt>Totaal</dt>
                  <dd class="font-semibold">{{ team.leden.totaal }}</dd>
                </div>
              </dl>
            </article>
          </div>

          <article class="mt-8 rounded-xl border border-slate-200 p-6">
            <h3 class="mb-4 text-lg font-bold text-robo-dark">Ledenfoto's</h3>

            <p v-if="!team.leden_fotos.length" class="text-slate-500">
              Nog geen ledenfoto's beschikbaar.
            </p>

            <ul v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3" role="list">
              <li v-for="foto in team.leden_fotos" :key="foto.id">
                <img
                  :src="foto.url"
                  :alt="foto.alt_tekst ?? `Ledenfoto van ${team.naam}`"
                  class="h-44 w-full rounded-lg border border-slate-200 object-cover"
                  loading="lazy"
                />
              </li>
            </ul>
          </article>
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

      <!-- Team updates -->
      <section class="bg-slate-50 py-16" aria-labelledby="team-updates-title">
        <div class="mx-auto max-w-3xl px-6">
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
