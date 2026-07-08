<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { getAdminPages, updateAdminPageStatus } from '@/api'
import type { AdminPage } from '@/types/api'

const pages = ref<AdminPage[]>([])
const loading = ref(false)
const errorMessage = ref<string | null>(null)

async function laadPages(): Promise<void> {
  loading.value = true
  errorMessage.value = null

  try {
    const data = await getAdminPages()
    pages.value = data.data
  } catch {
    errorMessage.value = 'Pagina\'s laden mislukt.'
  } finally {
    loading.value = false
  }
}

async function togglePublished(page: AdminPage): Promise<void> {
  try {
    const updated = await updateAdminPageStatus(page.id, {
      is_published: !page.is_published,
    })

    const index = pages.value.findIndex((item) => item.id === page.id)
    if (index >= 0) pages.value[index] = updated
  } catch {
    errorMessage.value = `Status wijzigen mislukt voor pagina ${page.titel}.`
  }
}

onMounted(async () => {
  await laadPages()
})
</script>

<template>
  <main class="mx-auto min-h-[70vh] max-w-6xl px-6 py-12">
    <header class="mb-8">
      <h1 class="text-3xl font-black text-white">Admin · Pagina's</h1>
      <p class="mt-2 text-slate-300">Publicatiebeheer voor CMS-pagina's via API.</p>
    </header>

    <p v-if="errorMessage" class="mb-4 rounded-md border border-red-400/40 bg-red-950/30 px-3 py-2 text-sm text-red-200">{{ errorMessage }}</p>

    <section class="overflow-hidden rounded-xl border border-white/10 bg-robo-dark/60">
      <table class="w-full border-collapse">
        <thead class="bg-slate-900/70 text-left text-xs uppercase tracking-wide text-slate-300">
          <tr>
            <th class="px-4 py-3">Titel</th>
            <th class="px-4 py-3">Slug</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3">Actie</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td colspan="4" class="px-4 py-6 text-center text-slate-300">Laden...</td>
          </tr>
          <tr v-else-if="pages.length === 0">
            <td colspan="4" class="px-4 py-6 text-center text-slate-300">Geen pagina's gevonden.</td>
          </tr>
          <tr v-for="page in pages" :key="page.id" class="border-t border-white/10">
            <td class="px-4 py-3 text-white">{{ page.titel }}</td>
            <td class="px-4 py-3 text-sm text-slate-400">{{ page.slug }}</td>
            <td class="px-4 py-3 text-sm" :class="page.is_published ? 'text-emerald-300' : 'text-amber-300'">
              {{ page.is_published ? 'Gepubliceerd' : 'Concept' }}
            </td>
            <td class="px-4 py-3">
              <div class="flex flex-wrap gap-2">
                <RouterLink
                  :to="`/admin/pages/${page.id}/edit`"
                  class="rounded-md border border-robo-orange/60 px-3 py-1.5 text-xs font-semibold text-robo-orange hover:bg-robo-orange/15"
                >
                  Bewerken
                </RouterLink>
                <button
                  class="rounded-md border border-white/30 px-3 py-1.5 text-xs font-semibold text-slate-100 hover:bg-white/10"
                  @click="togglePublished(page)"
                >
                  {{ page.is_published ? 'Depubliceren' : 'Publiceren' }}
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </section>
  </main>
</template>
