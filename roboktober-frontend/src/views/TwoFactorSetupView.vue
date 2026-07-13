<script setup lang="ts">
import { confirmTwoFactorSetup, getTwoFactorSetup, setAuthToken } from '@/api'
import QRCode from 'qrcode'
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuth } from '@/composables/useAuth'
import type { TwoFactorProvisioning } from '@/types/api'

const route = useRoute()
const router = useRouter()
const auth = useAuth()

const provisioning = ref<TwoFactorProvisioning | null>(null)
const code = ref('')
const loading = ref(false)
const bootstrapping = ref(true)
const error = ref('')
const success = ref('')
const qrCodeDataUrl = ref('')
const showBackupSecret = ref(false)

const redirectTarget = typeof route.query.redirect === 'string' && route.query.redirect.startsWith('/')
  ? route.query.redirect
  : '/'

onMounted(async () => {
  bootstrapping.value = true
  error.value = ''

  try {
    provisioning.value = await getTwoFactorSetup()

    if (provisioning.value?.otpauth_url) {
      qrCodeDataUrl.value = await QRCode.toDataURL(provisioning.value.otpauth_url, {
        errorCorrectionLevel: 'M',
        margin: 1,
        width: 280,
      })
    }
  } catch {
    error.value = 'Kon de 2FA setupgegevens niet laden. Log opnieuw in en probeer opnieuw.'
  } finally {
    bootstrapping.value = false
  }
})

async function handleConfirm(): Promise<void> {
  loading.value = true
  error.value = ''
  success.value = ''

  try {
    const response = await confirmTwoFactorSetup({
      code: code.value.trim(),
      device_name: 'web-app',
    })

    if (!response.token) {
      throw new Error('Missing API token after 2FA setup confirmation.')
    }

    setAuthToken(response.token)
    await auth.refreshMe()

    success.value = 'Twee-factor-authenticatie is actief. Je wordt doorgestuurd.'

    setTimeout(() => {
      void router.push(redirectTarget)
    }, 600)
  } catch {
    error.value = 'Ongeldige code of setup mislukt. Controleer je authenticator en probeer opnieuw.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <main class="mx-auto min-h-[70vh] max-w-xl px-6 py-16 text-white">
    <header class="mb-8">
      <h1 class="text-3xl font-black">Stel 2FA in</h1>
      <p class="mt-2 text-slate-300">Verplicht voor alle accounts. Voeg je account toe in een authenticator app en bevestig met een code.</p>
    </header>

    <section class="space-y-5 rounded-xl border border-white/10 bg-robo-dark/60 p-6">
      <p v-if="bootstrapping" class="text-slate-300">Setupgegevens laden...</p>

      <template v-else>
        <div v-if="provisioning" class="space-y-3">
          <p class="text-sm text-slate-200">1. Scan de QR-code met je authenticator app (Google Authenticator, 1Password, Authy, etc.).</p>

          <div class="flex justify-center rounded-xl border border-white/15 bg-white p-4">
            <img v-if="qrCodeDataUrl" :src="qrCodeDataUrl" alt="2FA QR code" class="h-56 w-56" />
            <p v-else class="text-sm text-slate-500">QR-code genereren...</p>
          </div>

          <div class="rounded-lg border border-white/10 bg-black/20 p-3">
            <button
              type="button"
              class="text-sm font-semibold text-slate-200 underline decoration-dotted underline-offset-4"
              @click="showBackupSecret = !showBackupSecret"
            >
              {{ showBackupSecret ? 'Verberg backup secret' : 'Toon backup secret (alleen als scannen niet lukt)' }}
            </button>

            <ul v-if="showBackupSecret" class="mt-3 space-y-2 text-sm text-slate-300">
              <li><span class="font-semibold text-white">Issuer:</span> {{ provisioning.issuer }}</li>
              <li><span class="font-semibold text-white">Account:</span> {{ provisioning.account }}</li>
              <li class="break-all"><span class="font-semibold text-white">Secret:</span> {{ provisioning.secret }}</li>
              <li class="break-all text-xs text-slate-400">otpauth: {{ provisioning.otpauth_url }}</li>
            </ul>
          </div>
        </div>

        <form class="space-y-4" @submit.prevent="handleConfirm">
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

          <p v-if="success" class="rounded-md border border-emerald-400/40 bg-emerald-950/30 px-3 py-2 text-sm text-emerald-200">
            {{ success }}
          </p>

          <button
            type="submit"
            :disabled="loading || !provisioning"
            class="w-full rounded-lg bg-robo-orange px-4 py-2.5 font-bold text-white transition hover:bg-robo-orange-dark disabled:cursor-not-allowed disabled:opacity-60"
          >
            {{ loading ? 'Bevestigen...' : '2FA activeren' }}
          </button>
        </form>
      </template>
    </section>
  </main>
</template>
