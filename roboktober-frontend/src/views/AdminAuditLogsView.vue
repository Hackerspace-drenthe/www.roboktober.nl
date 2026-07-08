<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { getAdminAuditLogs } from '@/api'
import type { AdminAuditLog } from '@/types/api'

const logs = ref<AdminAuditLog[]>([])
const loading = ref(false)
const errorMessage = ref<string | null>(null)

const actionFilter = ref('')
const actorFilter = ref('')
const subjectTypeFilter = ref('')

async function laadLogs(): Promise<void> {
  loading.value = true
  errorMessage.value = null

  try {
    const data = await getAdminAuditLogs({
      action: actionFilter.value.trim() || undefined,
      actor_user_id: actorFilter.value.trim() ? Number(actorFilter.value) : undefined,
      subject_type: subjectTypeFilter.value.trim() || undefined,
    })

    logs.value = data.data
  } catch {
    errorMessage.value = 'Audit logs laden mislukt.'
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  await laadLogs()
})

function stringifyValue(value: unknown): string {
  if (value === null || value === undefined) return '-'
  if (typeof value === 'string') return value
  return JSON.stringify(value)
}
</script>

<template>
  <main class="mx-auto min-h-[70vh] max-w-7xl px-6 py-12">
    <header class="mb-8">
      <h1 class="text-3xl font-black text-white">Admin · Audit logs</h1>
      <p class="mt-2 text-slate-300">Inzicht in alle admin mutaties met actor, actie en before/after state.</p>
    </header>

    <form class="mb-6 grid gap-3 rounded-xl border border-white/10 bg-robo-dark/60 p-4 md:grid-cols-4" @submit.prevent="laadLogs">
      <input
        v-model="actionFilter"
        type="text"
        placeholder="Filter actie (bv. user.role_updated)"
        class="rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white outline-none ring-robo-orange/70 focus:ring-2"
      />

      <input
        v-model="actorFilter"
        type="number"
        min="1"
        placeholder="Actor user id"
        class="rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white outline-none ring-robo-orange/70 focus:ring-2"
      />

      <input
        v-model="subjectTypeFilter"
        type="text"
        placeholder="Subject type (FQCN)"
        class="rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white outline-none ring-robo-orange/70 focus:ring-2"
      />

      <button
        type="submit"
        class="rounded-lg bg-robo-orange px-4 py-2.5 font-bold text-white hover:bg-robo-orange-dark"
      >
        Filter
      </button>
    </form>

    <p v-if="errorMessage" class="mb-4 rounded-md border border-red-400/40 bg-red-950/30 px-3 py-2 text-sm text-red-200">
      {{ errorMessage }}
    </p>

    <section class="overflow-hidden rounded-xl border border-white/10 bg-robo-dark/60">
      <table class="w-full border-collapse">
        <thead class="bg-slate-900/70 text-left text-xs uppercase tracking-wide text-slate-300">
          <tr>
            <th class="px-4 py-3">Tijd</th>
            <th class="px-4 py-3">Actor</th>
            <th class="px-4 py-3">Actie</th>
            <th class="px-4 py-3">Subject</th>
            <th class="px-4 py-3">Before</th>
            <th class="px-4 py-3">After</th>
          </tr>
        </thead>

        <tbody>
          <tr v-if="loading">
            <td colspan="6" class="px-4 py-6 text-center text-slate-300">Laden...</td>
          </tr>

          <tr v-else-if="logs.length === 0">
            <td colspan="6" class="px-4 py-6 text-center text-slate-300">Geen audit logs gevonden.</td>
          </tr>

          <tr v-for="log in logs" :key="log.id" class="border-t border-white/10 align-top">
            <td class="px-4 py-3 text-xs text-slate-300">{{ log.created_at ?? '-' }}</td>
            <td class="px-4 py-3 text-xs text-slate-300">
              <p>{{ log.actor?.name ?? '-' }}</p>
              <p class="text-slate-400">{{ log.actor?.email ?? '-' }}</p>
            </td>
            <td class="px-4 py-3 text-xs text-robo-orange">{{ log.action }}</td>
            <td class="px-4 py-3 text-xs text-slate-300">
              <p>{{ log.subject_type }}</p>
              <p class="text-slate-400">#{{ log.subject_id }}</p>
            </td>
            <td class="px-4 py-3 text-xs text-slate-300">{{ stringifyValue(log.before) }}</td>
            <td class="px-4 py-3 text-xs text-slate-300">{{ stringifyValue(log.after) }}</td>
          </tr>
        </tbody>
      </table>
    </section>
  </main>
</template>
