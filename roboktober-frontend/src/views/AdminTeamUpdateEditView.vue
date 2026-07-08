<script setup lang="ts">
import { getAdminTeamUpdate, updateAdminTeamUpdateContent } from '@/api'
import ContentResourcePanel from '@/components/editor/ContentResourcePanel.vue'
import EditorFormattingToolbar from '@/components/editor/EditorFormattingToolbar.vue'
import { useContentInsertion } from '@/composables/useContentInsertion'
import type { AdminTeamUpdate } from '@/types/api'
import { computed, onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()

const loading = ref(true)
const saving = ref(false)
const errorMessage = ref<string | null>(null)
const successMessage = ref<string | null>(null)
const teamUpdate = ref<AdminTeamUpdate | null>(null)
const contentTextarea = ref<HTMLTextAreaElement | null>(null)
const contentRef = ref('')

const titel = ref('')
const excerpt = ref('')
const content = ref('')
const contentFormat = ref<'html' | 'markdown'>('html')

const teamUpdateId = computed(() => Number(route.params.id))
const {
  insertSnippet,
  wrapSelection,
  formatHeading,
  formatList,
  formatLink,
  formatQuote,
  formatCode,
  insertDivider,
} = useContentInsertion(contentRef, contentTextarea)

function insertResourceSnippet(snippet: string): void {
  contentRef.value = content.value
  insertSnippet(snippet)
  content.value = contentRef.value
}

function runFormatAction(action: 'bold' | 'italic' | 'h2' | 'h3' | 'ul' | 'ol' | 'link' | 'quote' | 'code' | 'divider'): void {
  contentRef.value = content.value

  if (action === 'bold') {
    if (contentFormat.value === 'html') {
      wrapSelection('<strong>', '</strong>', 'vetgedrukte tekst')
    } else {
      wrapSelection('**', '**', 'vetgedrukte tekst')
    }
  }

  if (action === 'italic') {
    if (contentFormat.value === 'html') {
      wrapSelection('<em>', '</em>', 'cursieve tekst')
    } else {
      wrapSelection('*', '*', 'cursieve tekst')
    }
  }

  if (action === 'h2') {
    formatHeading(2, contentFormat.value)
  }

  if (action === 'h3') {
    formatHeading(3, contentFormat.value)
  }

  if (action === 'ul') {
    formatList('ul', contentFormat.value)
  }

  if (action === 'ol') {
    formatList('ol', contentFormat.value)
  }

  if (action === 'link') {
    const url = window.prompt('Voer de URL in', 'https://')
    if (url && url.trim().length > 0) {
      formatLink(url.trim(), contentFormat.value)
    }
  }

  if (action === 'quote') {
    formatQuote(contentFormat.value)
  }

  if (action === 'code') {
    formatCode(contentFormat.value)
  }

  if (action === 'divider') {
    insertDivider(contentFormat.value)
  }

  content.value = contentRef.value
}

async function loadTeamUpdate(): Promise<void> {
  loading.value = true
  errorMessage.value = null

  try {
    const data = await getAdminTeamUpdate(teamUpdateId.value)
    teamUpdate.value = data

    titel.value = data.titel
    excerpt.value = data.excerpt ?? ''
    content.value = data.content
    contentFormat.value = data.content_format
  } catch {
    errorMessage.value = 'Team update laden mislukt.'
  } finally {
    loading.value = false
  }
}

async function saveTeamUpdate(): Promise<void> {
  if (!teamUpdate.value) return

  saving.value = true
  errorMessage.value = null
  successMessage.value = null

  try {
    const updated = await updateAdminTeamUpdateContent(teamUpdate.value.id, {
      titel: titel.value,
      excerpt: excerpt.value || null,
      content: content.value,
      content_format: contentFormat.value,
    })

    teamUpdate.value = updated
    successMessage.value = 'Team update opgeslagen.'
  } catch {
    errorMessage.value = 'Opslaan mislukt. Controleer je invoer en probeer opnieuw.'
  } finally {
    saving.value = false
  }
}

onMounted(async () => {
  await loadTeamUpdate()
})
</script>

<template>
  <main class="mx-auto min-h-[70vh] max-w-6xl px-6 py-12">
    <header class="mb-8">
      <h1 class="text-3xl font-black text-white">Admin · Team update bewerken</h1>
      <p class="mt-2 text-slate-300">Bewerk teamblog-content en voeg resources direct in.</p>
      <p v-if="teamUpdate?.team?.naam" class="mt-1 text-sm text-slate-400">Team: {{ teamUpdate.team.naam }}</p>
    </header>

    <p v-if="errorMessage" class="mb-4 rounded-md border border-red-400/40 bg-red-950/30 px-3 py-2 text-sm text-red-200">{{ errorMessage }}</p>
    <p v-if="successMessage" class="mb-4 rounded-md border border-emerald-400/40 bg-emerald-950/30 px-3 py-2 text-sm text-emerald-200">{{ successMessage }}</p>

    <section v-if="loading" class="rounded-xl border border-white/10 bg-robo-dark/60 p-6 text-slate-300">Team update laden...</section>

    <form v-else-if="teamUpdate" class="space-y-4 rounded-xl border border-white/10 bg-robo-dark/60 p-6" @submit.prevent="saveTeamUpdate">
      <div>
        <label class="mb-1 block text-sm font-semibold text-slate-200" for="titel">Titel</label>
        <input id="titel" v-model="titel" required class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white" />
      </div>

      <div>
        <label class="mb-1 block text-sm font-semibold text-slate-200" for="excerpt">Intro</label>
        <input id="excerpt" v-model="excerpt" class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white" />
      </div>

      <div>
        <label class="mb-1 block text-sm font-semibold text-slate-200" for="content-format">Opmaak</label>
        <select id="content-format" v-model="contentFormat" class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white">
          <option value="html">HTML</option>
          <option value="markdown">Markdown</option>
        </select>
      </div>

      <div>
        <label class="mb-1 block text-sm font-semibold text-slate-200" for="content">Content</label>
        <EditorFormattingToolbar :disabled="saving" @action="runFormatAction" />
        <textarea id="content" ref="contentTextarea" v-model="content" rows="12" required class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white" />
      </div>

      <ContentResourcePanel :content-format="contentFormat" :disabled="saving" @insert="insertResourceSnippet" />

      <div class="flex items-center gap-3">
        <button type="submit" :disabled="saving" class="rounded-lg bg-robo-orange px-5 py-2.5 font-bold text-white hover:bg-robo-orange-dark disabled:opacity-60">
          {{ saving ? 'Opslaan...' : 'Opslaan' }}
        </button>
        <RouterLink to="/admin/team-updates" class="text-sm text-slate-300 underline">Terug naar overzicht</RouterLink>
      </div>
    </form>
  </main>
</template>
