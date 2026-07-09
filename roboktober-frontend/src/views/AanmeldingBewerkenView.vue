<script setup lang="ts">
import {
  createMijnRegistratieUpdate,
  getCaptainTeamMembershipRequests,
  getEditions,
  getMijnRegistratie,
  getMijnRegistratieUpdates,
  reviewCaptainTeamMembershipRequest,
  updateMijnRegistratieUpdate,
  uploadRichMedia,
  updateMijnRegistratie,
} from '@/api'
import ContentResourcePanel from '@/components/editor/ContentResourcePanel.vue'
import EditorFormattingToolbar from '@/components/editor/EditorFormattingToolbar.vue'
import { useContentInsertion } from '@/composables/useContentInsertion'
import { useAuth } from '@/composables/useAuth'
import type { Edition, Gewichtsklasse, TeamMembership, TeamUpdate, UpdateRegistratiePayload } from '@/types/api'
import { computed, onMounted, onUnmounted, reactive, ref } from 'vue'
import { useRoute } from 'vue-router'

type Status = 'laden' | 'klaar' | 'opslaan' | 'succes' | 'fout'
type BewerkSectie = 'basis' | 'robots' | 'foto' | 'leden' | 'updates'

const route = useRoute()
const auth = useAuth()
const magWijzigen = computed(() => auth.isAuthenticated.value)

const status = ref<Status>('laden')
const actieveSectie = ref<BewerkSectie>('basis')
const foutmelding = ref('')
const successMelding = ref('')
const editions = ref<Edition[]>([])
const teamfotoNaam = ref('')
const teamfotoFout = ref('')
const teamfotoBestand = ref<File | null>(null)
const teamfotoPreviewUrl = ref<string | null>(null)
const huidigeFotoUrl = ref<string | null>(null)
const robotFotoUrls = ref<Record<number, string>>({})
const robotFotoStatus = ref<Record<number, 'idle' | 'uploaden'>>({})
const robotFotoFouten = ref<Record<number, string>>({})
const robotFotoSuccess = ref<Record<number, string>>({})
const teamUpdates = ref<TeamUpdate[]>([])
const membershipRequests = ref<TeamMembership[]>([])
const membershipStatus = ref<'idle' | 'opslaan'>('idle')
const membershipFout = ref('')
const updateStatus = ref<'idle' | 'opslaan'>('idle')
const updateFout = ref('')
const updateSucces = ref('')
const updateAfbeeldingNamen = ref<string[]>([])
const updateAfbeeldingen = ref<File[]>([])
const bewerkUpdateId = ref<number | null>(null)
const verwijderUpdateAfbeeldingIds = ref<number[]>([])
const updateContentTextarea = ref<HTMLTextAreaElement | null>(null)
const updateContentRef = ref('')

const {
  insertSnippet: insertUpdateSnippet,
  wrapSelection,
  formatHeading,
  formatList,
  formatLink,
  formatQuote,
  formatCode,
  insertDivider,
} = useContentInsertion(updateContentRef, updateContentTextarea)

const updateForm = reactive({
  titel: '',
  excerpt: '',
  content: '',
  content_format: 'html' as 'html' | 'markdown',
})

const TOEGESTANE_TEAMFOTO_MIME_TYPES = new Set(['image/jpeg', 'image/png', 'image/webp'])
const TEAMFOTO_MAX_BYTES = 50 * 1024 * 1024
const UPDATE_MAX_IMAGES = 12

const heeftOpenAanvragen = computed(() => membershipRequests.value.length > 0)

function gaNaarSectie(sectie: BewerkSectie): void {
  actieveSectie.value = sectie
}

const formulier = reactive<UpdateRegistratiePayload>({
  edition_id: 0,
  naam: '',
  contactpersoon: '',
  email: '',
  volwassenen: 1,
  kinderen: undefined,
  opmerkingen: '',
  teamfoto: null,
  teamfoto_verwijderen: false,
  robots: [
    {
      naam: '',
      gewichtsklasse: 'antweight',
      beschrijving: '',
    },
  ],
})

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

  if (bestand) {
    formulier.teamfoto_verwijderen = false
  }
}

onUnmounted(() => {
  if (teamfotoPreviewUrl.value) {
    URL.revokeObjectURL(teamfotoPreviewUrl.value)
  }
})

