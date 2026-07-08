<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { getAdminTeams, updateAdminTeamStatus } from '@/api'
import type { AdminTeam, TeamStatus } from '@/types/api'

const teams = ref<AdminTeam[]>([])
const loading = ref(false)
const selectedStatus = ref<TeamStatus | ''>('')
const searchTerm = ref('')
const errorMessage = ref<string | null>(null)

async function laadTeams(): Promise<void> {
  loading.value = true
  errorMessage.value = null

  try {
    const data = await getAdminTeams({
      status: selectedStatus.value || undefined,
      q: searchTerm.value.trim() || undefined,
    })

    teams.value = data.data
  } catch {
    errorMessage.value = 'Teams laden mislukt. Controleer je rechten en probeer opnieuw.'
  } finally {
    loading.value = false
  }
}

async function wijzigStatus(team: AdminTeam, status: TeamStatus): Promise<void> {
  try {
    const updated = await updateAdminTeamStatus(team.id, {
      status,
      opmerkingen: team.opmerkingen ?? undefined,
    })

    const index = teams.value.findIndex((item) => item.id === team.id)
    if (index >= 0) {
      teams.value[index] = updated
    }
  } catch {
    errorMessage.value = `Status wijzigen mislukt voor team ${team.naam}.`
  }
}

onMounted(async () => {
  await laadTeams()
})
</script>

<template>
  <main class="mx-auto min-h-[70vh] max-w-6xl px-6 py-12">
    <header class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
      <div>
        <h1 class="text-3xl font-black text-white">Admin · Teams</h1>
        <p class="mt-2 text-slate-300">API-only moderatie van teams en goedkeuringen.</p>
      </div>

      <form class="flex w-full flex-col gap-2 md:w-auto md:flex-row" @submit.prevent="laadTeams">
        <input
          v-model="searchTerm"
          type="search"
          placeholder="Zoek op team/contact/email"
          class="min-w-72 rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white outline-none ring-robo-orange/70 transition focus:ring-2"
        />

        <select
          v-model="selectedStatus"
          class="rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white outline-none ring-robo-orange/70 transition focus:ring-2"
        >
          <option value="">Alle statussen</option>
          <option value="pending">In behandeling</option>
          <option value="approved">Goedgekeurd</option>
          <option value="rejected">Afgewezen</option>
        </select>

        <button type="submit" class="rounded-lg bg-robo-orange px-4 py-2.5 font-bold text-white hover:bg-robo-orange-dark">
          Filter
        </button>
      </form>
    </header>

    <p v-if="errorMessage" class="mb-4 rounded-md border border-red-400/40 bg-red-950/30 px-3 py-2 text-sm text-red-200">
      {{ errorMessage }}
    </p>

    <section class="overflow-hidden rounded-xl border border-white/10 bg-robo-dark/60">
      <table class="w-full border-collapse">
        <thead class="bg-slate-900/70 text-left text-xs uppercase tracking-wide text-slate-300">
          <tr>
            <th class="px-4 py-3">Team</th>
            <th class="px-4 py-3">Contact</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3">Actie</th>
          </tr>
        </thead>

        <tbody>
          <tr v-if="loading">
            <td colspan="4" class="px-4 py-6 text-center text-slate-300">Laden...</td>
          </tr>

          <tr v-else-if="teams.length === 0">
            <td colspan="4" class="px-4 py-6 text-center text-slate-300">Geen teams gevonden.</td>
          </tr>

          <tr v-for="team in teams" :key="team.id" class="border-t border-white/10">
            <td class="px-4 py-3">
              <p class="font-semibold text-white">{{ team.naam }}</p>
              <p class="text-xs text-slate-400">Editie: {{ team.edition?.naam ?? 'Onbekend' }}</p>
            </td>
            <td class="px-4 py-3 text-sm text-slate-300">
              <p>{{ team.contactpersoon }}</p>
              <p class="text-xs text-slate-400">{{ team.email }}</p>
            </td>
            <td class="px-4 py-3 text-sm text-slate-200">{{ team.status_label }}</td>
            <td class="px-4 py-3">
              <div class="flex gap-2">
                <button
                  class="rounded-md border border-emerald-500/60 px-2 py-1 text-xs font-semibold text-emerald-300 hover:bg-emerald-500/10"
                  @click="wijzigStatus(team, 'approved')"
                >
                  Goedkeuren
                </button>
                <button
                  class="rounded-md border border-rose-500/60 px-2 py-1 text-xs font-semibold text-rose-300 hover:bg-rose-500/10"
                  @click="wijzigStatus(team, 'rejected')"
                >
                  Afwijzen
                </button>
                <button
                  class="rounded-md border border-amber-500/60 px-2 py-1 text-xs font-semibold text-amber-300 hover:bg-amber-500/10"
                  @click="wijzigStatus(team, 'pending')"
                >
                  Pending
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </section>
  </main>
</template>
