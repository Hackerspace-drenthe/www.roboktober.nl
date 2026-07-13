<script setup lang="ts">
import { computed, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuth } from '@/composables/useAuth'

const router = useRouter()
const route = useRoute()
const auth = useAuth()

const email = ref('')
const password = ref('')
const errorMessage = ref<string | null>(null)

const redirectTarget = computed(() => {
  const redirect = route.query.redirect
  return typeof redirect === 'string' && redirect.startsWith('/') ? redirect : '/'
})

async function handleSubmit(): Promise<void> {
  errorMessage.value = null

  try {
    const response = await auth.login({
      email: email.value.trim(),
      password: password.value,
      device_name: 'web-app',
    })

    if (response.two_factor_required && typeof response.two_factor_challenge_id === 'string') {
      await router.push({
        name: 'twee-factor-challenge',
        query: {
          challenge: response.two_factor_challenge_id,
          redirect: redirectTarget.value,
        },
      })
      return
    }

    if (response.two_factor_setup_required) {
      await router.push({
        name: 'twee-factor-setup',
        query: { redirect: redirectTarget.value },
      })
      return
    }

    await router.push(redirectTarget.value)
  } catch {
    errorMessage.value = 'Inloggen mislukt. Controleer je e-mailadres en wachtwoord.'
  }
}
</script>

<template>
  <main class="mx-auto min-h-[70vh] max-w-xl px-6 py-16">
    <header class="mb-8">
      <h1 class="text-3xl font-black text-white">Inloggen</h1>
      <p class="mt-2 text-slate-300">Log in om je team en admin-acties via de API te beheren.</p>
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

      <div>
        <label class="mb-2 block text-sm font-semibold text-slate-200" for="password">Wachtwoord</label>
        <input
          id="password"
          v-model="password"
          type="password"
          required
          autocomplete="current-password"
          class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white outline-none ring-robo-orange/70 transition focus:ring-2"
        />
        <div class="mt-2 text-right">
          <RouterLink class="text-sm font-semibold text-robo-orange hover:text-robo-orange-dark" to="/wachtwoord-vergeten">
            Wachtwoord vergeten?
          </RouterLink>
        </div>
      </div>

      <p v-if="errorMessage" class="rounded-md border border-red-400/40 bg-red-950/30 px-3 py-2 text-sm text-red-200">
        {{ errorMessage }}
      </p>

      <button
        type="submit"
        :disabled="auth.loading.value"
        class="w-full rounded-lg bg-robo-orange px-4 py-2.5 font-bold text-white transition hover:bg-robo-orange-dark disabled:cursor-not-allowed disabled:opacity-60"
      >
        {{ auth.loading.value ? 'Bezig...' : 'Inloggen' }}
      </button>

      <p class="text-center text-sm text-slate-400">
        Nog geen account?
        <RouterLink class="font-semibold text-robo-orange hover:text-robo-orange-dark" to="/registreren">Registreer hier</RouterLink>
      </p>
    </form>
  </main>
</template>
