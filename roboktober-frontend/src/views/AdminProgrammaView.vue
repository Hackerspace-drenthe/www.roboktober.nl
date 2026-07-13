<script setup lang="ts">
import {
  createAdminEditionProgrammaItem,
  deleteAdminProgrammaItem,
  getAdminEditionProgrammaItems,
  getEditions,
  updateAdminProgrammaItem,
} from '@/api'
import ContentResourcePanel from '@/components/editor/ContentResourcePanel.vue'
import EditorFormattingToolbar from '@/components/editor/EditorFormattingToolbar.vue'
import { useEditorComposer, type EditorAction } from '@/composables/useEditorComposer'
import type { AdminProgrammaItemPayload, Edition, ProgrammaItem } from '@/types/api'
import { computed, onMounted, ref } from 'vue'

const editions = ref<Edition[]>([])
const selectedEditionId = ref<number | null>(null)
const items = ref<ProgrammaItem[]>([])
const loading = ref(false)
const saving = ref(false)
const error = ref('')
const success = ref('')
const editingId = ref<number | null>(null)

const contentTextarea = ref<HTMLTextAreaElement | null>(null)
const contentRef = ref('')

const form = ref({
  titel: '',
  beschrijving: '',
  content_format: 'html' as 'html' | 'markdown',
  start_at: '',
  end_at: '',
  volgorde: 0,
  is_published: true,
})

const contentFormat = computed<'html' | 'markdown'>({
  get: () => form.value.content_format,
  set: (value) => {
    form.value.content_format = value
  },
})

const { applyAction, insert } = useEditorComposer(contentRef, contentTextarea, contentFormat)

const kanOpslaan = computed(() => {
  return selectedEditionId.value !== null && form.value.titel.trim() !== '' && form.value.start_at.trim() !== '' && form.value.beschrijving.trim() !== ''
})

function toLocalInputValue(isoValue: string | null): string {
  if (!isoValue) return ''

  const date = new Date(isoValue)
  if (Number.isNaN(date.getTime())) return ''

  const timezoneOffsetMs = date.getTimezoneOffset() * 60_000
  return new Date(date.getTime() - timezoneOffsetMs).toISOString().slice(0, 16)
}

function toIsoValue(localValue: string): string | null {
  if (localValue.trim() === '') return null

  const date = new Date(localValue)
  if (Number.isNaN(date.getTime())) return null

  return date.toISOString()
}

function resetForm(): void {
  editingId.value = null
  form.value = {
    titel: '',
    beschrijving: '',
    content_format: 'html',
    start_at: '',
    end_at: '',
    volgorde: 0,
    is_published: true,
  }
}

function startEdit(item: ProgrammaItem): void {
  editingId.value = item.id
  form.value = {
    titel: item.titel,
    beschrijving: item.beschrijving,
    content_format: item.content_format,
    start_at: toLocalInputValue(item.start_at),
    end_at: toLocalInputValue(item.end_at),
    volgorde: item.volgorde,
    is_published: item.is_published,
  }
}

function buildPayload(): AdminProgrammaItemPayload {
  const start = toIsoValue(form.value.start_at)

  if (!start) {
    throw new Error('Starttijd ontbreekt')
  }

  return {
    titel: form.value.titel.trim(),
    beschrijving: form.value.beschrijving,
    content_format: form.value.content_format,
    start_at: start,
    end_at: toIsoValue(form.value.end_at),
    volgorde: Number.isFinite(form.value.volgorde) ? form.value.volgorde : 0,
    is_published: form.value.is_published,
  }
}

function runFormatAction(action: EditorAction): void {
  contentRef.value = form.value.beschrijving
  applyAction(action)
  form.value.beschrijving = contentRef.value
}

function insertResourceSnippet(snippet: string): void {
  contentRef.value = form.value.beschrijving
  insert(snippet)
  form.value.beschrijving = contentRef.value
}

