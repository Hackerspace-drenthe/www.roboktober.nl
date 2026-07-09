<script setup lang="ts">
import {
  createAdminCompetitionBattle,
  createAdminCompetitionCategory,
  getAdminEditionCompetition,
  getEditions,
  upsertAdminCompetitionBattleScores,
} from '@/api'
import type {
  AdminCompetitionBattleScoreEntryPayload,
  AdminEditionCompetitionData,
  CompetitionBattleMode,
  Edition,
} from '@/types/api'
import { onMounted, reactive, ref, watch } from 'vue'

const editions = ref<Edition[]>([])
const selectedEditionId = ref<number | null>(null)
const data = ref<AdminEditionCompetitionData | null>(null)
const loading = ref(false)
const error = ref('')
const success = ref('')

const nieuweCategorie = reactive({
  naam: '',
  omschrijving: '',
  volgorde: 0,
})

const battleForms = reactive<Record<number, {
  naam: string
  battle_mode: CompetitionBattleMode
  omschrijving: string
  scheduled_at: string
  volgorde: number
}>>({})
const scoreForms = reactive<Record<number, { robot_id: number | null; punten: number; opmerkingen: string }>>({})

async function loadEditions(): Promise<void> {
  editions.value = await getEditions()
  const eersteEditie = editions.value[0]

  if (eersteEditie && selectedEditionId.value === null) {
    selectedEditionId.value = eersteEditie.id
  }
}

async function loadCompetition(): Promise<void> {
  if (!selectedEditionId.value) {
    data.value = null
    return
  }

  loading.value = true
  error.value = ''

  try {
    data.value = await getAdminEditionCompetition(selectedEditionId.value)
  } catch {
    error.value = 'Competitiebeheer laden mislukt.'
    data.value = null
  } finally {
    loading.value = false
  }
}

async function addCategorie(): Promise<void> {
  if (!selectedEditionId.value || nieuweCategorie.naam.trim() === '') return

  error.value = ''
  success.value = ''

  try {
    const created = await createAdminCompetitionCategory(selectedEditionId.value, {
      naam: nieuweCategorie.naam.trim(),
      omschrijving: nieuweCategorie.omschrijving.trim() || undefined,
      volgorde: nieuweCategorie.volgorde,
    })

    if (data.value) {
      data.value.categories.push(created)
      data.value.categories.sort((a, b) => a.volgorde - b.volgorde || a.id - b.id)
    }

    nieuweCategorie.naam = ''
    nieuweCategorie.omschrijving = ''
    nieuweCategorie.volgorde = 0
    success.value = 'Categorie toegevoegd.'
  } catch {
    error.value = 'Toevoegen van categorie mislukt.'
  }
}

function ensureBattleForm(categoryId: number): void {
  if (!battleForms[categoryId]) {
    battleForms[categoryId] = {
      naam: '',
      battle_mode: 'solo',
      omschrijving: '',
      scheduled_at: '',
      volgorde: 0,
    }
  }
}