function wijzigUpdateAfbeeldingen(event: Event): void {
  const target = event.target as HTMLInputElement
  const bestanden = Array.from(target.files ?? [])

  updateFout.value = ''

  if (bestanden.length > UPDATE_MAX_IMAGES) {
    updateAfbeeldingen.value = []
    updateAfbeeldingNamen.value = []
    target.value = ''
    updateFout.value = `Je kunt maximaal ${UPDATE_MAX_IMAGES} afbeeldingen toevoegen.`
    return
  }

  const ongeldigType = bestanden.find((bestand) => !TOEGESTANE_TEAMFOTO_MIME_TYPES.has(bestand.type))
  if (ongeldigType) {
    updateAfbeeldingen.value = []
    updateAfbeeldingNamen.value = []
    target.value = ''
    updateFout.value = 'Gebruik alleen JPG, PNG of WEBP afbeeldingen.'
    return
  }

  const teGroot = bestanden.find((bestand) => bestand.size > TEAMFOTO_MAX_BYTES)
  if (teGroot) {
    updateAfbeeldingen.value = []
    updateAfbeeldingNamen.value = []
    target.value = ''
    updateFout.value = 'Elke afbeelding mag maximaal 50 MB groot zijn.'
    return
  }

  updateAfbeeldingen.value = bestanden
  updateAfbeeldingNamen.value = bestanden.map((bestand) => bestand.name)
}

function startBewerkUpdate(update: TeamUpdate): void {
  bewerkUpdateId.value = update.id
  updateForm.titel = update.titel
  updateForm.excerpt = update.excerpt ?? ''
  updateForm.content = update.content
  updateForm.content_format = update.content_format
  updateAfbeeldingen.value = []
  updateAfbeeldingNamen.value = []
  verwijderUpdateAfbeeldingIds.value = []
  updateFout.value = ''
  updateSucces.value = ''
}

function annuleerBewerkUpdate(): void {
  bewerkUpdateId.value = null
  updateForm.titel = ''
  updateForm.excerpt = ''
  updateForm.content = ''
  updateForm.content_format = 'html'
  updateAfbeeldingen.value = []
  updateAfbeeldingNamen.value = []
  verwijderUpdateAfbeeldingIds.value = []
  updateFout.value = ''
  updateSucces.value = ''
}

function toggleVerwijderUpdateAfbeelding(mediaId: number): void {
  if (verwijderUpdateAfbeeldingIds.value.includes(mediaId)) {
    verwijderUpdateAfbeeldingIds.value = verwijderUpdateAfbeeldingIds.value.filter((id) => id !== mediaId)
    return
  }

  verwijderUpdateAfbeeldingIds.value = [...verwijderUpdateAfbeeldingIds.value, mediaId]
}

async function uploadRobotFoto(robotId: number, event: Event): Promise<void> {
  const target = event.target as HTMLInputElement
  const bestand = target.files?.[0] ?? null

  robotFotoFouten.value[robotId] = ''
  robotFotoSuccess.value[robotId] = ''

  if (!bestand) {
    return
  }

  if (!TOEGESTANE_TEAMFOTO_MIME_TYPES.has(bestand.type)) {
    robotFotoFouten.value[robotId] = 'Gebruik een JPG, PNG of WEBP bestand.'
    target.value = ''
    return
  }

  if (bestand.size > TEAMFOTO_MAX_BYTES) {
    robotFotoFouten.value[robotId] = 'De robotfoto mag maximaal 50 MB groot zijn.'
    target.value = ''
    return
  }

  robotFotoStatus.value[robotId] = 'uploaden'

  try {
    const result = await uploadRichMedia({
      bestand,
      target_type: 'robot',
      target_id: robotId,
      collectie: 'foto',
      alt_tekst: 'Robotfoto',
      onderschrift: 'Geupload via teambeheer',
    })

    robotFotoUrls.value[robotId] = result.data.url
    robotFotoSuccess.value[robotId] = 'Robotfoto bijgewerkt.'
    target.value = ''
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
      robotFotoFouten.value[robotId] = error.response.data.message
    } else {
      robotFotoFouten.value[robotId] = 'Uploaden van robotfoto is mislukt.'
    }
  } finally {
    robotFotoStatus.value[robotId] = 'idle'
  }
}

function formatDatum(iso: string | null): string {
  if (!iso) return ''

  return new Date(iso).toLocaleDateString('nl-NL', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  })
}

function insertResourceSnippet(snippet: string): void {
  updateContentRef.value = updateForm.content
  insertUpdateSnippet(snippet)
  updateForm.content = updateContentRef.value
}

