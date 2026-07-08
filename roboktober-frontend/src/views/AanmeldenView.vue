<script setup lang="ts">
/**
 * Team registratie formulier.
 *
 * WCAG 2.2 AA: alle velden hebben labels, foutmeldingen zijn gekoppeld via aria-describedby.
 * OWASP: input validation op client én server (StoreTeamRequest.php).
 *
 * @see PLAN.md §5.1 — registratie via account
 * @see PLAN.md §6.x — Aanmelden page design
 */
import { onMounted, onUnmounted, reactive, ref } from 'vue'
import { getEditions, registreerTeam } from '@/api'
import { useAuth } from '@/composables/useAuth'
import type { Edition, RegistratiePayload } from '@/types/api'
import headerImage from '@/assets/headers/header-aanmelden.png'

type FormulierStatus = 'idle' | 'versturen' | 'succes' | 'fout'

const status = ref<FormulierStatus>('idle')
const auth = useAuth()
const foutmelding = ref<string>('')
const teamfotoNaam = ref<string>('')
const teamfotoFout = ref<string>('')
const teamfotoBestand = ref<File | null>(null)
const teamfotoPreviewUrl = ref<string | null>(null)
const editions = ref<Edition[]>([])
const editionsLaden = ref(true)
const stap = ref<1 | 2 | 3>(1)

const TOEGESTANE_TEAMFOTO_MIME_TYPES = new Set(['image/jpeg', 'image/png', 'image/webp'])
const TEAMFOTO_MAX_BYTES = 50 * 1024 * 1024