function formatDatumTijd(iso: string | null): string {
  if (!iso) {
    return 'Nog niet gepland'
  }

  return new Date(iso).toLocaleString('nl-NL', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

function ensureScoreForm(battleId: number): void {
  if (!scoreForms[battleId]) {
    scoreForms[battleId] = {
      robot_id: null,
      punten: 0,
      opmerkingen: '',
    }
  }
}

async function addBattle(categoryId: number): Promise<void> {
  ensureBattleForm(categoryId)
  const form = battleForms[categoryId]

  if (!form || form.naam.trim() === '') return

  error.value = ''
  success.value = ''

  try {
    const created = await createAdminCompetitionBattle(categoryId, {
      naam: form.naam.trim(),
      battle_mode: form.battle_mode,
      omschrijving: form.omschrijving.trim() || undefined,
      scheduled_at: form.scheduled_at || undefined,
      volgorde: form.volgorde,
    })

    const category = data.value?.categories.find((item) => item.id === categoryId)
    if (category) {
      category.battles.push(created)
      category.battles.sort((a, b) => a.volgorde - b.volgorde || a.id - b.id)
    }

    battleForms[categoryId] = {
      naam: '',
      battle_mode: 'solo',
      omschrijving: '',
      scheduled_at: '',
      volgorde: 0,
    }
    success.value = 'Battle toegevoegd.'
  } catch {
    error.value = 'Toevoegen van battle mislukt.'
  }
}

async function addScore(categoryId: number, battleId: number): Promise<void> {
  ensureScoreForm(battleId)
  const form = scoreForms[battleId]

  if (!form || !form.robot_id) return

  error.value = ''
  success.value = ''

  const entries: AdminCompetitionBattleScoreEntryPayload[] = [{
    robot_id: form.robot_id,
    punten: form.punten,
    opmerkingen: form.opmerkingen.trim() || undefined,
  }]

  try {
    const updatedBattle = await upsertAdminCompetitionBattleScores(battleId, entries)
    const category = data.value?.categories.find((item) => item.id === categoryId)

    if (category) {
      const idx = category.battles.findIndex((battle) => battle.id === battleId)
      if (idx >= 0) {
        category.battles[idx] = updatedBattle
      }
    }

    scoreForms[battleId] = {
      robot_id: null,
      punten: 0,
      opmerkingen: '',
    }
    success.value = 'Score opgeslagen.'
  } catch {
    error.value = 'Score opslaan mislukt (controleer battle_ready en editie-koppeling).'
  }
}

watch(selectedEditionId, async () => {
  await loadCompetition()
})

onMounted(async () => {
  try {
    await loadEditions()
    await loadCompetition()
  } catch {
    error.value = 'Edities laden mislukt.'
  }
})
</script>

<template>
  <main class="mx-auto min-h-[70vh] max-w-7xl px-6 py-12">
    <header class="mb-6">
      <h1 class="text-3xl font-black text-white">Admin · Competitie</h1>
      <p class="mt-2 text-slate-300">Beheer categorieen, battles en punten per editie.</p>
    </header>

    <p v-if="error" class="mb-4 rounded border border-red-500/40 bg-red-950/30 px-3 py-2 text-sm text-red-200">{{ error }}</p>
    <p v-if="success" class="mb-4 rounded border border-emerald-500/40 bg-emerald-950/30 px-3 py-2 text-sm text-emerald-200">{{ success }}</p>

    <section class="mb-6 rounded-xl border border-white/10 bg-robo-dark/60 p-4">
      <label for="admin-edition" class="mb-2 block text-sm font-semibold text-slate-200">Editie</label>
      <select
        id="admin-edition"
        v-model.number="selectedEditionId"
        class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white"
      >
        <option v-for="edition in editions" :key="edition.id" :value="edition.id">{{ edition.naam }}</option>
      </select>
    </section>

    <section class="mb-6 rounded-xl border border-white/10 bg-robo-dark/60 p-4">
      <h2 class="mb-3 text-lg font-bold text-white">Nieuwe categorie</h2>
      <div class="grid gap-3 md:grid-cols-4">
        <input v-model="nieuweCategorie.naam" placeholder="Naam" class="rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white">
        <input v-model="nieuweCategorie.omschrijving" placeholder="Omschrijving" class="rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white">
        <input v-model.number="nieuweCategorie.volgorde" type="number" min="0" class="rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white">
        <button class="rounded-lg bg-robo-orange px-4 py-2 font-bold text-white hover:bg-robo-orange-dark" @click="addCategorie">Categorie toevoegen</button>
      </div>
    </section>

    <section v-if="loading" class="rounded-xl border border-white/10 bg-robo-dark/60 p-4 text-slate-300">Laden...</section>

    <section v-else-if="data" class="space-y-5">
      <article v-for="category in data.categories" :key="category.id" class="rounded-xl border border-white/10 bg-robo-dark/60 p-5">
        <h3 class="text-xl font-black text-white">{{ category.naam }}</h3>
        <p v-if="category.omschrijving" class="mt-1 text-sm text-slate-300">{{ category.omschrijving }}</p>

        <div class="mt-4 rounded-lg border border-white/10 bg-slate-900/60 p-3">
          <p class="mb-2 text-sm font-semibold text-slate-200">Nieuwe battle in {{ category.naam }}</p>
          <div class="grid gap-2 md:grid-cols-6">
            <input
              :value="battleForms[category.id]?.naam ?? ''"
              class="rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white"
              placeholder="Battle naam"
              @focus="ensureBattleForm(category.id)"
              @input="ensureBattleForm(category.id); battleForms[category.id]!.naam = ($event.target as HTMLInputElement).value"
            >
            <select
              :value="battleForms[category.id]?.battle_mode ?? 'solo'"
              class="rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white"
              @focus="ensureBattleForm(category.id)"
              @change="ensureBattleForm(category.id); battleForms[category.id]!.battle_mode = ($event.target as HTMLSelectElement).value as CompetitionBattleMode"
            >
              <option value="solo">Solo</option>
              <option value="multi">Multi</option>
            </select>
            <input
              :value="battleForms[category.id]?.omschrijving ?? ''"
              class="rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white"
              placeholder="Omschrijving"
              @focus="ensureBattleForm(category.id)"
              @input="ensureBattleForm(category.id); battleForms[category.id]!.omschrijving = ($event.target as HTMLInputElement).value"
            >
            <input
              :value="battleForms[category.id]?.scheduled_at ?? ''"
              type="datetime-local"
              class="rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white"
              @focus="ensureBattleForm(category.id)"
              @input="ensureBattleForm(category.id); battleForms[category.id]!.scheduled_at = ($event.target as HTMLInputElement).value"
            >
            <input
              :value="battleForms[category.id]?.volgorde ?? 0"
              type="number"
              min="0"
              class="rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white"
              @focus="ensureBattleForm(category.id)"
              @input="ensureBattleForm(category.id); battleForms[category.id]!.volgorde = Number(($event.target as HTMLInputElement).value)"
            >
            <button class="rounded-lg bg-indigo-500 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-400" @click="addBattle(category.id)">Battle toevoegen</button>
          </div>
        </div>

        <div v-if="category.battles.length === 0" class="mt-3 text-sm text-slate-300">Nog geen battles.</div>

        <div v-for="battle in category.battles" :key="battle.id" class="mt-4 rounded-lg border border-white/10 bg-slate-900/50 p-4">
          <div class="mb-2 flex items-center justify-between">
            <h4 class="font-bold text-white">{{ battle.naam }} <span class="text-xs text-slate-400">({{ battle.battle_mode }})</span></h4>
            <span class="text-xs text-slate-400">Volgorde: {{ battle.volgorde }}</span>
          </div>
          <p class="mb-2 text-xs text-slate-400">Tijd: {{ formatDatumTijd(battle.scheduled_at) }}</p>

          <div class="grid gap-2 md:grid-cols-5">
            <select
              :value="scoreForms[battle.id]?.robot_id ?? ''"
              class="rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white"
              @focus="ensureScoreForm(battle.id)"
              @change="ensureScoreForm(battle.id); scoreForms[battle.id]!.robot_id = Number(($event.target as HTMLSelectElement).value)"
            >
              <option value="">Kies robot</option>
              <option
                v-for="robot in data.available_robots"
                :key="robot.id"
                :value="robot.id"
                :disabled="!robot.is_battle_ready"
              >
                {{ robot.naam }} · {{ robot.team?.naam ?? 'Onbekend' }} {{ robot.is_battle_ready ? '' : '(niet ready)' }}
              </option>
            </select>
            <input
              :value="scoreForms[battle.id]?.punten ?? 0"
              type="number"
              class="rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white"
              @focus="ensureScoreForm(battle.id)"
              @input="ensureScoreForm(battle.id); scoreForms[battle.id]!.punten = Number(($event.target as HTMLInputElement).value)"
            >
            <input
              :value="scoreForms[battle.id]?.opmerkingen ?? ''"
              class="rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white"
              placeholder="Opmerking"
              @focus="ensureScoreForm(battle.id)"
              @input="ensureScoreForm(battle.id); scoreForms[battle.id]!.opmerkingen = ($event.target as HTMLInputElement).value"
            >
            <button class="rounded-lg bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-500 md:col-span-2" @click="addScore(category.id, battle.id)">
              Score opslaan
            </button>
          </div>

          <table class="mt-3 w-full border-collapse text-sm">
            <thead class="text-left text-slate-400">
              <tr>
                <th class="py-1">Robot</th>
                <th class="py-1">Team</th>
                <th class="py-1">Punten</th>
                <th class="py-1">Opmerkingen</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="battle.scores.length === 0">
                <td colspan="4" class="py-2 text-slate-300">Nog geen scores.</td>
              </tr>
              <tr v-for="score in battle.scores" :key="score.id" class="border-t border-white/10">
                <td class="py-2 text-white">{{ score.robot?.naam ?? '-' }}</td>
                <td class="py-2 text-slate-300">{{ score.robot?.team?.naam ?? '-' }}</td>
                <td class="py-2 text-slate-300">{{ score.punten }}</td>
                <td class="py-2 text-slate-400">{{ score.opmerkingen ?? '-' }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </article>
    </section>
  </main>
</template>