function runUpdateFormatAction(action: 'bold' | 'italic' | 'h2' | 'h3' | 'ul' | 'ol' | 'link' | 'quote' | 'code' | 'divider'): void {
  updateContentRef.value = updateForm.content

  if (action === 'bold') {
    if (updateForm.content_format === 'html') {
      wrapSelection('<strong>', '</strong>', 'vetgedrukte tekst')
    } else {
      wrapSelection('**', '**', 'vetgedrukte tekst')
    }
  }

  if (action === 'italic') {
    if (updateForm.content_format === 'html') {
      wrapSelection('<em>', '</em>', 'cursieve tekst')
    } else {
      wrapSelection('*', '*', 'cursieve tekst')
    }
  }

  if (action === 'h2') {
    formatHeading(2, updateForm.content_format)
  }

  if (action === 'h3') {
    formatHeading(3, updateForm.content_format)
  }

  if (action === 'ul') {
    formatList('ul', updateForm.content_format)
  }

  if (action === 'ol') {
    formatList('ol', updateForm.content_format)
  }

  if (action === 'link') {
    const url = window.prompt('Voer de URL in', 'https://')
    if (url && url.trim().length > 0) {
      formatLink(url.trim(), updateForm.content_format)
    }
  }

  if (action === 'quote') {
    formatQuote(updateForm.content_format)
  }

  if (action === 'code') {
    formatCode(updateForm.content_format)
  }

  if (action === 'divider') {
    insertDivider(updateForm.content_format)
  }

  updateForm.content = updateContentRef.value
}

onMounted(async (): Promise<void> => {
  status.value = 'laden'

  if (!auth.initialized.value) {
    await auth.initAuth()
  }

  try {
    const [editieData, registratie, updates] = await Promise.all([
      getEditions(),
      getMijnRegistratie(),
      getMijnRegistratieUpdates(),
    ])

    membershipRequests.value = await getCaptainTeamMembershipRequests()

    editions.value = editieData

    formulier.edition_id = registratie.edition_id ?? editieData[0]?.id ?? 0
    formulier.naam = registratie.naam
    formulier.contactpersoon = registratie.contactpersoon
    formulier.email = registratie.email
    formulier.volwassenen = registratie.volwassenen
    formulier.kinderen = registratie.kinderen ?? undefined
    formulier.opmerkingen = registratie.opmerkingen ?? ''
    formulier.robots = registratie.robots.map((robot) => ({
      id: robot.id,
      naam: robot.naam,
      gewichtsklasse: robot.gewichtsklasse as Gewichtsklasse,
      beschrijving: robot.beschrijving ?? '',
    }))

    robotFotoUrls.value = {}
    for (const robot of registratie.robots) {
      if (robot.foto?.url) {
        robotFotoUrls.value[robot.id] = robot.foto.url
      }
    }

    if (!formulier.robots.length) {
      formulier.robots = [{ naam: '', gewichtsklasse: 'antweight', beschrijving: '' }]
    }

    huidigeFotoUrl.value = registratie.foto?.url ?? null
    teamUpdates.value = updates

    status.value = 'klaar'
  } catch {
    status.value = 'fout'
    foutmelding.value = 'Je hebt nog geen team gekoppeld aan dit account of laden is mislukt.'
  }
})

async function beoordeelAanvraag(id: number, status: 'approved' | 'rejected'): Promise<void> {
  membershipStatus.value = 'opslaan'
  membershipFout.value = ''

  try {
    const beoordeeld = await reviewCaptainTeamMembershipRequest(id, status)
    membershipRequests.value = membershipRequests.value.filter((item) => item.id !== beoordeeld.id)
  } catch {
    membershipFout.value = 'Bijwerken van lidmaatschapsaanvraag mislukt.'
  } finally {
    membershipStatus.value = 'idle'
  }
}

async function opslaan(): Promise<void> {
  if (!magWijzigen.value) {
    status.value = 'fout'
    foutmelding.value = 'Inloggen is vereist om wijzigingen op te slaan.'
    return
  }

  if (teamfotoFout.value) {
    status.value = 'fout'
    foutmelding.value = teamfotoFout.value
    return
  }

  status.value = 'opslaan'
  foutmelding.value = ''
  successMelding.value = ''

  try {
    const resultaat = await updateMijnRegistratie({
      ...formulier,
      teamfoto: teamfotoBestand.value,
    })
    huidigeFotoUrl.value = resultaat.foto?.url ?? null

    if (teamfotoPreviewUrl.value) {
      URL.revokeObjectURL(teamfotoPreviewUrl.value)
      teamfotoPreviewUrl.value = null
    }

    formulier.teamfoto = null
    teamfotoBestand.value = null
    teamfotoNaam.value = ''
    formulier.teamfoto_verwijderen = false
    status.value = 'succes'
    successMelding.value = 'Je aanmelding is bijgewerkt.'
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
      return
    }

    if (
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
      return
    }

    foutmelding.value = 'Opslaan is mislukt. Probeer het opnieuw.'
  }
}

