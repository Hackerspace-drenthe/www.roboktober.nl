<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { getAdminUsers, updateAdminUserRole } from '@/api'
import type { AdminUser, UserRole } from '@/types/api'

const users = ref<AdminUser[]>([])
const loading = ref(false)
const errorMessage = ref<string | null>(null)
const searchTerm = ref('')

const roleOptions: Array<{ value: UserRole; label: string }> = [
  { value: 'visitor', label: 'Bezoeker' },
  { value: 'teamcaptain', label: 'Teamcaptain' },
  { value: 'moderator', label: 'Moderator' },
  { value: 'admin', label: 'Admin' },
]

async function laadUsers(): Promise<void> {
  loading.value = true
  errorMessage.value = null

  try {
    const data = await getAdminUsers({
      q: searchTerm.value.trim() || undefined,
    })
    users.value = data.data
  } catch {
    errorMessage.value = 'Gebruikers laden mislukt.'
  } finally {
    loading.value = false
  }
}

async function wijzigRol(user: AdminUser, role: UserRole): Promise<void> {
  try {
    const updated = await updateAdminUserRole(user.id, { role })
    const index = users.value.findIndex((item) => item.id === user.id)
    if (index >= 0) users.value[index] = updated
  } catch {
    errorMessage.value = `Rol wijzigen mislukt voor ${user.email}.`
  }
}

onMounted(async () => {
  await laadUsers()
})
</script>

<template>
  <main class="mx-auto min-h-[70vh] max-w-6xl px-6 py-12">
    <header class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
      <div>
        <h1 class="text-3xl font-black text-white">Admin · Gebruikers</h1>
        <p class="mt-2 text-slate-300">Rolbeheer voor accounts via API-only backend.</p>
      </div>

      <form class="flex w-full flex-col gap-2 md:w-auto md:flex-row" @submit.prevent="laadUsers">
        <input
          v-model="searchTerm"
          type="search"
          placeholder="Zoek op naam of e-mail"
          class="min-w-72 rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white outline-none ring-robo-orange/70 transition focus:ring-2"
        />
        <button type="submit" class="rounded-lg bg-robo-orange px-4 py-2.5 font-bold text-white hover:bg-robo-orange-dark">
          Zoeken
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
            <th class="px-4 py-3">Naam</th>
            <th class="px-4 py-3">E-mail</th>
            <th class="px-4 py-3">Rol</th>
            <th class="px-4 py-3">Wijzigen</th>
          </tr>
        </thead>

        <tbody>
          <tr v-if="loading">
            <td colspan="4" class="px-4 py-6 text-center text-slate-300">Laden...</td>
          </tr>

          <tr v-else-if="users.length === 0">
            <td colspan="4" class="px-4 py-6 text-center text-slate-300">Geen gebruikers gevonden.</td>
          </tr>

          <tr v-for="user in users" :key="user.id" class="border-t border-white/10">
            <td class="px-4 py-3 text-white">{{ user.name }}</td>
            <td class="px-4 py-3 text-sm text-slate-300">{{ user.email }}</td>
            <td class="px-4 py-3 text-sm text-slate-200">{{ user.role_label }}</td>
            <td class="px-4 py-3">
              <div class="flex flex-wrap gap-2">
                <button
                  v-for="option in roleOptions"
                  :key="option.value"
                  type="button"
                  class="rounded-md border px-2 py-1 text-xs font-semibold"
                  :class="option.value === user.role ? 'border-emerald-400/80 text-emerald-300 bg-emerald-900/20' : 'border-white/30 text-slate-200 hover:bg-white/10'"
                  @click="wijzigRol(user, option.value)"
                >
                  {{ option.label }}
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </section>
  </main>
</template>
