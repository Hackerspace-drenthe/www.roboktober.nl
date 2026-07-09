<script setup lang="ts">
import { getEditionCompetitionLeaderboard, getEditions } from '@/api'
import type { Edition, EditionCompetitionLeaderboard } from '@/types/api'
import { onMounted, ref, watch } from 'vue'

const editions = ref<Edition[]>([])
const selectedEditionId = ref<number | null>(null)
const leaderboard = ref<EditionCompetitionLeaderboard | null>(null)
const loading = ref(false)
const error = ref('')

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

async function loadEditions(): Promise<void> {
  editions.value = await getEditions()
  const eersteEditie = editions.value[0]

  if (eersteEditie && selectedEditionId.value === null) {
    selectedEditionId.value = eersteEditie.id
  }
}

async function loadLeaderboard(): Promise<void> {
  if (!selectedEditionId.value) {
    leaderboard.value = null
    return
  }

  loading.value = true
  error.value = ''

  try {
    leaderboard.value = await getEditionCompetitionLeaderboard(selectedEditionId.value)
  } catch {
    error.value = 'Competitieklassement laden mislukt.'
    leaderboard.value = null
  } finally {
    loading.value = false
  }
}

watch(selectedEditionId, async () => {
  await loadLeaderboard()
})

onMounted(async () => {
  try {
    await loadEditions()
    await loadLeaderboard()
  } catch {
    error.value = 'Edities laden mislukt.'
  }
})
</script>

<template>
  <main class="mx-auto min-h-[70vh] max-w-6xl px-6 py-12">
    <header class="mb-8">
      <h1 class="text-3xl font-black text-white">Competitieklassement</h1>
      <p class="mt-2 text-slate-300">
        Per editie zie je de puntentelling per categorie en de beste robots.
      </p>
    </header>

    <section class="mb-6 rounded-xl border border-white/10 bg-robo-dark/60 p-4">
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
    </section>

    <p v-if="error" class="mb-6 rounded border border-red-500/40 bg-red-950/30 px-3 py-2 text-sm text-red-200">
      {{ error }}
    </p>

    <section v-if="loading" class="rounded-xl border border-white/10 bg-robo-dark/60 px-4 py-6 text-slate-300">
      Klassement laden...
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
  </main>
</template>