const formulier = reactive<RegistratiePayload>({
  edition_id: 0,
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

onMounted(async (): Promise<void> => {
  if (!auth.initialized.value) {
    await auth.initAuth()
  }

  if (auth.user.value?.email) {
    formulier.email = auth.user.value.email
  }

  try {
    const data = await getEditions()
    editions.value = data

    const eersteEditie = data.at(0)
    if (eersteEditie) {
      formulier.edition_id = eersteEditie.id
    }
  } catch {
    foutmelding.value = 'Edities konden niet geladen worden. Herlaad de pagina en probeer opnieuw.'
  } finally {
    editionsLaden.value = false
  }
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

  teamfotoFout.value = ''

  if (bestand && !TOEGESTANE_TEAMFOTO_MIME_TYPES.has(bestand.type)) {
    formulier.teamfoto = null
    teamfotoBestand.value = null
    teamfotoNaam.value = ''
    target.value = ''
    teamfotoFout.value = 'Gebruik een JPG, PNG of WEBP bestand.'

    if (teamfotoPreviewUrl.value) {
      URL.revokeObjectURL(teamfotoPreviewUrl.value)
      teamfotoPreviewUrl.value = null
    }

    return
  }

  if (bestand && bestand.size > TEAMFOTO_MAX_BYTES) {
    formulier.teamfoto = null
    teamfotoBestand.value = null
    teamfotoNaam.value = ''
    target.value = ''
    teamfotoFout.value = 'De teamfoto mag maximaal 50 MB groot zijn.'

    if (teamfotoPreviewUrl.value) {
      URL.revokeObjectURL(teamfotoPreviewUrl.value)
      teamfotoPreviewUrl.value = null
    }

    return
  }

  if (teamfotoPreviewUrl.value) {
    URL.revokeObjectURL(teamfotoPreviewUrl.value)
    teamfotoPreviewUrl.value = null
  }

  teamfotoBestand.value = bestand
  formulier.teamfoto = bestand
  teamfotoNaam.value = bestand?.name ?? ''

  if (bestand) {
    teamfotoPreviewUrl.value = URL.createObjectURL(bestand)
  }
}

onUnmounted(() => {
  if (teamfotoPreviewUrl.value) {
    URL.revokeObjectURL(teamfotoPreviewUrl.value)
  }
})

function resetFormulierFout(): void {
  if (status.value === 'fout') {
    status.value = 'idle'
  }

  foutmelding.value = ''
}

function valideerStap1(): boolean {
  if (formulier.edition_id <= 0) {
    foutmelding.value = 'Kies eerst een editie.'
    status.value = 'fout'
    return false
  }

  if (formulier.naam.trim() === '') {
    foutmelding.value = 'Vul een teamnaam in.'
    status.value = 'fout'
    return false
  }

  if (formulier.contactpersoon.trim() === '') {
    foutmelding.value = 'Vul de naam van de contactpersoon in.'
    status.value = 'fout'
    return false
  }

  if (!/^\S+@\S+\.\S+$/.test(formulier.email.trim())) {
    foutmelding.value = 'Vul een geldig e-mailadres in.'
    status.value = 'fout'
    return false
  }

  if (formulier.volwassenen < 1) {
    foutmelding.value = 'Er moet minimaal 1 volwassene worden opgegeven.'
    status.value = 'fout'
    return false
  }

  return true
}

function valideerStap2(): boolean {
  const heeftLegeRobotNaam = formulier.robots.some((robot) => robot.naam.trim() === '')

  if (heeftLegeRobotNaam) {
    foutmelding.value = 'Geef elke robot een naam voordat je doorgaat.'
    status.value = 'fout'
    return false
  }

  return true
}

function volgendeStap(): void {
  resetFormulierFout()

  if (stap.value === 1 && !valideerStap1()) {
    return
  }

  if (stap.value === 2 && !valideerStap2()) {
    return
  }

  if (stap.value < 3) {
    stap.value = (stap.value + 1) as 1 | 2 | 3
  }
}

function vorigeStap(): void {
  resetFormulierFout()

  if (stap.value > 1) {
    stap.value = (stap.value - 1) as 1 | 2 | 3
  }
}

async function verstuur(): Promise<void> {
  if (teamfotoFout.value) {
    status.value = 'fout'
    foutmelding.value = teamfotoFout.value

    return
  }

  status.value = 'versturen'
  foutmelding.value = ''

  try {
    await registreerTeam({
      ...formulier,
      teamfoto: teamfotoBestand.value,
    })
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
    } else if (
      error !== null &&
      typeof error === 'object' &&
      'response' in error &&
      error.response !== null &&
      typeof error.response === 'object' &&
      'data' in error.response &&
      error.response.data !== null &&
      typeof error.response.data === 'object' &&
      'errors' in error.response.data &&
      error.response.data.errors !== null &&
      typeof error.response.data.errors === 'object'
    ) {
      const eersteFout = Object.values(error.response.data.errors)
        .flat()
        .find((waarde) => typeof waarde === 'string')

      foutmelding.value = typeof eersteFout === 'string'
        ? eersteFout
        : 'Controleer je invoer en probeer opnieuw.'
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
          Je bent ingelogd. Rond hieronder je teamaanmelding af.
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
          <p class="mt-3 text-slate-200">
            Je kunt je aanmelding later altijd aanpassen vanuit je accountomgeving.
          </p>
          <RouterLink
            to="/aanmelding/wijzigen"
            class="mt-4 inline-block font-semibold text-robo-orange hover:underline"
          >
            Aanmelding beheren
          </RouterLink>
        </div>

        <!-- Formulier -->
        <form
          v-else
          novalidate
          class="space-y-6"
          @submit.prevent="verstuur"
        >
        <div class="rounded-lg border border-blue-400/30 bg-blue-500/10 p-4 text-sm text-slate-200">
          Later wijzigen? Geen probleem. Je beheert je aanmelding daarna via je account.
        </div>

        <ol class="grid gap-2 sm:grid-cols-3" aria-label="Stappen aanmelding">
          <li class="rounded-lg border px-3 py-2 text-sm font-semibold" :class="stap === 1 ? 'border-robo-orange bg-robo-orange/15 text-white' : 'border-white/15 text-slate-300'">1. Teamgegevens</li>
          <li class="rounded-lg border px-3 py-2 text-sm font-semibold" :class="stap === 2 ? 'border-robo-orange bg-robo-orange/15 text-white' : 'border-white/15 text-slate-300'">2. Robots</li>
          <li class="rounded-lg border px-3 py-2 text-sm font-semibold" :class="stap === 3 ? 'border-robo-orange bg-robo-orange/15 text-white' : 'border-white/15 text-slate-300'">3. Foto & verzenden</li>
        </ol>

        <div v-if="stap === 1" class="space-y-6">
          <div>
            <label
              for="edition_id"
              class="mb-2 block font-semibold"
            >
              Editie <span aria-hidden="true" class="text-robo-orange">*</span>
            </label>
            <select
              id="edition_id"
              v-model.number="formulier.edition_id"
              required
              :disabled="status === 'versturen' || editionsLaden || editions.length === 0"
              class="w-full rounded-lg border border-white/20 bg-robo-dark px-4 py-3 text-white focus:border-robo-orange focus:outline-none focus:ring-2 focus:ring-robo-orange/50 disabled:opacity-50"
            >
              <option disabled value="0">Kies een editie</option>
              <option
                v-for="edition in editions"
                :key="edition.id"
                :value="edition.id"
              >
                {{ edition.naam }} · {{ edition.locatie }}
              </option>
            </select>
            <p v-if="editionsLaden" class="mt-2 text-sm text-slate-400">Edities laden...</p>
            <p v-else-if="editions.length === 0" class="mt-2 text-sm text-amber-300">
              Er zijn momenteel geen open edities beschikbaar voor aanmelding.
            </p>
          </div>

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
              readonly
              :disabled="status === 'versturen'"
              class="w-full rounded-lg border border-white/20 bg-white/5 px-4 py-3 text-white placeholder-slate-400 focus:border-robo-orange focus:outline-none focus:ring-2 focus:ring-robo-orange/50 disabled:opacity-50"
              placeholder="jouw@email.nl"
            />
            <p class="mt-2 text-xs text-slate-400">
              Dit e-mailadres komt uit je account en wordt gebruikt als teamcaptain/registrator contact.
            </p>
          </div>

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
        </div>

        <div v-else-if="stap === 2" class="space-y-4 rounded-xl border border-white/15 bg-white/5 p-4">
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

        <div v-else class="space-y-6">
          <div>
            <label for="teamfoto" class="mb-2 block font-semibold">
              Teamfoto <span class="text-slate-400 font-normal">(optioneel)</span>
            </label>
            <p class="mb-2 text-sm text-slate-300">Toegestane formaten: JPG, PNG, WEBP · Maximaal 50 MB.</p>

            <img
              v-if="teamfotoPreviewUrl"
              :src="teamfotoPreviewUrl"
              alt="Voorbeeld van geselecteerde teamfoto"
              class="mb-3 h-40 w-40 rounded-lg border border-white/20 object-cover"
            />

            <input
              id="teamfoto"
              type="file"
              accept="image/jpeg,image/png,image/webp"
              :disabled="status === 'versturen'"
              class="w-full rounded-lg border border-white/20 bg-white/5 px-4 py-3 text-sm text-slate-200 file:mr-4 file:rounded file:border-0 file:bg-robo-orange file:px-3 file:py-2 file:font-semibold file:text-white hover:file:bg-robo-orange-dark focus:border-robo-orange focus:outline-none focus:ring-2 focus:ring-robo-orange/50 disabled:opacity-50"
              @change="wijzigTeamfoto"
            />
            <p v-if="teamfotoNaam" class="mt-2 text-sm text-slate-300">
              Gekozen bestand: {{ teamfotoNaam }}
            </p>
            <p v-if="teamfotoFout" class="mt-2 text-sm text-red-300">
              {{ teamfotoFout }}
            </p>
          </div>

          <div class="rounded-xl border border-white/15 bg-white/5 p-4 text-sm text-slate-200">
            <p class="font-semibold text-white">Controleer je samenvatting</p>
            <ul class="mt-2 space-y-1">
              <li><strong>Team:</strong> {{ formulier.naam || 'Nog niet ingevuld' }}</li>
              <li><strong>Contact:</strong> {{ formulier.contactpersoon || 'Nog niet ingevuld' }}</li>
              <li><strong>E-mail:</strong> {{ formulier.email || 'Nog niet ingevuld' }}</li>
              <li><strong>Aantal robots:</strong> {{ formulier.robots.length }}</li>
            </ul>
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

        <div class="flex flex-col gap-3 sm:flex-row sm:justify-between">
          <button
            v-if="stap > 1"
            type="button"
            :disabled="status === 'versturen'"
            class="rounded-lg border border-white/20 px-6 py-3 font-semibold text-slate-200 transition hover:bg-white/10 disabled:opacity-60"
            @click="vorigeStap"
          >
            Vorige stap
          </button>

          <div class="sm:ml-auto">
            <button
              v-if="stap < 3"
              type="button"
              :disabled="status === 'versturen' || editionsLaden || editions.length === 0"
              class="w-full rounded-lg bg-robo-orange px-8 py-3 font-bold text-white transition hover:bg-robo-orange-dark focus:outline-none focus:ring-4 focus:ring-robo-orange/50 disabled:opacity-60"
              @click="volgendeStap"
            >
              Volgende stap
            </button>

            <button
              v-else
              type="submit"
              :disabled="status === 'versturen' || editionsLaden || editions.length === 0"
              class="w-full rounded-lg bg-robo-orange px-8 py-3 font-bold text-white transition hover:bg-robo-orange-dark focus:outline-none focus:ring-4 focus:ring-robo-orange/50 disabled:opacity-60"
            >
              <span v-if="status === 'versturen'">Aanmelden...</span>
              <span v-else>Aanmelden</span>
            </button>
          </div>
        </div>

        <p class="text-center text-sm text-slate-400">
          <span aria-hidden="true">*</span> Verplichte velden
        </p>
        </form>
      </div>
    </section>
  </main>
</template>
