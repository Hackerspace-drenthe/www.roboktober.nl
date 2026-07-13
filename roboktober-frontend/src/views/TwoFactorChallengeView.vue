<script setup lang="ts">
import { completeTwoFactorChallenge, setAuthToken } from '@/api'
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuth } from '@/composables/useAuth'

const route = useRoute()
const router = useRouter()
const auth = useAuth()

const challengeId = typeof route.query.challenge === 'string' ? route.query.challenge : ''
const redirectTarget = typeof route.query.redirect === 'string' && route.query.redirect.startsWith('/')
  ? route.query.redirect
  : '/'

const code = ref('')
const loading = ref(false)
const error = ref('')

async function handleSubmit(): Promise<void> {
  loading.value = true
  error.value = ''

  if (!challengeId) {
    loading.value = false
    error.value = '2FA challenge ontbreekt. Log opnieuw in.'
    return
  }

  try {
    const response = await completeTwoFactorChallenge({
      challenge_id: challengeId,
      code: code.value.trim(),
      device_name: 'web-app',
    })

    if (!response.token) {
      throw new Error('Missing API token after 2FA challenge.')
    }

    setAuthToken(response.token)
    await auth.refreshMe()

    await router.push(redirectTarget)
  } catch {
    error.value = 'Ongeldige of verlopen 2FA code. Probeer opnieuw in te loggen.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <main class="mx-auto min-h-[70vh] max-w-xl px-6 py-16 text-white">
    <header class="mb-8">
      <h1 class="text-3xl font-black">2FA verificatie</h1>
      <p class="mt-2 text-slate-300">Voer de 6-cijferige code uit je authenticator app in om in te loggen.</p>
    </header>

    <form class="space-y-5 rounded-xl border border-white/10 bg-robo-dark/60 p-6" @submit.prevent="handleSubmit">
      <div>
        <label class="mb-2 block text-sm font-semibold text-slate-200" for="code">2FA code</label>
        <input
          id="code"
          v-model="code"
          type="text"
          inputmode="numeric"
          pattern="[0-9]{6}"
          maxlength="6"
          minlength="6"
          required
          class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white outline-none ring-robo-orange/70 transition focus:ring-2"
        />
      </div>

      <p v-if="error" class="rounded-md border border-red-400/40 bg-red-950/30 px-3 py-2 text-sm text-red-200">
        {{ error }}
      </p>

      <button
        type="submit"
        :disabled="loading"
        class="w-full rounded-lg bg-robo-orange px-4 py-2.5 font-bold text-white transition hover:bg-robo-orange-dark disabled:cursor-not-allowed disabled:opacity-60"
      >
        {{ loading ? 'Verifiëren...' : 'Inloggen afronden' }}
      </button>
    </form>
  </main>
</template>
