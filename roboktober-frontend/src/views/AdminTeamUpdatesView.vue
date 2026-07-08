<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { getAdminTeamUpdates, updateAdminTeamUpdateStatus } from '@/api'
import type { AdminTeamUpdate } from '@/types/api'

const updates = ref<AdminTeamUpdate[]>([])
const loading = ref(false)
const errorMessage = ref<string | null>(null)

async function laadUpdates(): Promise<void> {
  loading.value = true
  errorMessage.value = null

  try {
    const data = await getAdminTeamUpdates()
    updates.value = data.data
  } catch {
    errorMessage.value = 'Team updates laden mislukt.'
  } finally {
    loading.value = false
  }
}

async function togglePublished(update: AdminTeamUpdate): Promise<void> {
  try {
    const updated = await updateAdminTeamUpdateStatus(update.id, {
      is_published: !update.is_published,
    })

    const index = updates.value.findIndex((item) => item.id === update.id)
    if (index >= 0) updates.value[index] = updated
  } catch {
    errorMessage.value = `Status wijzigen mislukt voor update ${update.titel}.`
  }
}

onMounted(async () => {
  await laadUpdates()
})
</script>

<template>
  <main class="mx-auto min-h-[70vh] max-w-6xl px-6 py-12">
    <header class="mb-8">
      <h1 class="text-3xl font-black text-white">Admin · Team updates</h1>
      <p class="mt-2 text-slate-300">Publicatiebeheer voor teamblogs via API.</p>
    </header>

    <p v-if="errorMessage" class="mb-4 rounded-md border border-red-400/40 bg-red-950/30 px-3 py-2 text-sm text-red-200">{{ errorMessage }}</p>

    <section class="overflow-hidden rounded-xl border border-white/10 bg-robo-dark/60">
      <table class="w-full border-collapse">
        <thead class="bg-slate-900/70 text-left text-xs uppercase tracking-wide text-slate-300">
          <tr>
            <th class="px-4 py-3">Titel</th>
            <th class="px-4 py-3">Team</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3">Actie</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td colspan="4" class="px-4 py-6 text-center text-slate-300">Laden...</td>
          </tr>
          <tr v-else-if="updates.length === 0">
            <td colspan="4" class="px-4 py-6 text-center text-slate-300">Geen team updates gevonden.</td>
          </tr>
          <tr v-for="update in updates" :key="update.id" class="border-t border-white/10">
            <td class="px-4 py-3 text-white">{{ update.titel }}</td>
            <td class="px-4 py-3 text-sm text-slate-300">{{ update.team?.naam ?? 'Onbekend team' }}</td>
            <td class="px-4 py-3 text-sm" :class="update.is_published ? 'text-emerald-300' : 'text-amber-300'">
              {{ update.is_published ? 'Gepubliceerd' : 'Concept' }}
            </td>
            <td class="px-4 py-3">
              <div class="flex flex-wrap gap-2">
                <RouterLink
                  :to="`/admin/team-updates/${update.id}/edit`"
                  class="rounded-md border border-robo-orange/60 px-3 py-1.5 text-xs font-semibold text-robo-orange hover:bg-robo-orange/15"
                >
                  Bewerken
                </RouterLink>
                <button
                  class="rounded-md border border-white/30 px-3 py-1.5 text-xs font-semibold text-slate-100 hover:bg-white/10"
                  @click="togglePublished(update)"
                >
                  {{ update.is_published ? 'Depubliceren' : 'Publiceren' }}
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </section>
  </main>
</template>
