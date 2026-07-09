<script setup lang="ts">
import {
  createAdminRobot,
  deleteAdminRobot,
  getAdminRobots,
  getAdminTeams,
  updateAdminRobot,
} from '@/api'
import type {
  AdminRobot,
  AdminRobotPayload,
  Gewichtsklasse,
  RobotStatus,
} from '@/types/api'
import { onMounted, reactive, ref } from 'vue'

const robots = ref<AdminRobot[]>([])
const teams = ref<Array<{ id: number; naam: string }>>([])
const loading = ref(false)
const errorMessage = ref<string | null>(null)
const successMessage = ref<string | null>(null)
const editingId = ref<number | null>(null)

const filters = reactive<{
  q: string
  status: '' | RobotStatus
  gewichtsklasse: '' | Gewichtsklasse
}>({
  q: '',
  status: '',
  gewichtsklasse: '',
})

const form = reactive<{
  team_id: number
  naam: string
  gewichtsklasse: Gewichtsklasse
  status: RobotStatus
  beschrijving: string
}>({
  team_id: 0,
  naam: '',
  gewichtsklasse: 'antweight',
  status: 'in_ontwikkeling',
  beschrijving: '',
})

function resetForm(): void {
  editingId.value = null
  form.team_id = 0
  form.naam = ''
  form.gewichtsklasse = 'antweight'
  form.status = 'in_ontwikkeling'
  form.beschrijving = ''
}

function editRobot(robot: AdminRobot): void {
  editingId.value = robot.id
  form.team_id = robot.team_id
  form.naam = robot.naam
  form.gewichtsklasse = robot.gewichtsklasse
  form.status = robot.status
  form.beschrijving = robot.beschrijving ?? ''
}

function buildPayload(): AdminRobotPayload {
  if (form.team_id <= 0) {
    throw new Error('Team is verplicht.')
  }

  return {
    team_id: form.team_id,
    naam: form.naam.trim(),
    gewichtsklasse: form.gewichtsklasse,
    status: form.status,
    beschrijving: form.beschrijving.trim() || null,
  }
}

async function loadTeams(): Promise<void> {
  try {
    const response = await getAdminTeams()
    teams.value = response.data.map((team) => ({ id: team.id, naam: team.naam }))

    if (form.team_id === 0 && teams.value.length > 0) {
      form.team_id = teams.value[0]!.id
    }
  } catch {
    // Teams are optional for initial render feedback; create/update will still validate.
  }
}

async function loadRobots(): Promise<void> {
  loading.value = true
  errorMessage.value = null

  try {
    const response = await getAdminRobots({
      q: filters.q.trim() || undefined,
      status: filters.status || undefined,
      gewichtsklasse: filters.gewichtsklasse || undefined,
    })

    robots.value = response.data
  } catch {
    errorMessage.value = 'Robots laden mislukt.'
  } finally {
    loading.value = false
  }
}

async function submitForm(): Promise<void> {
  errorMessage.value = null
  successMessage.value = null

  try {
    const payload = buildPayload()

    if (editingId.value === null) {
      await createAdminRobot(payload)
      successMessage.value = 'Robot toegevoegd.'
    } else {
      await updateAdminRobot(editingId.value, payload)
      successMessage.value = 'Robot bijgewerkt.'
    }

    resetForm()
    await loadRobots()
  } catch {
    errorMessage.value = 'Opslaan van robot mislukt. Controleer team, naam en status.'
  }
}

async function removeRobot(robot: AdminRobot): Promise<void> {
  const confirmed = window.confirm(`Weet je zeker dat je robot \"${robot.naam}\" wilt verwijderen?`)
  if (!confirmed) return

  errorMessage.value = null
  successMessage.value = null

  try {
    await deleteAdminRobot(robot.id)
    successMessage.value = 'Robot verwijderd.'

    if (editingId.value === robot.id) {
      resetForm()
    }

    await loadRobots()
  } catch {
    errorMessage.value = 'Verwijderen mislukt. Mogelijk zijn er al competitiepunten gekoppeld.'
  }
}

onMounted(async () => {
  await Promise.all([loadTeams(), loadRobots()])
})
</script>

