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
const teamfotoNaam = ref<string>('')

const formulier = reactive<RegistratiePayload>({
  naam: '',
  contactpersoon: '',
  email: '',
  volwassenen: 1,
  kinderen: undefined,
  opmerkingen: '',
  teamfoto: null,
  robots: [
    {
      naam: '',
      gewichtsklasse: 'antweight',
      beschrijving: '',
    },
  ],
})

const heroStyle = {
  backgroundImage: `url(${headerImage})`,
  backgroundSize: 'cover',
  backgroundPosition: 'center',
}

function voegRobotToe(): void {
  formulier.robots.push({
    naam: '',
    gewichtsklasse: 'antweight',
    beschrijving: '',
  })
}

function verwijderRobot(index: number): void {
  if (formulier.robots.length <= 1) {
    return
  }

  formulier.robots.splice(index, 1)
}

function wijzigTeamfoto(event: Event): void {
  const target = event.target as HTMLInputElement
  const bestand = target.files?.[0] ?? null

  formulier.teamfoto = bestand
  teamfotoNaam.value = bestand?.name ?? ''
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

        <!-- Teamfoto -->
        <div>
          <label for="teamfoto" class="mb-2 block font-semibold">
            Teamfoto <span class="text-slate-400 font-normal">(optioneel)</span>
          </label>
          <input
            id="teamfoto"
            type="file"
            accept="image/*"
            :disabled="status === 'versturen'"
            class="w-full rounded-lg border border-white/20 bg-white/5 px-4 py-3 text-sm text-slate-200 file:mr-4 file:rounded file:border-0 file:bg-robo-orange file:px-3 file:py-2 file:font-semibold file:text-white hover:file:bg-robo-orange-dark focus:border-robo-orange focus:outline-none focus:ring-2 focus:ring-robo-orange/50 disabled:opacity-50"
            @change="wijzigTeamfoto"
          />
          <p v-if="teamfotoNaam" class="mt-2 text-sm text-slate-300">
            Gekozen bestand: {{ teamfotoNaam }}
          </p>
        </div>

        <!-- Robots -->
        <div class="space-y-4 rounded-xl border border-white/15 bg-white/5 p-4">
          <div class="flex items-center justify-between gap-4">
            <h2 class="text-lg font-bold">Robots in dit team</h2>
            <button
              type="button"
              :disabled="status === 'versturen'"
              class="rounded-lg border border-robo-orange px-3 py-2 text-sm font-semibold text-robo-orange transition hover:bg-robo-orange hover:text-white disabled:opacity-60"
              @click="voegRobotToe"
            >
              + Robot toevoegen
            </button>
          </div>

          <p class="text-sm text-slate-300">
            Voeg minimaal 1 robot toe. Een team mag meerdere robots inschrijven.
          </p>

          <article
            v-for="(robot, index) in formulier.robots"
            :key="index"
            class="space-y-4 rounded-lg border border-white/10 bg-black/20 p-4"
          >
            <div class="flex items-center justify-between gap-4">
              <h3 class="font-semibold">Robot {{ index + 1 }}</h3>
              <button
                type="button"
                :disabled="status === 'versturen' || formulier.robots.length <= 1"
                class="rounded border border-red-400 px-2 py-1 text-xs font-semibold text-red-300 transition hover:bg-red-500/20 disabled:opacity-40"
                @click="verwijderRobot(index)"
              >
                Verwijder
              </button>
            </div>

            <div>
              <label :for="`robot-naam-${index}`" class="mb-2 block font-semibold">
                Robotnaam <span aria-hidden="true" class="text-robo-orange">*</span>
              </label>
              <input
                :id="`robot-naam-${index}`"
                v-model="robot.naam"
                type="text"
                required
                maxlength="255"
                :disabled="status === 'versturen'"
                class="w-full rounded-lg border border-white/20 bg-white/5 px-4 py-3 text-white placeholder-slate-400 focus:border-robo-orange focus:outline-none focus:ring-2 focus:ring-robo-orange/50 disabled:opacity-50"
                placeholder="Bv. Kecil"
              />
            </div>

            <div>
              <label :for="`robot-klasse-${index}`" class="mb-2 block font-semibold">
                Gewichtsklasse <span aria-hidden="true" class="text-robo-orange">*</span>
              </label>
              <select
                :id="`robot-klasse-${index}`"
                v-model="robot.gewichtsklasse"
                required
                :disabled="status === 'versturen'"
                class="w-full rounded-lg border border-white/20 bg-robo-dark px-4 py-3 text-white focus:border-robo-orange focus:outline-none focus:ring-2 focus:ring-robo-orange/50 disabled:opacity-50"
              >
                <option value="antweight">Antweight (max. 150 g)</option>
                <option value="beetleweight">Beetleweight (max. 1,36 kg)</option>
                <option value="featherweight">Featherweight (max. 13,6 kg)</option>
              </select>
            </div>

            <div>
              <label :for="`robot-beschrijving-${index}`" class="mb-2 block font-semibold">
                Beschrijving <span class="text-slate-400 font-normal">(optioneel)</span>
              </label>
              <textarea
                :id="`robot-beschrijving-${index}`"
                v-model="robot.beschrijving"
                rows="3"
                maxlength="1000"
                :disabled="status === 'versturen'"
                class="w-full rounded-lg border border-white/20 bg-white/5 px-4 py-3 text-white placeholder-slate-400 focus:border-robo-orange focus:outline-none focus:ring-2 focus:ring-robo-orange/50 disabled:opacity-50"
                placeholder="Korte uitleg over ontwerp, aandrijving of strategie"
              />
            </div>
          </article>
        </div>

        <!-- Opmerkingen -->
        <div>
          <label for="opmerkingen" class="mb-2 block font-semibold">
            Opmerkingen <span class="text-slate-400 font-normal">(optioneel)</span>
          </label>
          <textarea
            id="opmerkingen"
            v-model="formulier.opmerkingen"
            rows="4"
            maxlength="2000"
            :disabled="status === 'versturen'"
            class="w-full rounded-lg border border-white/20 bg-white/5 px-4 py-3 text-white placeholder-slate-400 focus:border-robo-orange focus:outline-none focus:ring-2 focus:ring-robo-orange/50 disabled:opacity-50"
            placeholder="Extra info over beschikbaarheid, hulpvraag of speciale wensen"
          />
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
