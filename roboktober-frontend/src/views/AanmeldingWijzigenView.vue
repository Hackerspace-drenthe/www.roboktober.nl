<script setup lang="ts">
import { issueTeamEditLink } from '@/api'
import headerImage from '@/assets/headers/header-aanmelden.png'
import { useAuth } from '@/composables/useAuth'
import { onMounted, ref } from 'vue'

const auth = useAuth()
const uitgifteStatus = ref<'idle' | 'laden'>('idle')
const uitgifteFout = ref('')

onMounted(async () => {
  if (!auth.initialized.value) {
    await auth.initAuth()
  }

  if (auth.isAuthenticated.value) {
    await openMijnBewerkLink()
  }
})

async function openMijnBewerkLink(): Promise<void> {
  uitgifteStatus.value = 'laden'
  uitgifteFout.value = ''

  try {
    const data = await issueTeamEditLink()
    window.location.assign(data.edit_url)
  } catch (error: unknown) {
    if (
      error !== null &&
      typeof error === 'object' &&
      'response' in error &&
      error.response !== null &&
      typeof error.response === 'object' &&
      'data' in error.response &&
      error.response.data !== null &&
      typeof error.response.data === 'object' &&
      'message' in error.response.data &&
      typeof error.response.data.message === 'string'
    ) {
      uitgifteFout.value = error.response.data.message
    } else {
      uitgifteFout.value = 'Bewerklink ophalen mislukt. Probeer het opnieuw.'
    }
  } finally {
    uitgifteStatus.value = 'idle'
  }
}

const heroStyle = {
  backgroundImage: `url(${headerImage})`,
  backgroundSize: 'cover',
  backgroundPosition: 'center',
}
</script>

<template>
  <main class="min-h-screen">
    <section class="relative overflow-hidden py-20 text-white" :style="heroStyle">
      <div class="absolute inset-0 bg-robo-dark/75" aria-hidden="true" />
      <div class="relative z-10 mx-auto max-w-3xl px-6 text-center">
        <h1 class="mb-4 text-4xl font-black">Aanmelding <span class="text-robo-orange">wijzigen</span></h1>
        <p class="text-slate-300">
          Je wordt automatisch doorgestuurd naar je eigen bewerkomgeving.
        </p>
      </div>
    </section>

    <section class="bg-robo-dark py-16 text-white">
      <div class="mx-auto max-w-3xl space-y-6 px-6">
        <article class="rounded-xl border border-white/15 bg-white/5 p-6">
          <p class="text-slate-200" v-if="uitgifteStatus === 'laden'">
            Beherpagina openen...
          </p>

          <template v-else-if="uitgifteFout">
            <h2 class="mb-2 text-xl font-bold text-amber-300">Bewerkomgeving niet beschikbaar</h2>
            <p class="text-slate-200">
              {{ uitgifteFout }}
            </p>
            <div class="mt-4 flex flex-wrap gap-3">
              <button
                type="button"
                class="rounded-lg bg-robo-orange px-4 py-2 font-bold text-white transition hover:bg-robo-orange-dark"
                @click="openMijnBewerkLink"
              >
                Opnieuw proberen
              </button>
              <RouterLink
                to="/aanmelden"
                class="rounded-lg border border-white/20 px-4 py-2 font-semibold text-slate-200 transition hover:bg-white/10"
              >
                Nieuwe aanmelding maken
              </RouterLink>
            </div>
          </template>
        </article>
      </div>
    </section>
  </main>
</template>
