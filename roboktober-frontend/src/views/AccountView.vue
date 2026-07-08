<script setup lang="ts">
import { updateAccount, updatePassword } from '@/api'
import { useAuth } from '@/composables/useAuth'
import { onMounted, ref } from 'vue'

const auth = useAuth()

const name = ref('')
const email = ref('')
const currentPassword = ref('')
const newPassword = ref('')
const confirmPassword = ref('')

const accountStatus = ref<'idle' | 'opslaan'>('idle')
const passwordStatus = ref<'idle' | 'opslaan'>('idle')

const accountError = ref('')
const accountSuccess = ref('')
const passwordError = ref('')
const passwordSuccess = ref('')

onMounted(async () => {
  if (!auth.initialized.value) {
    await auth.initAuth()
  }

  if (auth.user.value) {
    name.value = auth.user.value.name
    email.value = auth.user.value.email
  }
})

async function saveAccount(): Promise<void> {
  accountStatus.value = 'opslaan'
  accountError.value = ''
  accountSuccess.value = ''

  try {
    const user = await updateAccount({
      name: name.value.trim(),
      email: email.value.trim(),
    })

    auth.user.value = user
    accountSuccess.value = 'Accountgegevens zijn bijgewerkt.'
  } catch {
    accountError.value = 'Bijwerken van accountgegevens is mislukt.'
  } finally {
    accountStatus.value = 'idle'
  }
}

async function savePassword(): Promise<void> {
  passwordStatus.value = 'opslaan'
  passwordError.value = ''
  passwordSuccess.value = ''

  if (newPassword.value !== confirmPassword.value) {
    passwordStatus.value = 'idle'
    passwordError.value = 'Nieuwe wachtwoorden komen niet overeen.'
    return
  }

  try {
    await updatePassword({
      current_password: currentPassword.value,
      password: newPassword.value,
      password_confirmation: confirmPassword.value,
    })

    currentPassword.value = ''
    newPassword.value = ''
    confirmPassword.value = ''
    passwordSuccess.value = 'Wachtwoord is bijgewerkt.'
  } catch {
    passwordError.value = 'Bijwerken van wachtwoord is mislukt.'
  } finally {
    passwordStatus.value = 'idle'
  }
}
</script>

<template>
  <main class="mx-auto min-h-[70vh] max-w-3xl px-6 py-16 text-white">
    <header class="mb-8">
      <h1 class="text-3xl font-black">Mijn account</h1>
      <p class="mt-2 text-slate-300">Beheer je profiel en wachtwoord.</p>
    </header>

    <section class="mb-8 rounded-xl border border-white/10 bg-robo-dark/60 p-6">
      <h2 class="mb-4 text-xl font-bold">Profiel</h2>

      <form class="space-y-4" @submit.prevent="saveAccount">
        <div>
          <label class="mb-2 block text-sm font-semibold text-slate-200" for="name">Naam</label>
          <input
            id="name"
            v-model="name"
            type="text"
            required
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
            class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white outline-none ring-robo-orange/70 transition focus:ring-2"
          />
        </div>

        <p v-if="accountError" class="rounded-md border border-red-400/40 bg-red-950/30 px-3 py-2 text-sm text-red-200">
          {{ accountError }}
        </p>

        <p v-if="accountSuccess" class="rounded-md border border-emerald-400/40 bg-emerald-950/30 px-3 py-2 text-sm text-emerald-200">
          {{ accountSuccess }}
        </p>

        <button
          type="submit"
          :disabled="accountStatus === 'opslaan'"
          class="rounded-lg bg-robo-orange px-4 py-2.5 font-bold text-white transition hover:bg-robo-orange-dark disabled:cursor-not-allowed disabled:opacity-60"
        >
          {{ accountStatus === 'opslaan' ? 'Opslaan...' : 'Profiel opslaan' }}
        </button>
      </form>
    </section>

    <section class="rounded-xl border border-white/10 bg-robo-dark/60 p-6">
      <h2 class="mb-4 text-xl font-bold">Wachtwoord wijzigen</h2>

      <form class="space-y-4" @submit.prevent="savePassword">
        <div>
          <label class="mb-2 block text-sm font-semibold text-slate-200" for="current_password">Huidig wachtwoord</label>
          <input
            id="current_password"
            v-model="currentPassword"
            type="password"
            required
            autocomplete="current-password"
            class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white outline-none ring-robo-orange/70 transition focus:ring-2"
          />
        </div>

        <div>
          <label class="mb-2 block text-sm font-semibold text-slate-200" for="new_password">Nieuw wachtwoord</label>
          <input
            id="new_password"
            v-model="newPassword"
            type="password"
            required
            autocomplete="new-password"
            class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white outline-none ring-robo-orange/70 transition focus:ring-2"
          />
        </div>

        <div>
          <label class="mb-2 block text-sm font-semibold text-slate-200" for="new_password_confirmation">Herhaal nieuw wachtwoord</label>
          <input
            id="new_password_confirmation"
            v-model="confirmPassword"
            type="password"
            required
            autocomplete="new-password"
            class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-white outline-none ring-robo-orange/70 transition focus:ring-2"
          />
        </div>

        <p v-if="passwordError" class="rounded-md border border-red-400/40 bg-red-950/30 px-3 py-2 text-sm text-red-200">
          {{ passwordError }}
        </p>

        <p v-if="passwordSuccess" class="rounded-md border border-emerald-400/40 bg-emerald-950/30 px-3 py-2 text-sm text-emerald-200">
          {{ passwordSuccess }}
        </p>

        <button
          type="submit"
          :disabled="passwordStatus === 'opslaan'"
          class="rounded-lg bg-robo-orange px-4 py-2.5 font-bold text-white transition hover:bg-robo-orange-dark disabled:cursor-not-allowed disabled:opacity-60"
        >
          {{ passwordStatus === 'opslaan' ? 'Opslaan...' : 'Wachtwoord opslaan' }}
        </button>
      </form>
    </section>
  </main>
</template>