async function plaatsUpdate(): Promise<void> {
  if (!magWijzigen.value) {
    updateFout.value = 'Inloggen is vereist om een voortgangsbericht te plaatsen.'
    return
  }

  updateFout.value = ''
  updateSucces.value = ''

  if (updateForm.titel.trim() === '' || updateForm.content.trim() === '') {
    updateFout.value = 'Vul minimaal een titel en inhoud in voor je voortgangsbericht.'
    return
  }

  updateStatus.value = 'opslaan'

  try {
    if (bewerkUpdateId.value !== null) {
      const bijgewerkt = await updateMijnRegistratieUpdate(bewerkUpdateId.value, {
        titel: updateForm.titel,
        excerpt: updateForm.excerpt || undefined,
        content: updateForm.content,
        content_format: updateForm.content_format,
        afbeeldingen: updateAfbeeldingen.value,
        verwijder_afbeelding_ids: verwijderUpdateAfbeeldingIds.value,
      })

      teamUpdates.value = teamUpdates.value.map((item) => (item.id === bijgewerkt.id ? bijgewerkt : item))
      annuleerBewerkUpdate()
      updateSucces.value = 'Voortgangsbericht bijgewerkt.'
    } else {
      const nieuw = await createMijnRegistratieUpdate({
        titel: updateForm.titel,
        excerpt: updateForm.excerpt || undefined,
        content: updateForm.content,
        content_format: updateForm.content_format,
        afbeeldingen: updateAfbeeldingen.value,
      })

      teamUpdates.value = [nieuw, ...teamUpdates.value]
      updateForm.titel = ''
      updateForm.excerpt = ''
      updateForm.content = ''
      updateForm.content_format = 'html'
      updateAfbeeldingen.value = []
      updateAfbeeldingNamen.value = []
      verwijderUpdateAfbeeldingIds.value = []
      updateSucces.value = 'Voortgangsbericht geplaatst.'
    }
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
      'errors' in error.response.data &&
      error.response.data.errors !== null &&
      typeof error.response.data.errors === 'object'
    ) {
      const eersteFout = Object.values(error.response.data.errors)
        .flat()
        .find((waarde) => typeof waarde === 'string')

      updateFout.value = typeof eersteFout === 'string'
        ? eersteFout
        : 'Plaatsen van voortgangsbericht is mislukt.'
    } else {
      updateFout.value = 'Plaatsen van voortgangsbericht is mislukt.'
    }
  } finally {
    updateStatus.value = 'idle'
  }
}
</script>

