<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { getAdminPageVisitAnalytics } from '@/api'
import type { AdminPageVisitAnalytics, PageVisitGranularity } from '@/types/api'

const granularity = ref<PageVisitGranularity>('daily')
const analytics = ref<AdminPageVisitAnalytics | null>(null)
const loading = ref(false)
const error = ref('')

const palette = ['#f97316', '#22d3ee', '#a78bfa', '#34d399', '#f43f5e', '#f59e0b', '#60a5fa', '#10b981']

const chartWidth = 960
const chartHeight = 300
const chartPadding = 28

const chartMax = computed(() => {
  const values = analytics.value?.series.flatMap((serie) => serie.points) ?? []
  const max = Math.max(0, ...values)
  return max > 0 ? max : 1
})

const lineSeries = computed(() => {
  const labelsCount = analytics.value?.labels.length ?? 0
  const width = chartWidth - chartPadding * 2
  const height = chartHeight - chartPadding * 2

  return (analytics.value?.series ?? []).map((serie, index) => {
    const points = serie.points.map((value, pointIndex) => {
      const x = chartPadding + (labelsCount <= 1 ? 0 : (width * pointIndex) / (labelsCount - 1))
      const y = chartPadding + height - (value / chartMax.value) * height
      return `${x},${y}`
    }).join(' ')

    return {
      ...serie,
      color: palette[index % palette.length],
      points,
    }
  })
})

const xAxisInfo = computed(() => {
  const labels = analytics.value?.labels ?? []

  if (labels.length === 0) {
    return { start: '-', end: '-' }
  }

  return {
    start: labels[0] ?? '-',
    end: labels[labels.length - 1] ?? '-',
  }
})

async function loadAnalytics(): Promise<void> {
  loading.value = true
  error.value = ''

  try {
    analytics.value = await getAdminPageVisitAnalytics({
      granularity: granularity.value,
      limit_pages: 8,
    })
  } catch {
    analytics.value = null
    error.value = 'Pagina-bezoek analytics laden mislukt.'
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  await loadAnalytics()
})
</script>

<template>
  <main class="mx-auto min-h-[70vh] max-w-7xl px-6 py-12">
    <header class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
      <div>
        <h1 class="text-3xl font-black text-white">Admin · Page Analytics</h1>
        <p class="mt-2 text-slate-300">Bezoekersaantallen per pagina over tijd, alleen zichtbaar voor admins.</p>
      </div>

      <div class="flex items-center gap-2">
        <label for="granularity" class="text-sm font-semibold text-slate-200">Resolutie</label>
        <select
          id="granularity"
          v-model="granularity"
          class="rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white"
          @change="loadAnalytics"
        >
          <option value="daily">Dagelijks (laatste 30 dagen)</option>
          <option value="hourly">Per uur (laatste 24 uur)</option>
        </select>
      </div>
    </header>

    <p v-if="error" class="mb-4 rounded-md border border-red-500/40 bg-red-950/30 px-3 py-2 text-sm text-red-200">
      {{ error }}
    </p>

    <section v-if="loading" class="rounded-xl border border-white/10 bg-robo-dark/60 px-4 py-6 text-slate-300">
      Grafiek laden...
    </section>

    <template v-else-if="analytics">
      <section class="mb-6 grid gap-4 md:grid-cols-3">
        <article class="rounded-xl border border-white/10 bg-robo-dark/70 p-4">
          <h2 class="text-xs uppercase tracking-wide text-slate-300">Totaal visits</h2>
          <p class="mt-2 text-3xl font-black text-white">{{ analytics.totals.overall_visits }}</p>
        </article>
        <article class="rounded-xl border border-white/10 bg-robo-dark/70 p-4">
          <h2 class="text-xs uppercase tracking-wide text-slate-300">Unieke pagina's</h2>
          <p class="mt-2 text-3xl font-black text-white">{{ analytics.totals.pages_tracked }}</p>
        </article>
        <article class="rounded-xl border border-white/10 bg-robo-dark/70 p-4">
          <h2 class="text-xs uppercase tracking-wide text-slate-300">Top lijnen</h2>
          <p class="mt-2 text-3xl font-black text-white">{{ analytics.series.length }}</p>
        </article>
      </section>

      <section class="mb-6 rounded-xl border border-white/10 bg-robo-dark/70 p-5">
        <div class="mb-3 flex items-center justify-between text-xs text-slate-400">
          <span>Van {{ xAxisInfo.start }}</span>
          <span>Tot {{ xAxisInfo.end }}</span>
        </div>

        <div class="overflow-x-auto rounded-lg border border-white/10 bg-slate-950/50 p-3">
          <svg :viewBox="`0 0 ${chartWidth} ${chartHeight}`" class="h-[320px] min-w-[860px] w-full">
            <line :x1="chartPadding" :y1="chartHeight - chartPadding" :x2="chartWidth - chartPadding" :y2="chartHeight - chartPadding" stroke="#334155" stroke-width="1" />
            <line :x1="chartPadding" :y1="chartPadding" :x2="chartPadding" :y2="chartHeight - chartPadding" stroke="#334155" stroke-width="1" />

            <polyline
              v-for="serie in lineSeries"
              :key="serie.page_path"
              :points="serie.points"
              fill="none"
              :stroke="serie.color"
              stroke-width="2.5"
              stroke-linecap="round"
              stroke-linejoin="round"
            />
          </svg>
        </div>

        <div class="mt-4 grid gap-2 md:grid-cols-2 xl:grid-cols-4">
          <div
            v-for="serie in lineSeries"
            :key="serie.page_path"
            class="rounded-lg border border-white/10 bg-slate-900/60 px-3 py-2"
          >
            <div class="flex items-center gap-2">
              <span class="inline-block h-2.5 w-2.5 rounded-full" :style="{ backgroundColor: serie.color }" />
              <p class="truncate text-sm font-semibold text-white" :title="serie.page_path">{{ serie.page_path }}</p>
            </div>
            <p class="mt-1 text-xs text-slate-300">{{ serie.total }} visits</p>
          </div>
        </div>
      </section>

      <section class="overflow-hidden rounded-xl border border-white/10 bg-robo-dark/60">
        <table class="w-full border-collapse">
          <thead class="bg-slate-900/70 text-left text-xs uppercase tracking-wide text-slate-300">
            <tr>
              <th class="px-4 py-3">Pagina</th>
              <th class="px-4 py-3">Visits</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="serie in analytics.series" :key="serie.page_path" class="border-t border-white/10">
              <td class="px-4 py-3 text-white">{{ serie.page_path }}</td>
              <td class="px-4 py-3 text-slate-300">{{ serie.total }}</td>
            </tr>
          </tbody>
        </table>
      </section>
    </template>
  </main>
</template>