async function loadEditionsAndDefault(): Promise<void> {
  editions.value = await getEditions()

  if (selectedEditionId.value === null && editions.value.length > 0) {
    selectedEditionId.value = editions.value[0]?.id ?? null
  }
}

async function loadItems(): Promise<void> {
  if (!selectedEditionId.value) {
    items.value = []
    return
  }

  loading.value = true
  error.value = ''

  try {
    items.value = await getAdminEditionProgrammaItems(selectedEditionId.value)
  } catch {
    error.value = 'Programma-items laden mislukt.'
    items.value = []
  } finally {
    loading.value = false
  }
}

async function saveItem(): Promise<void> {
  if (!selectedEditionId.value || !kanOpslaan.value) {
    return
  }

  saving.value = true
  error.value = ''
  success.value = ''

  try {
    const payload = buildPayload()

    if (editingId.value === null) {
      await createAdminEditionProgrammaItem(selectedEditionId.value, payload)
      success.value = 'Programma-item toegevoegd.'
    } else {
      await updateAdminProgrammaItem(editingId.value, payload)
      success.value = 'Programma-item bijgewerkt.'
    }

    resetForm()
    await loadItems()
  } catch {
    error.value = 'Opslaan van programma-item mislukt.'
  } finally {
    saving.value = false
  }
}

async function removeItem(item: ProgrammaItem): Promise<void> {
  const confirmed = window.confirm(`Weet je zeker dat je programma-item "${item.titel}" wilt verwijderen?`)
  if (!confirmed) {
    return
  }

  error.value = ''
  success.value = ''

  try {
    await deleteAdminProgrammaItem(item.id)
    success.value = 'Programma-item verwijderd.'

    if (editingId.value === item.id) {
      resetForm()
    }

    await loadItems()
  } catch {
    error.value = 'Verwijderen van programma-item mislukt.'
  }
}