<template>
  <main class="min-h-screen bg-robo-dark py-12 text-white">
    <section class="mx-auto max-w-3xl px-6">
      <h1 class="mb-3 text-3xl font-black">Teamaanmelding bewerken</h1>
      <p class="mb-6 text-slate-300">Bewerk alleen wat nu relevant is. Alles is opgedeeld in duidelijke secties.</p>

      <nav class="mb-8 grid gap-2 sm:grid-cols-5" aria-label="Secties teamaanmelding">
        <button
          type="button"
          class="rounded-lg border px-3 py-2 text-sm font-semibold text-left"
          :class="actieveSectie === 'basis' ? 'border-robo-orange bg-robo-orange/15 text-white' : 'border-white/15 text-slate-300 hover:border-white/40'"
          @click="gaNaarSectie('basis')"
        >
          Teamgegevens
        </button>
        <button
          type="button"
          class="rounded-lg border px-3 py-2 text-sm font-semibold text-left"
          :class="actieveSectie === 'robots' ? 'border-robo-orange bg-robo-orange/15 text-white' : 'border-white/15 text-slate-300 hover:border-white/40'"
          @click="gaNaarSectie('robots')"
        >
          Robots
        </button>
        <button
          type="button"
          class="rounded-lg border px-3 py-2 text-sm font-semibold text-left"
          :class="actieveSectie === 'foto' ? 'border-robo-orange bg-robo-orange/15 text-white' : 'border-white/15 text-slate-300 hover:border-white/40'"
          @click="gaNaarSectie('foto')"
        >
          Teamfoto
        </button>
        <button
          type="button"
          class="rounded-lg border px-3 py-2 text-sm font-semibold text-left"
          :class="actieveSectie === 'leden' ? 'border-robo-orange bg-robo-orange/15 text-white' : 'border-white/15 text-slate-300 hover:border-white/40'"
          @click="gaNaarSectie('leden')"
        >
          Teamleden
          <span v-if="heeftOpenAanvragen" class="ml-1 rounded bg-robo-orange px-1.5 py-0.5 text-xs text-white">{{ membershipRequests.length }}</span>
        </button>
        <button
          type="button"
          class="rounded-lg border px-3 py-2 text-sm font-semibold text-left"
          :class="actieveSectie === 'updates' ? 'border-robo-orange bg-robo-orange/15 text-white' : 'border-white/15 text-slate-300 hover:border-white/40'"
          @click="gaNaarSectie('updates')"
        >
          Voortgang
        </button>
      </nav>

      <div v-if="!magWijzigen" class="mb-6 rounded-xl border border-amber-400/30 bg-amber-500/10 p-4 text-amber-100">
        Je bekijkt deze pagina in read-only modus. Log in om wijzigingen door te voeren.
        <RouterLink
          :to="{ name: 'login', query: { redirect: route.fullPath } }"
          class="ml-2 font-semibold text-robo-orange underline"
        >Inloggen</RouterLink>
      </div>

      <div v-if="status === 'laden'" class="rounded-xl border border-white/15 bg-white/5 p-6 text-slate-300">
        Aanmelding laden...
      </div>

      <div v-else-if="status === 'fout' && !successMelding" class="rounded-xl border border-red-500/30 bg-red-500/10 p-4 text-red-300">
        {{ foutmelding }}
      </div>

      <form v-else-if="actieveSectie !== 'updates'" class="space-y-6" @submit.prevent="opslaan">
        <fieldset :disabled="status === 'opslaan' || !magWijzigen" class="space-y-6">
          <section v-if="actieveSectie === 'basis'" class="space-y-6 rounded-xl border border-white/15 bg-white/5 p-5">
            <h2 class="text-lg font-bold">Teamgegevens</h2>

            <div>
              <label for="edition_id" class="mb-2 block font-semibold">Editie</label>
              <select
                id="edition_id"
                v-model.number="formulier.edition_id"
                required
                class="w-full rounded-lg border border-white/20 bg-robo-dark px-4 py-3 text-white"
              >
                <option v-for="edition in editions" :key="edition.id" :value="edition.id">
                  {{ edition.naam }} · {{ edition.locatie }}
                </option>
              </select>
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
              <div>
                <label for="naam" class="mb-2 block font-semibold">Teamnaam</label>
                <input id="naam" v-model="formulier.naam" required maxlength="255" class="w-full rounded-lg border border-white/20 bg-white/5 px-4 py-3" />
              </div>
              <div>
                <label for="contactpersoon" class="mb-2 block font-semibold">Contactpersoon</label>
                <input id="contactpersoon" v-model="formulier.contactpersoon" required maxlength="255" class="w-full rounded-lg border border-white/20 bg-white/5 px-4 py-3" />
              </div>
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
              <div>
                <label for="email" class="mb-2 block font-semibold">E-mailadres</label>
                <input id="email" v-model="formulier.email" type="email" required maxlength="255" class="w-full rounded-lg border border-white/20 bg-white/5 px-4 py-3" />
              </div>
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label for="volwassenen" class="mb-2 block font-semibold">Volwassenen</label>
                  <input id="volwassenen" v-model.number="formulier.volwassenen" type="number" min="1" max="20" required class="w-full rounded-lg border border-white/20 bg-white/5 px-4 py-3" />
                </div>
                <div>
                  <label for="kinderen" class="mb-2 block font-semibold">Kinderen</label>
                  <input id="kinderen" v-model.number="formulier.kinderen" type="number" min="0" max="50" class="w-full rounded-lg border border-white/20 bg-white/5 px-4 py-3" />
                </div>
              </div>
            </div>

            <div>
              <label for="opmerkingen" class="mb-2 block font-semibold">Opmerkingen</label>
              <textarea id="opmerkingen" v-model="formulier.opmerkingen" rows="4" maxlength="2000" class="w-full rounded-lg border border-white/20 bg-white/5 px-4 py-3" />
            </div>
          </section>

          <section v-if="actieveSectie === 'robots'" class="space-y-4 rounded-xl border border-white/15 bg-white/5 p-5">
            <div class="flex items-center justify-between">
              <h2 class="text-lg font-bold">Robots</h2>
              <button type="button" class="rounded border border-robo-orange px-3 py-2 text-sm font-semibold text-robo-orange" @click="voegRobotToe">
                + Robot toevoegen
              </button>
            </div>

            <article v-for="(robot, index) in formulier.robots" :key="robot.id ?? index" class="space-y-3 rounded-lg border border-white/10 bg-black/20 p-4">
              <div class="flex items-center justify-between">
                <h3 class="font-semibold">Robot {{ index + 1 }}</h3>
                <button type="button" class="rounded border border-red-400 px-2 py-1 text-xs font-semibold text-red-300" @click="verwijderRobot(index)">
                  Verwijder
                </button>
              </div>

              <input v-model="robot.naam" required maxlength="255" placeholder="Robotnaam" class="w-full rounded-lg border border-white/20 bg-white/5 px-4 py-3" />

              <select v-model="robot.gewichtsklasse" required class="w-full rounded-lg border border-white/20 bg-robo-dark px-4 py-3">
                <option value="antweight">Antweight (max. 150 g)</option>
                <option value="beetleweight">Beetleweight (max. 1,36 kg)</option>
                <option value="featherweight">Featherweight (max. 13,6 kg)</option>
              </select>

              <textarea v-model="robot.beschrijving" rows="3" maxlength="1000" placeholder="Beschrijving" class="w-full rounded-lg border border-white/20 bg-white/5 px-4 py-3" />

              <div class="rounded-lg border border-white/10 bg-white/5 p-3">
                <p class="mb-2 text-sm font-semibold text-slate-200">Robotfoto</p>

                <img
                  v-if="robot.id && robotFotoUrls[robot.id]"
                  :src="robotFotoUrls[robot.id]"
                  alt="Robotfoto"
                  class="mb-3 h-32 w-32 rounded-lg border border-white/20 object-cover"
                />

                <p v-else class="mb-3 text-xs text-slate-400">
                  Nog geen robotfoto geupload.
                </p>

                <div v-if="robot.id" class="space-y-2">
                  <input
                    type="file"
                    accept="image/jpeg,image/png,image/webp"
                    class="w-full rounded-lg border border-white/20 bg-white/5 px-3 py-2 text-xs"
                    :disabled="robotFotoStatus[robot.id] === 'uploaden'"
                    @change="uploadRobotFoto(robot.id, $event)"
                  />

                  <p v-if="robotFotoFouten[robot.id]" class="text-xs text-red-300">
                    {{ robotFotoFouten[robot.id] }}
                  </p>
                  <p v-if="robotFotoSuccess[robot.id]" class="text-xs text-green-300">
                    {{ robotFotoSuccess[robot.id] }}
                  </p>
                </div>

                <p v-else class="text-xs text-slate-400">
                  Sla eerst de aanmelding op om een robotfoto te kunnen uploaden.
                </p>
              </div>
            </article>
          </section>

          <section v-if="actieveSectie === 'foto'" class="space-y-3 rounded-xl border border-white/15 bg-white/5 p-5">
            <h2 class="text-lg font-bold">Teamfoto</h2>

            <div class="grid gap-3 sm:grid-cols-2">
              <div>
                <p class="mb-2 text-sm font-semibold text-slate-200">Huidige foto</p>
                <img
                  v-if="huidigeFotoUrl && !formulier.teamfoto_verwijderen"
                  :src="huidigeFotoUrl"
                  alt="Huidige teamfoto"
                  class="h-40 w-40 rounded-lg object-cover"
                />
                <div
                  v-else
                  class="flex h-40 w-40 items-center justify-center rounded-lg border border-dashed border-white/25 bg-white/5 text-xs text-slate-300"
                >
                  Geen huidige foto
                </div>
              </div>

              <div>
                <p class="mb-2 text-sm font-semibold text-slate-200">Nieuwe foto</p>
                <img
                  v-if="teamfotoPreviewUrl"
                  :src="teamfotoPreviewUrl"
                  alt="Nieuwe teamfoto preview"
                  class="h-40 w-40 rounded-lg border border-white/20 object-cover"
                />
                <div
                  v-else
                  class="flex h-40 w-40 items-center justify-center rounded-lg border border-dashed border-white/25 bg-white/5 text-xs text-slate-300"
                >
                  Nog geen nieuwe foto gekozen
                </div>
              </div>
            </div>

            <label class="inline-flex items-center gap-2 text-sm">
              <input v-model="formulier.teamfoto_verwijderen" type="checkbox" class="h-4 w-4" />
              Huidige teamfoto verwijderen
            </label>

            <input
              id="teamfoto"
              type="file"
              accept="image/jpeg,image/png,image/webp"
              class="w-full rounded-lg border border-white/20 bg-white/5 px-4 py-3 text-sm"
              @change="wijzigTeamfoto"
            />

            <p v-if="teamfotoNaam" class="text-sm text-slate-300">Nieuw bestand: {{ teamfotoNaam }}</p>
            <p v-if="teamfotoFout" class="text-sm text-red-300">{{ teamfotoFout }}</p>
          </section>

          <section v-if="actieveSectie === 'leden'" class="rounded-xl border border-white/15 bg-white/5 p-5">
            <h2 class="mb-3 text-lg font-bold">Lidmaatschapsaanvragen</h2>
            <p v-if="membershipFout" class="mb-3 rounded border border-red-500/30 bg-red-500/10 px-3 py-2 text-sm text-red-300">
              {{ membershipFout }}
            </p>

            <p v-if="membershipRequests.length === 0" class="text-sm text-slate-300">
              Er zijn geen openstaande aanvragen.
            </p>

            <ul v-else class="space-y-3">
              <li v-for="request in membershipRequests" :key="request.id" class="rounded-lg border border-white/10 bg-black/20 p-3">
                <p class="font-semibold">{{ request.user?.name }} <span class="text-xs text-slate-400">({{ request.user?.email }})</span></p>
                <p v-if="request.request_message" class="mt-1 text-sm text-slate-300">{{ request.request_message }}</p>
                <div class="mt-3 flex gap-2">
                  <button
                    type="button"
                    class="rounded border border-green-400/60 px-3 py-1 text-sm font-semibold text-green-300 disabled:opacity-60"
                    :disabled="membershipStatus === 'opslaan'"
                    @click="beoordeelAanvraag(request.id, 'approved')"
                  >
                    Goedkeuren
                  </button>
                  <button
                    type="button"
                    class="rounded border border-red-400/60 px-3 py-1 text-sm font-semibold text-red-300 disabled:opacity-60"
                    :disabled="membershipStatus === 'opslaan'"
                    @click="beoordeelAanvraag(request.id, 'rejected')"
                  >
                    Afwijzen
                  </button>
                </div>
              </li>
            </ul>
          </section>

          <div v-if="status === 'fout'" class="rounded-lg border border-red-500/30 bg-red-500/10 p-4 text-red-300">
            {{ foutmelding }}
          </div>

          <div v-if="successMelding" class="rounded-lg border border-green-500/30 bg-green-500/10 p-4 text-green-300">
            {{ successMelding }}
          </div>

          <div class="sticky bottom-3 z-10 rounded-xl border border-white/20 bg-robo-dark/95 p-3 backdrop-blur">
            <button
              type="submit"
              :disabled="status === 'opslaan' || !magWijzigen"
              class="w-full rounded-lg bg-robo-orange px-8 py-4 text-lg font-bold text-white transition hover:bg-robo-orange-dark disabled:opacity-60"
            >
              <span v-if="status === 'opslaan'">Opslaan...</span>
              <span v-else-if="!magWijzigen">Log in om op te slaan</span>
              <span v-else>Aanmelding opslaan</span>
            </button>
          </div>
        </fieldset>
      </form>

      <section v-else class="mt-2 space-y-6 rounded-xl border border-white/15 bg-white/5 p-6">
        <div>
          <h2 class="text-2xl font-black">
            {{ bewerkUpdateId ? 'Voortgangsbericht bewerken' : 'Voortgang posten op je teampagina' }}
          </h2>
          <p class="mt-2 text-sm text-slate-300">
            Plaats updates met opmaak en resources (afbeeldingen, video, STL). Deze worden zichtbaar op jullie publieke teampagina.
          </p>
          <div v-if="bewerkUpdateId" class="mt-3">
            <button
              type="button"
              class="rounded border border-white/30 px-3 py-1 text-sm text-slate-200 hover:bg-white/10"
              @click="annuleerBewerkUpdate"
            >
              Bewerken annuleren
            </button>
          </div>
        </div>

        <form class="space-y-4" @submit.prevent="plaatsUpdate">
          <fieldset :disabled="updateStatus === 'opslaan' || !magWijzigen" class="space-y-4">
          <div>
            <label class="mb-2 block font-semibold" for="update-titel">Titel</label>
            <input
              id="update-titel"
              v-model="updateForm.titel"
              maxlength="255"
              required
              class="w-full rounded-lg border border-white/20 bg-white/5 px-4 py-3"
            />
          </div>

          <div>
            <label class="mb-2 block font-semibold" for="update-excerpt">Korte intro (optioneel)</label>
            <input
              id="update-excerpt"
              v-model="updateForm.excerpt"
              maxlength="320"
              class="w-full rounded-lg border border-white/20 bg-white/5 px-4 py-3"
            />
          </div>

          <div>
            <label class="mb-2 block font-semibold" for="update-format">Opmaak</label>
            <select
              id="update-format"
              v-model="updateForm.content_format"
              class="w-full rounded-lg border border-white/20 bg-robo-dark px-4 py-3"
            >
              <option value="html">HTML</option>
              <option value="markdown">Markdown</option>
            </select>
          </div>

          <div>
            <label class="mb-2 block font-semibold" for="update-content">Inhoud</label>
            <EditorFormattingToolbar
              :disabled="updateStatus === 'opslaan' || !magWijzigen"
              @action="runUpdateFormatAction"
            />
            <textarea
              ref="updateContentTextarea"
              id="update-content"
              v-model="updateForm.content"
              rows="8"
              required
              class="w-full rounded-lg border border-white/20 bg-white/5 px-4 py-3"
            />
          </div>

          <ContentResourcePanel
            :content-format="updateForm.content_format"
            :disabled="updateStatus === 'opslaan' || !magWijzigen"
            @insert="insertResourceSnippet"
          />

          <div>
            <label class="mb-2 block font-semibold" for="update-afbeeldingen">Afbeeldingen (optioneel)</label>
            <input
              id="update-afbeeldingen"
              type="file"
              multiple
              accept="image/jpeg,image/png,image/webp"
              class="w-full rounded-lg border border-white/20 bg-white/5 px-4 py-3 text-sm"
              @change="wijzigUpdateAfbeeldingen"
            />
            <ul v-if="updateAfbeeldingNamen.length" class="mt-2 list-disc space-y-1 pl-5 text-sm text-slate-300">
              <li v-for="naam in updateAfbeeldingNamen" :key="naam">{{ naam }}</li>
            </ul>
          </div>

          <div v-if="bewerkUpdateId" class="space-y-2 rounded-lg border border-white/10 bg-black/20 p-3">
            <p class="text-sm font-semibold text-slate-200">Bestaande afbeeldingen verwijderen</p>

            <p
              v-if="!teamUpdates.find((item) => item.id === bewerkUpdateId)?.afbeeldingen?.length"
              class="text-xs text-slate-400"
            >
              Dit voortgangsbericht heeft geen bestaande afbeeldingen.
            </p>

            <ul v-else class="space-y-2">
              <li
                v-for="afbeelding in teamUpdates.find((item) => item.id === bewerkUpdateId)?.afbeeldingen ?? []"
                :key="afbeelding.id"
                class="flex items-center gap-3"
              >
                <img :src="afbeelding.url" alt="Bestaande update-afbeelding" class="h-12 w-12 rounded object-cover" />
                <label class="inline-flex items-center gap-2 text-xs text-slate-300">
                  <input
                    type="checkbox"
                    :checked="verwijderUpdateAfbeeldingIds.includes(afbeelding.id)"
                    @change="toggleVerwijderUpdateAfbeelding(afbeelding.id)"
                  />
                  Verwijderen
                </label>
              </li>
            </ul>
          </div>

          <div v-if="updateFout" class="rounded-lg border border-red-500/30 bg-red-500/10 p-3 text-red-300">
            {{ updateFout }}
          </div>

          <div v-if="updateSucces" class="rounded-lg border border-green-500/30 bg-green-500/10 p-3 text-green-300">
            {{ updateSucces }}
          </div>

          <button
            type="submit"
            :disabled="updateStatus === 'opslaan' || !magWijzigen"
            class="rounded-lg bg-robo-orange px-6 py-3 font-bold text-white transition hover:bg-robo-orange-dark disabled:opacity-60"
          >
            <span v-if="updateStatus === 'opslaan'">Opslaan...</span>
            <span v-else-if="!magWijzigen">Log in om te plaatsen</span>
            <span v-else-if="bewerkUpdateId">Voortgang bijwerken</span>
            <span v-else>Voortgang plaatsen</span>
          </button>
          </fieldset>
        </form>

        <div>
          <h3 class="mb-3 text-lg font-bold">Geplaatste voortgangsberichten</h3>
          <p v-if="!teamUpdates.length" class="text-sm text-slate-400">Nog geen voortgangsberichten geplaatst.</p>

          <ul v-else class="space-y-3">
            <li v-for="update in teamUpdates" :key="update.id" class="rounded-lg border border-white/10 bg-black/20 p-4">
              <p class="font-semibold">{{ update.titel }}</p>
              <p v-if="update.published_at" class="text-xs text-slate-400">{{ formatDatum(update.published_at) }}</p>
              <p v-if="update.excerpt" class="mt-1 text-sm text-slate-300">{{ update.excerpt }}</p>
              <button
                type="button"
                class="mt-3 rounded border border-robo-orange px-3 py-1 text-xs font-semibold text-robo-orange hover:bg-robo-orange/10"
                @click="startBewerkUpdate(update)"
              >
                Bewerk bericht
              </button>
            </li>
          </ul>
        </div>
      </section>
    </section>
  </main>
</template>
