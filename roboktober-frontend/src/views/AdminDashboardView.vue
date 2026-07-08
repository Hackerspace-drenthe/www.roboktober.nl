<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { getAdminDashboardSummary } from '@/api'
import type { AdminDashboardSummary } from '@/types/api'

const summary = ref<AdminDashboardSummary | null>(null)
const loading = ref(false)
const errorMessage = ref<string | null>(null)

async function laadDashboard(): Promise<void> {
  loading.value = true
  errorMessage.value = null

  try {
    summary.value = await getAdminDashboardSummary()
  } catch {
    errorMessage.value = 'Dashboard laden mislukt.'
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  await laadDashboard()
})
</script>

<template>
  <main class="mx-auto min-h-[70vh] max-w-7xl px-6 py-12">
    <header class="mb-8">
      <h1 class="text-3xl font-black text-white">Admin dashboard</h1>
      <p class="mt-2 text-slate-300">Snel overzicht van moderatie-werk en recente mutaties.</p>
    </header>

    <p v-if="errorMessage" class="mb-4 rounded-md border border-red-400/40 bg-red-950/30 px-3 py-2 text-sm text-red-200">
      {{ errorMessage }}
    </p>

    <section v-if="loading" class="rounded-xl border border-white/10 bg-robo-dark/60 px-4 py-6 text-slate-300">
      Laden...
    </section>

    <template v-else-if="summary">
      <section class="mb-6 grid gap-4 md:grid-cols-4">
        <article class="rounded-xl border border-amber-500/30 bg-robo-dark/70 p-4">
          <h2 class="text-xs uppercase tracking-wide text-amber-300">Pending teams</h2>
          <p class="mt-2 text-3xl font-black text-white">{{ summary.stats.pending_teams }}</p>
        </article>

        <article class="rounded-xl border border-sky-500/30 bg-robo-dark/70 p-4">
          <h2 class="text-xs uppercase tracking-wide text-sky-300">Draft posts</h2>
          <p class="mt-2 text-3xl font-black text-white">{{ summary.stats.draft_posts }}</p>
        </article>

        <article class="rounded-xl border border-indigo-500/30 bg-robo-dark/70 p-4">
          <h2 class="text-xs uppercase tracking-wide text-indigo-300">Draft pages</h2>
          <p class="mt-2 text-3xl font-black text-white">{{ summary.stats.draft_pages }}</p>
        </article>

        <article class="rounded-xl border border-emerald-500/30 bg-robo-dark/70 p-4">
          <h2 class="text-xs uppercase tracking-wide text-emerald-300">Draft team updates</h2>
          <p class="mt-2 text-3xl font-black text-white">{{ summary.stats.draft_team_updates }}</p>
        </article>
      </section>

      <section class="mb-6 overflow-hidden rounded-xl border border-white/10 bg-robo-dark/60">
        <div class="border-b border-white/10 px-4 py-3">
          <h2 class="font-bold text-white">Pending teams (top 5)</h2>
        </div>
        <table class="w-full border-collapse">
          <thead class="bg-slate-900/70 text-left text-xs uppercase tracking-wide text-slate-300">
            <tr>
              <th class="px-4 py-3">Team</th>
              <th class="px-4 py-3">Contact</th>
              <th class="px-4 py-3">Aangemeld</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="summary.pending_teams.length === 0">
              <td colspan="3" class="px-4 py-4 text-center text-slate-300">Geen pending teams.</td>
            </tr>
            <tr v-for="team in summary.pending_teams" :key="team.id" class="border-t border-white/10">
              <td class="px-4 py-3 text-white">{{ team.naam }}</td>
              <td class="px-4 py-3 text-slate-300">{{ team.contactpersoon }}</td>
              <td class="px-4 py-3 text-slate-400">{{ team.created_at ?? '-' }}</td>
            </tr>
          </tbody>
        </table>
      </section>

      <section class="overflow-hidden rounded-xl border border-white/10 bg-robo-dark/60">
        <div class="border-b border-white/10 px-4 py-3">
          <h2 class="font-bold text-white">Recente activiteit</h2>
        </div>
        <table class="w-full border-collapse">
          <thead class="bg-slate-900/70 text-left text-xs uppercase tracking-wide text-slate-300">
            <tr>
              <th class="px-4 py-3">Actie</th>
              <th class="px-4 py-3">Actor</th>
              <th class="px-4 py-3">Subject</th>
              <th class="px-4 py-3">Tijd</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="summary.recent_activity.length === 0">
              <td colspan="4" class="px-4 py-4 text-center text-slate-300">Geen recente activiteit.</td>
            </tr>
            <tr v-for="activity in summary.recent_activity" :key="activity.id" class="border-t border-white/10">
              <td class="px-4 py-3 text-robo-orange">{{ activity.action }}</td>
              <td class="px-4 py-3 text-slate-300">{{ activity.actor.name ?? '-' }}</td>
              <td class="px-4 py-3 text-slate-400">{{ activity.subject_type }} #{{ activity.subject_id }}</td>
              <td class="px-4 py-3 text-slate-400">{{ activity.created_at ?? '-' }}</td>
            </tr>
          </tbody>
        </table>
      </section>
    </template>
  </main>
</template>