<template>
  <main class="mx-auto min-h-[70vh] max-w-7xl px-6 py-12">
    <header class="mb-8">
      <h1 class="text-3xl font-black text-white">Admin · Robots</h1>
      <p class="mt-2 text-slate-300">Volledig API-beheer van robots.</p>
    </header>

    <p v-if="errorMessage" class="mb-4 rounded-md border border-red-400/40 bg-red-950/30 px-3 py-2 text-sm text-red-200">
      {{ errorMessage }}
    </p>
    <p v-if="successMessage" class="mb-4 rounded-md border border-emerald-400/40 bg-emerald-950/30 px-3 py-2 text-sm text-emerald-200">
      {{ successMessage }}
    </p>

    <section class="mb-6 rounded-xl border border-white/10 bg-robo-dark/60 p-5">
      <div class="mb-3 flex items-center justify-between">
        <h2 class="text-lg font-bold text-white">{{ editingId === null ? 'Nieuwe robot' : 'Robot bewerken' }}</h2>
        <button
          v-if="editingId !== null"
          type="button"
          class="rounded-md border border-white/30 px-3 py-1.5 text-xs font-semibold text-slate-100 hover:bg-white/10"
          @click="resetForm"
        >
          Annuleren
        </button>
      </div>

      <form class="grid gap-3 md:grid-cols-2" @submit.prevent="submitForm">
        <label class="space-y-1 text-sm text-slate-300">
          <span class="font-semibold text-slate-200">Team</span>
          <select v-model.number="form.team_id" required class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white">
            <option :value="0">Kies team</option>
            <option v-for="team in teams" :key="team.id" :value="team.id">{{ team.naam }}</option>
          </select>
        </label>

        <label class="space-y-1 text-sm text-slate-300">
          <span class="font-semibold text-slate-200">Naam</span>
          <input v-model="form.naam" required class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white" />
        </label>

        <label class="space-y-1 text-sm text-slate-300">
          <span class="font-semibold text-slate-200">Gewichtsklasse</span>
          <select v-model="form.gewichtsklasse" class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white">
            <option value="antweight">Antweight</option>
            <option value="beetleweight">Beetleweight</option>
            <option value="featherweight">Featherweight</option>
          </select>
        </label>

        <label class="space-y-1 text-sm text-slate-300">
          <span class="font-semibold text-slate-200">Status</span>
          <select v-model="form.status" class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white">
            <option value="in_ontwikkeling">In ontwikkeling</option>
            <option value="gereed">Gereed</option>
            <option value="battle_ready">Battle ready</option>
          </select>
        </label>

        <label class="space-y-1 text-sm text-slate-300 md:col-span-2">
          <span class="font-semibold text-slate-200">Beschrijving</span>
          <textarea v-model="form.beschrijving" rows="3" class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white" />
        </label>

        <div class="md:col-span-2">
          <button type="submit" class="rounded-lg bg-robo-orange px-4 py-2 font-bold text-white hover:bg-robo-orange-dark">
            {{ editingId === null ? 'Robot toevoegen' : 'Wijzigingen opslaan' }}
          </button>
        </div>
      </form>
    </section>

    <section class="mb-6 rounded-xl border border-white/10 bg-robo-dark/60 p-4">
      <form class="grid gap-2 md:grid-cols-4" @submit.prevent="loadRobots">
        <input
          v-model="filters.q"
          placeholder="Zoek robot/team"
          class="rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white"
        />
        <select v-model="filters.gewichtsklasse" class="rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white">
          <option value="">Alle gewichtsklassen</option>
          <option value="antweight">Antweight</option>
          <option value="beetleweight">Beetleweight</option>
          <option value="featherweight">Featherweight</option>
        </select>
        <select v-model="filters.status" class="rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white">
          <option value="">Alle statussen</option>
          <option value="in_ontwikkeling">In ontwikkeling</option>
          <option value="gereed">Gereed</option>
          <option value="battle_ready">Battle ready</option>
        </select>
        <button type="submit" class="rounded-lg bg-slate-700 px-4 py-2 font-semibold text-white hover:bg-slate-600">Filter</button>
      </form>
    </section>

    <section class="overflow-hidden rounded-xl border border-white/10 bg-robo-dark/60">
      <table class="w-full border-collapse">
        <thead class="bg-slate-900/70 text-left text-xs uppercase tracking-wide text-slate-300">
          <tr>
            <th class="px-4 py-3">Robot</th>
            <th class="px-4 py-3">Team</th>
            <th class="px-4 py-3">Gewicht</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3">Acties</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td colspan="5" class="px-4 py-6 text-center text-slate-300">Laden...</td>
          </tr>
          <tr v-else-if="robots.length === 0">
            <td colspan="5" class="px-4 py-6 text-center text-slate-300">Geen robots gevonden.</td>
          </tr>
          <tr v-for="robot in robots" :key="robot.id" class="border-t border-white/10">
            <td class="px-4 py-3 text-white">{{ robot.naam }}</td>
            <td class="px-4 py-3 text-slate-300">{{ robot.team?.naam ?? '-' }}</td>
            <td class="px-4 py-3 text-slate-300">{{ robot.gewichtsklasse_label }}</td>
            <td class="px-4 py-3 text-slate-300">{{ robot.status_label }}</td>
            <td class="px-4 py-3">
              <div class="flex flex-wrap gap-2">
                <button
                  type="button"
                  class="rounded-md border border-robo-orange/60 px-3 py-1.5 text-xs font-semibold text-robo-orange hover:bg-robo-orange/15"
                  @click="editRobot(robot)"
                >
                  Bewerken
                </button>
                <button
                  type="button"
                  class="rounded-md border border-red-400/50 px-3 py-1.5 text-xs font-semibold text-red-200 hover:bg-red-500/15"
                  @click="removeRobot(robot)"
                >
                  Verwijderen
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </section>
  </main>
</template>
