<script setup lang="ts">
import { requestPasswordReset } from '@/api'
import { ref } from 'vue'

const email = ref('')
const loading = ref(false)
const error = ref('')
const success = ref('')

async function handleSubmit(): Promise<void> {
  loading.value = true
  error.value = ''
  success.value = ''

  try {
    await requestPasswordReset({ email: email.value.trim() })
    success.value = 'Als dit e-mailadres bekend is, ontvang je een resetlink.'
  } catch {
    error.value = 'Versturen van resetlink is mislukt.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <main class="mx-auto min-h-[70vh] max-w-xl px-6 py-16 text-white">
    <header class="mb-8">
      <h1 class="text-3xl font-black">Wachtwoord vergeten</h1>
      <p class="mt-2 text-slate-300">Vraag een resetlink aan via e-mail.</p>
    </header>

    <form class="space-y-5 rounded-xl border border-white/10 bg-robo-dark/60 p-6" @submit.prevent="handleSubmit">
      <div>
        <label class="mb-2 block text-sm font-semibold text-slate-200" for="email">E-mailadres</label>
        <input
          id="email"
          v-model="email"
          type="email"
          required
          autocomplete="email"
          class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white outline-none ring-robo-orange/70 transition focus:ring-2"
        />
      </div>

      <p v-if="error" class="rounded-md border border-red-400/40 bg-red-950/30 px-3 py-2 text-sm text-red-200">
        {{ error }}
      </p>

      <p v-if="success" class="rounded-md border border-emerald-400/40 bg-emerald-950/30 px-3 py-2 text-sm text-emerald-200">
        {{ success }}
      </p>

      <button
        type="submit"
        :disabled="loading"
        class="w-full rounded-lg bg-robo-orange px-4 py-2.5 font-bold text-white transition hover:bg-robo-orange-dark disabled:cursor-not-allowed disabled:opacity-60"
      >
        {{ loading ? 'Versturen...' : 'Resetlink versturen' }}
      </button>
    </form>
  </main>
</template>