function formatDateTime(iso: string | null): string {
  if (!iso) return '-'

  return new Date(iso).toLocaleString('nl-NL', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

onMounted(async () => {
  await loadEditionsAndDefault()
  await loadItems()
})
</script>

<template>
  <main class="mx-auto min-h-[70vh] max-w-7xl px-6 py-12">
    <header class="mb-8">
      <h1 class="text-3xl font-black text-white">Admin · Programma</h1>
      <p class="mt-2 text-slate-300">Beheer programma-items per editie. Items worden publiek op datum gegroepeerd getoond.</p>
    </header>

    <p v-if="error" class="mb-4 rounded-md border border-red-500/40 bg-red-950/30 px-3 py-2 text-sm text-red-200">{{ error }}</p>
    <p v-if="success" class="mb-4 rounded-md border border-emerald-500/40 bg-emerald-950/30 px-3 py-2 text-sm text-emerald-200">{{ success }}</p>

    <section class="mb-6 rounded-xl border border-white/10 bg-robo-dark/60 p-5">
      <label for="editie-select" class="mb-2 block text-sm font-semibold text-slate-200">Editie</label>
      <select
        id="editie-select"
        v-model.number="selectedEditionId"
        class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white"
        @change="loadItems"
      >
        <option v-for="edition in editions" :key="edition.id" :value="edition.id">{{ edition.naam }}</option>
      </select>
    </section>

    <section class="mb-6 rounded-xl border border-white/10 bg-robo-dark/60 p-5">
      <div class="mb-3 flex items-center justify-between">
        <h2 class="text-lg font-bold text-white">{{ editingId === null ? 'Nieuw programma-item' : 'Programma-item bewerken' }}</h2>
        <button
          v-if="editingId !== null"
          type="button"
          class="rounded-md border border-white/30 px-3 py-1.5 text-xs font-semibold text-slate-100 hover:bg-white/10"
          @click="resetForm"
        >
          Annuleren
        </button>
      </div>

      <form class="grid gap-3 md:grid-cols-2" @submit.prevent="saveItem">
        <label class="space-y-1 text-sm text-slate-300 md:col-span-2">
          <span class="font-semibold text-slate-200">Titel</span>
          <input v-model="form.titel" required class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white" />
        </label>

        <label class="space-y-1 text-sm text-slate-300">
          <span class="font-semibold text-slate-200">Start datum/tijd</span>
          <input v-model="form.start_at" type="datetime-local" required class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white" />
        </label>

        <label class="space-y-1 text-sm text-slate-300">
          <span class="font-semibold text-slate-200">Eind datum/tijd</span>
          <input v-model="form.end_at" type="datetime-local" class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white" />
        </label>

        <label class="space-y-1 text-sm text-slate-300">
          <span class="font-semibold text-slate-200">Opmaak</span>
          <select v-model="form.content_format" class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white">
            <option value="html">HTML</option>
            <option value="markdown">Markdown</option>
          </select>
        </label>

        <label class="space-y-1 text-sm text-slate-300">
          <span class="font-semibold text-slate-200">Volgorde</span>
          <input v-model.number="form.volgorde" type="number" min="0" class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white" />
        </label>

        <label class="flex items-center gap-2 text-sm text-slate-200 md:col-span-2">
          <input v-model="form.is_published" type="checkbox" class="h-4 w-4 rounded border-white/20 bg-slate-900 text-robo-orange" />
          Publiek tonen
        </label>

        <div class="md:col-span-2">
          <label class="mb-1 block text-sm font-semibold text-slate-200" for="beschrijving">Beschrijving</label>
          <EditorFormattingToolbar :disabled="saving" @action="runFormatAction" />
          <textarea
            id="beschrijving"
            ref="contentTextarea"
            v-model="form.beschrijving"
            rows="10"
            required
            class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white"
          />
        </div>

        <div class="md:col-span-2">
          <ContentResourcePanel :content-format="form.content_format" :disabled="saving" @insert="insertResourceSnippet" />
        </div>

        <div class="md:col-span-2">
          <button type="submit" :disabled="saving || !kanOpslaan" class="rounded-lg bg-robo-orange px-4 py-2 font-bold text-white hover:bg-robo-orange-dark disabled:opacity-60">
            {{ saving ? 'Opslaan...' : editingId === null ? 'Programma-item toevoegen' : 'Wijzigingen opslaan' }}
          </button>
        </div>
      </form>
    </section>

    <section class="overflow-hidden rounded-xl border border-white/10 bg-robo-dark/60">
      <table class="w-full border-collapse">
        <thead class="bg-slate-900/70 text-left text-xs uppercase tracking-wide text-slate-300">
          <tr>
            <th class="px-4 py-3">Titel</th>
            <th class="px-4 py-3">Start</th>
            <th class="px-4 py-3">Eind</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3">Actie</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td colspan="5" class="px-4 py-6 text-center text-slate-300">Programma-items laden...</td>
          </tr>
          <tr v-else-if="items.length === 0">
            <td colspan="5" class="px-4 py-6 text-center text-slate-300">Nog geen programma-items voor deze editie.</td>
          </tr>
          <tr v-for="item in items" :key="item.id" class="border-t border-white/10">
            <td class="px-4 py-3 text-white">{{ item.titel }}</td>
            <td class="px-4 py-3 text-slate-300">{{ formatDateTime(item.start_at) }}</td>
            <td class="px-4 py-3 text-slate-300">{{ formatDateTime(item.end_at) }}</td>
            <td class="px-4 py-3" :class="item.is_published ? 'text-emerald-300' : 'text-amber-300'">
              {{ item.is_published ? 'Publiek' : 'Verborgen' }}
            </td>
            <td class="px-4 py-3">
              <div class="flex flex-wrap gap-2">
                <button class="rounded-md border border-robo-orange/60 px-3 py-1.5 text-xs font-semibold text-robo-orange hover:bg-robo-orange/15" @click="startEdit(item)">Bewerken</button>
                <button class="rounded-md border border-red-500/50 px-3 py-1.5 text-xs font-semibold text-red-300 hover:bg-red-500/15" @click="removeItem(item)">Verwijderen</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </section>
  </main>
</template>
