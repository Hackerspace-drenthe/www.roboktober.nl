<script setup lang="ts">
const props = withDefaults(defineProps<{
  disabled?: boolean
}>(), {
  disabled: false,
})

const emit = defineEmits<{
  (e: 'action', action: 'bold' | 'italic' | 'h2' | 'h3' | 'ul' | 'ol' | 'link' | 'quote' | 'code' | 'divider'): void
}>()

const buttons: Array<{ key: 'bold' | 'italic' | 'h2' | 'h3' | 'ul' | 'ol' | 'link' | 'quote' | 'code' | 'divider'; label: string; title: string }> = [
  { key: 'bold', label: 'B', title: 'Vet' },
  { key: 'italic', label: 'I', title: 'Cursief' },
  { key: 'h2', label: 'H2', title: 'Kop 2' },
  { key: 'h3', label: 'H3', title: 'Kop 3' },
  { key: 'ul', label: '• Lijst', title: 'Bulletlijst' },
  { key: 'ol', label: '1. Lijst', title: 'Genummerde lijst' },
  { key: 'link', label: 'Link', title: 'Link toevoegen' },
  { key: 'quote', label: 'Quote', title: 'Citaatblok' },
  { key: 'code', label: 'Code', title: 'Codeblok' },
  { key: 'divider', label: 'Lijn', title: 'Scheidslijn' },
]
</script>

<template>
  <section class="rounded-xl border border-white/15 bg-black/20 p-3">
    <p class="mb-2 text-sm font-semibold text-slate-200">Snel opmaken</p>

    <div class="flex flex-wrap gap-2">
      <button
        v-for="button in buttons"
        :key="button.key"
        type="button"
        class="rounded border border-white/25 px-3 py-1.5 text-sm font-semibold text-slate-100 hover:bg-white/10 disabled:opacity-60"
        :title="button.title"
        :disabled="props.disabled"
        @click="emit('action', button.key)"
      >
        {{ button.label }}
      </button>
    </div>
  </section>
</template>
