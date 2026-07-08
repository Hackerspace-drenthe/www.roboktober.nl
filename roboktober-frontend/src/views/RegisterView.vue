<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '@/composables/useAuth'

const router = useRouter()
const auth = useAuth()

const name = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const errorMessage = ref<string | null>(null)

async function handleSubmit(): Promise<void> {
  errorMessage.value = null

  if (password.value !== passwordConfirmation.value) {
    errorMessage.value = 'Wachtwoorden komen niet overeen.'
    return
  }

  try {
    await auth.register({
      name: name.value.trim(),
      email: email.value.trim(),
      password: password.value,
      password_confirmation: passwordConfirmation.value,
    })

    await router.push('/')
  } catch {
    errorMessage.value = 'Registreren mislukt. Controleer je invoer en probeer opnieuw.'
  }
}
</script>

<template>
  <main class="mx-auto min-h-[70vh] max-w-xl px-6 py-16">
    <header class="mb-8">
      <h1 class="text-3xl font-black text-white">Registreren</h1>
      <p class="mt-2 text-slate-300">Maak een account aan voor teambeheer en moderatie via de API.</p>
    </header>

    <form class="space-y-5 rounded-xl border border-white/10 bg-robo-dark/60 p-6" @submit.prevent="handleSubmit">
      <div>
        <label class="mb-2 block text-sm font-semibold text-slate-200" for="name">Naam</label>
        <input
          id="name"
          v-model="name"
          type="text"
          required
          autocomplete="name"
          class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white outline-none ring-robo-orange/70 transition focus:ring-2"
        />
      </div>

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

      <div>
        <label class="mb-2 block text-sm font-semibold text-slate-200" for="password">Wachtwoord</label>
        <input
          id="password"
          v-model="password"
          type="password"
          required
          autocomplete="new-password"
          class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white outline-none ring-robo-orange/70 transition focus:ring-2"
        />
      </div>

      <div>
        <label class="mb-2 block text-sm font-semibold text-slate-200" for="password_confirmation">Herhaal wachtwoord</label>
        <input
          id="password_confirmation"
          v-model="passwordConfirmation"
          type="password"
          required
          autocomplete="new-password"
          class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white outline-none ring-robo-orange/70 transition focus:ring-2"
        />
      </div>

      <p v-if="errorMessage" class="rounded-md border border-red-400/40 bg-red-950/30 px-3 py-2 text-sm text-red-200">
        {{ errorMessage }}
      </p>

      <button
        type="submit"
        :disabled="auth.loading.value"
        class="w-full rounded-lg bg-robo-orange px-4 py-2.5 font-bold text-white transition hover:bg-robo-orange-dark disabled:cursor-not-allowed disabled:opacity-60"
      >
        {{ auth.loading.value ? 'Bezig...' : 'Registreren' }}
      </button>

      <p class="text-center text-sm text-slate-400">
        Al een account?
        <RouterLink class="font-semibold text-robo-orange hover:text-robo-orange-dark" to="/login">Log hier in</RouterLink>
      </p>
    </form>
  </main>
</template>
