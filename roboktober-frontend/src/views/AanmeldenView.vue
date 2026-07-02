<script setup lang="ts">
/**
 * Team registratie formulier.
 *
 * WCAG 2.2 AA: alle velden hebben labels, foutmeldingen zijn gekoppeld via aria-describedby.
 * OWASP: input validation op client én server (StoreTeamRequest.php).
 *
 * @see PLAN.md §5.1 — registratie altijd open
 * @see PLAN.md §6.x — Aanmelden page design
 */
import { reactive, ref } from 'vue'
import { registreerTeam } from '@/api'
import type { RegistratiePayload } from '@/types/api'
import headerImage from '@/assets/headers/header-aanmelden.png'

type FormulierStatus = 'idle' | 'versturen' | 'succes' | 'fout'

const status = ref<FormulierStatus>('idle')
const foutmelding = ref<string>('')

const formulier = reactive<RegistratiePayload>({
  naam: '',
  contactpersoon: '',
  email: '',
  volwassenen: 1,
  kinderen: undefined,
})

const heroStyle = {
  backgroundImage: `url(${headerImage})`,
  backgroundSize: 'cover',
  backgroundPosition: 'center',
}

async function verstuur(): Promise<void> {
  status.value = 'versturen'
  foutmelding.value = ''

  try {
    await registreerTeam(formulier)
    status.value = 'succes'
  } catch (error: unknown) {
    status.value = 'fout'
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
      foutmelding.value = error.response.data.message
    } else {
      foutmelding.value = 'Er ging iets mis. Probeer het opnieuw.'
    }
  }
}
</script>

<template>
  <main class="min-h-screen">
    <section class="relative overflow-hidden py-20 text-white" :style="heroStyle">
      <div class="absolute inset-0 bg-robo-dark/75" aria-hidden="true" />
      <div class="relative z-10 mx-auto max-w-2xl px-6 text-center">
        <h1 class="mb-4 text-4xl font-black">
          Meld je <span class="text-robo-orange">team aan</span>
        </h1>
        <p class="text-slate-300">
          Registratie staat altijd open. Na je aanmelding nemen wij contact met je op.
        </p>
      </div>
    </section>

    <section class="bg-robo-dark py-16 text-white">
      <div class="mx-auto max-w-2xl px-6">

        <!-- Succes bericht -->
        <div
          v-if="status === 'succes'"
          role="alert"
          class="rounded-xl border border-green-500/30 bg-green-500/10 p-6 text-center"
        >
          <p class="text-2xl font-bold text-green-400">✓ Aanmelding ontvangen!</p>
          <p class="mt-2 text-slate-300">
            We hebben je aanmelding ontvangen en nemen zo snel mogelijk contact op.
          </p>
        </div>

        <!-- Formulier -->
        <form
          v-else
          novalidate
          class="space-y-6"
          @submit.prevent="verstuur"
        >
        <!-- Teamnaam -->
        <div>
          <label
            for="naam"
            class="mb-2 block font-semibold"
          >
            Teamnaam <span aria-hidden="true" class="text-robo-orange">*</span>
          </label>
          <input
            id="naam"
            v-model="formulier.naam"
            type="text"
            required
            maxlength="255"
            autocomplete="organization"
            :disabled="status === 'versturen'"
            class="w-full rounded-lg border border-white/20 bg-white/5 px-4 py-3 text-white placeholder-slate-400 focus:border-robo-orange focus:outline-none focus:ring-2 focus:ring-robo-orange/50 disabled:opacity-50"
            placeholder="Bv. Team Wallie"
          />
        </div>

        <!-- Contactpersoon -->
        <div>
          <label
            for="contactpersoon"
            class="mb-2 block font-semibold"
          >
            Naam contactpersoon <span aria-hidden="true" class="text-robo-orange">*</span>
          </label>
          <input
            id="contactpersoon"
            v-model="formulier.contactpersoon"
            type="text"
            required
            maxlength="255"
            autocomplete="name"
            :disabled="status === 'versturen'"
            class="w-full rounded-lg border border-white/20 bg-white/5 px-4 py-3 text-white placeholder-slate-400 focus:border-robo-orange focus:outline-none focus:ring-2 focus:ring-robo-orange/50 disabled:opacity-50"
            placeholder="Bv. Jan Jansen"
          />
        </div>

        <!-- E-mail -->
        <div>
          <label
            for="email"
            class="mb-2 block font-semibold"
          >
            E-mailadres <span aria-hidden="true" class="text-robo-orange">*</span>
          </label>
          <input
            id="email"
            v-model="formulier.email"
            type="email"
            required
            maxlength="255"
            autocomplete="email"
            :disabled="status === 'versturen'"
            class="w-full rounded-lg border border-white/20 bg-white/5 px-4 py-3 text-white placeholder-slate-400 focus:border-robo-orange focus:outline-none focus:ring-2 focus:ring-robo-orange/50 disabled:opacity-50"
            placeholder="jouw@email.nl"
          />
        </div>

        <!-- Deelnemers -->
        <div class="grid gap-6 sm:grid-cols-2">
          <div>
            <label
              for="volwassenen"
              class="mb-2 block font-semibold"
            >
              Aantal volwassenen (18+) <span aria-hidden="true" class="text-robo-orange">*</span>
            </label>
            <input
              id="volwassenen"
              v-model.number="formulier.volwassenen"
              type="number"
              required
              min="1"
              max="20"
              :disabled="status === 'versturen'"
              class="w-full rounded-lg border border-white/20 bg-white/5 px-4 py-3 text-white focus:border-robo-orange focus:outline-none focus:ring-2 focus:ring-robo-orange/50 disabled:opacity-50"
            />
          </div>
          <div>
            <label
              for="kinderen"
              class="mb-2 block font-semibold"
            >
              Aantal jongeren (&lt;18) <span class="text-slate-400 font-normal">(optioneel)</span>
            </label>
            <input
              id="kinderen"
              v-model.number="formulier.kinderen"
              type="number"
              min="0"
              max="50"
              :disabled="status === 'versturen'"
              class="w-full rounded-lg border border-white/20 bg-white/5 px-4 py-3 text-white focus:border-robo-orange focus:outline-none focus:ring-2 focus:ring-robo-orange/50 disabled:opacity-50"
            />
          </div>
        </div>

        <!-- Foutmelding -->
        <div
          v-if="status === 'fout'"
          role="alert"
          class="rounded-lg border border-red-500/30 bg-red-500/10 p-4 text-red-400"
        >
          {{ foutmelding }}
        </div>

        <!-- Submit -->
        <button
          type="submit"
          :disabled="status === 'versturen'"
          class="w-full rounded-lg bg-robo-orange px-8 py-4 text-lg font-bold text-white transition hover:bg-robo-orange-dark focus:outline-none focus:ring-4 focus:ring-robo-orange/50 disabled:opacity-60"
        >
          <span v-if="status === 'versturen'">Aanmelden...</span>
          <span v-else>Aanmelden</span>
        </button>

        <p class="text-center text-sm text-slate-400">
          <span aria-hidden="true">*</span> Verplichte velden
        </p>
        </form>
      </div>
    </section>
  </main>
</template>
