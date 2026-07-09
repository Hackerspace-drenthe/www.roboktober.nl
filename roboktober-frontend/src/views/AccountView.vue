<script setup lang="ts">
import { updateAccount, updatePassword, uploadRichMedia } from '@/api'
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
const photoStatus = ref<'idle' | 'uploaden'>('idle')
const photoError = ref('')
const photoSuccess = ref('')
const profilePhotoUrl = ref<string | null>(null)

const TOEGESTANE_FOTO_MIME_TYPES = new Set(['image/jpeg', 'image/png', 'image/webp'])
const FOTO_MAX_BYTES = 50 * 1024 * 1024

onMounted(async () => {
  if (!auth.initialized.value) {
    await auth.initAuth()
  }

  if (auth.user.value) {
    name.value = auth.user.value.name
    email.value = auth.user.value.email
    profilePhotoUrl.value = auth.user.value.profile_photo?.url ?? null
  }
})

async function uploadProfilePhoto(event: Event): Promise<void> {
  const target = event.target as HTMLInputElement
  const bestand = target.files?.[0] ?? null

  photoError.value = ''
  photoSuccess.value = ''

  if (!bestand || !auth.user.value) {
    return
  }

  if (!TOEGESTANE_FOTO_MIME_TYPES.has(bestand.type)) {
    photoError.value = 'Gebruik een JPG, PNG of WEBP bestand.'
    target.value = ''
    return
  }

  if (bestand.size > FOTO_MAX_BYTES) {
    photoError.value = 'De profielfoto mag maximaal 50 MB groot zijn.'
    target.value = ''
    return
  }

  photoStatus.value = 'uploaden'

  try {
    await uploadRichMedia({
      bestand,
      target_type: 'user',
      target_id: auth.user.value.id,
      collectie: 'foto',
      alt_tekst: `Profielfoto van ${auth.user.value.name}`,
      onderschrift: 'Profielfoto',
      volgorde: 0,
    })

    const refreshedUser = await auth.refreshMe()
    if (refreshedUser) {
      profilePhotoUrl.value = refreshedUser.profile_photo?.url ?? null
    }

    photoSuccess.value = 'Profielfoto bijgewerkt.'
  } catch {
    photoError.value = 'Uploaden van profielfoto is mislukt.'
  } finally {
    photoStatus.value = 'idle'
    target.value = ''
  }
}

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

      <div class="mb-6 rounded-xl border border-white/10 bg-black/20 p-4">
        <p class="mb-3 text-sm font-semibold text-slate-200">Profielfoto</p>
        <div class="flex flex-wrap items-center gap-4">
          <img
            v-if="profilePhotoUrl"
            :src="profilePhotoUrl"
            alt="Profielfoto"
            class="h-20 w-20 rounded-full border border-white/20 object-cover"
          />
          <div
            v-else
            class="flex h-20 w-20 items-center justify-center rounded-full border border-dashed border-white/25 bg-white/5 text-xs text-slate-300"
          >
            Geen foto
          </div>

          <div class="min-w-[220px] flex-1">
            <input
              type="file"
              accept="image/jpeg,image/png,image/webp"
              class="w-full rounded-lg border border-white/15 bg-slate-900 px-3 py-2 text-sm text-slate-100"
              :disabled="photoStatus === 'uploaden'"
              @change="uploadProfilePhoto"
            />
            <p class="mt-2 text-xs text-slate-400">JPG, PNG of WEBP · max 50 MB</p>
          </div>
        </div>

        <p v-if="photoError" class="mt-3 rounded-md border border-red-400/40 bg-red-950/30 px-3 py-2 text-sm text-red-200">
          {{ photoError }}
        </p>

        <p v-if="photoSuccess" class="mt-3 rounded-md border border-emerald-400/40 bg-emerald-950/30 px-3 py-2 text-sm text-emerald-200">
          {{ photoSuccess }}
        </p>
      </div>

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
