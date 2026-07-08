<script setup lang="ts">
/**
 * Root application component.
 *
 * Contains the skip-nav link (WCAG 2.2 AA) and main navigation.
 * Navigation is sticky and collapses to hamburger on mobile.
 *
 * @see PLAN.md §4 — WCAG 2.2 AA compliance
 */
import { onMounted, onUnmounted, ref } from 'vue'
import { RouterLink, RouterView } from 'vue-router'
import { useAuth } from '@/composables/useAuth'

const siteGatePassword = 'Drenthe'
const siteGateSessionKey = 'roboktober-site-gate-unlocked'

const menuOpen = ref(false)
const accountMenuOpen = ref(false)
const accountMenuRef = ref<HTMLElement | null>(null)
const auth = useAuth()
const gatePasswordInput = ref('')
const gateError = ref('')
const gateUnlocked = ref(false)

const desktopNavLinkClass =
  'rounded-md px-3 py-2 text-slate-300 transition hover:bg-white/10 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-robo-orange/80'
const desktopNavActiveClass = 'bg-robo-orange/90 text-white shadow-sm'

const mobileNavLinkClass =
  'block rounded-md px-3 py-2 text-slate-300 transition hover:bg-white/10 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-robo-orange/80'
const mobileNavActiveClass = 'bg-white/10 text-white'

function sluitMenu(): void {
  menuOpen.value = false
}

function setGateUnlockedInSession(): void {
  try {
    sessionStorage.setItem(siteGateSessionKey, '1')
  } catch {
    // Ignore storage failures (e.g. strict privacy mode) and keep in-memory unlock.
  }
}

function getGateUnlockedFromSession(): boolean {
  try {
    return sessionStorage.getItem(siteGateSessionKey) === '1'
  } catch {
    return false
  }
}

function submitSiteGate(): void {
  const normalizedInput = gatePasswordInput.value.trim()

  if (normalizedInput === siteGatePassword) {
    gateUnlocked.value = true
    gateError.value = ''
    setGateUnlockedInSession()
    return
  }

  gateError.value = 'Onjuist wachtwoord. Probeer opnieuw.'
}

function openAccountMenu(): void {
  accountMenuOpen.value = true
}

function closeAccountMenu(): void {
  accountMenuOpen.value = false
}

function toggleAccountMenu(): void {
  accountMenuOpen.value = !accountMenuOpen.value
}

function handleDocumentPointerDown(event: MouseEvent): void {
  const target = event.target

  if (!(target instanceof Node)) {
    return
  }

  if (!accountMenuRef.value?.contains(target)) {
    closeAccountMenu()
  }
}

function handleDocumentKeydown(event: KeyboardEvent): void {
  if (event.key === 'Escape') {
    closeAccountMenu()
  }
}

void auth.initAuth()

onMounted(() => {
  gateUnlocked.value = getGateUnlockedFromSession()
  document.addEventListener('mousedown', handleDocumentPointerDown)
  document.addEventListener('keydown', handleDocumentKeydown)
})

onUnmounted(() => {
  document.removeEventListener('mousedown', handleDocumentPointerDown)
  document.removeEventListener('keydown', handleDocumentKeydown)
})

async function handleLogout(): Promise<void> {
  await auth.logout()
  closeAccountMenu()
  sluitMenu()
}
</script>

<template>
  <div v-if="!gateUnlocked" class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-950/90 p-6">
    <div class="w-full max-w-md rounded-2xl border border-white/20 bg-robo-dark p-6 shadow-2xl">
      <h1 class="text-2xl font-black text-white">Site in aanbouw</h1>
      <p class="mt-3 text-sm text-slate-300">
        Roboktober is nog niet open voor normale bezoekers. De site gaat begin augustus 2026 live.
      </p>
      <p class="mt-2 text-sm text-slate-300">
        Voer het toegangswachtwoord in om verder te gaan.
      </p>
      <p class="mt-2 rounded-lg border border-amber-300/40 bg-amber-500/10 px-3 py-2 text-sm font-semibold text-amber-100">
        Testomgeving: voer geen echte persoonsgegevens of andere niet-testdata in.
      </p>

      <form class="mt-5 space-y-3" @submit.prevent="submitSiteGate">
        <label for="site-gate-password" class="block text-sm font-semibold text-slate-200">Wachtwoord</label>
        <input
          id="site-gate-password"
          v-model="gatePasswordInput"
          @input="gateError = ''"
          type="password"
          class="w-full rounded-lg border border-white/25 bg-slate-950/70 px-3 py-2 text-white placeholder:text-slate-500 focus:border-robo-orange focus:outline-none"
          placeholder="Voer wachtwoord in"
          autocomplete="current-password"
          required
        >

        <p v-if="gateError" class="text-sm font-semibold text-red-300">{{ gateError }}</p>

        <button
          type="submit"
          class="w-full rounded-lg bg-robo-orange px-4 py-2 font-bold text-white transition hover:bg-robo-orange-dark"
        >
          Verder
        </button>
      </form>
    </div>
  </div>

  <template v-else>
    <!-- Skip-nav link for keyboard/screen reader users (WCAG 2.4.1) -->
    <a
      href="#main-content"
      class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded focus:bg-robo-orange focus:px-4 focus:py-2 focus:font-bold focus:text-white"
    >
      Ga naar inhoud
    </a>

  <!-- Navigatie -->
  <header class="sticky top-0 z-40 border-b border-white/10 bg-robo-dark/95 backdrop-blur">
    <nav
      class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4"
      aria-label="Hoofdnavigatie"
    >
      <!-- Logo -->
      <RouterLink
        to="/"
        class="text-xl font-black text-white"
        aria-label="Roboktober – naar de homepage"
        @click="sluitMenu"
      >
        Robo<span class="text-robo-orange">ktober</span>
      </RouterLink>

      <!-- Desktop navigatie -->
      <div class="hidden items-center gap-6 md:flex">
        <ul class="flex items-center gap-3" role="list">
          <li>
            <RouterLink to="/programma" :class="desktopNavLinkClass" :active-class="desktopNavActiveClass">Programma</RouterLink>
          </li>
          <li>
            <RouterLink to="/teams" :class="desktopNavLinkClass" :active-class="desktopNavActiveClass">Teams</RouterLink>
          </li>
          <li>
            <RouterLink to="/nieuws" :class="desktopNavLinkClass" :active-class="desktopNavActiveClass">Nieuws</RouterLink>
          </li>
          <li>
            <RouterLink to="/build-hub" :class="desktopNavLinkClass" :active-class="desktopNavActiveClass">Build Hub</RouterLink>
          </li>
          <li>
            <RouterLink to="/bouwen" :class="desktopNavLinkClass" :active-class="desktopNavActiveClass">Bouwen</RouterLink>
          </li>
          <li>
            <RouterLink to="/walter" :class="desktopNavLinkClass" :active-class="desktopNavActiveClass">Uw Gastheer</RouterLink>
          </li>
          <li>
            <RouterLink
              to="/aanmelden"
              class="rounded-lg bg-robo-orange px-4 py-2 font-bold text-white hover:bg-robo-orange-dark"
              active-class="bg-robo-orange-dark ring-2 ring-robo-orange/80"
            >
              Aanmelden
            </RouterLink>
          </li>
        </ul>

        <div class="h-6 w-px bg-white/15" aria-hidden="true" />

        <div ref="accountMenuRef" class="relative">
          <button
            type="button"
            class="rounded-md px-3 py-2 text-slate-300 transition hover:bg-white/10 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-robo-orange/80"
            aria-haspopup="menu"
            :aria-expanded="accountMenuOpen"
            aria-controls="desktop-account-menu"
            @click="toggleAccountMenu"
            @focus="openAccountMenu"
          >
              Mijn account
          </button>

            <div
              v-if="accountMenuOpen"
              id="desktop-account-menu"
              role="menu"
              class="absolute right-0 mt-2 w-64 space-y-1 rounded-xl border border-white/15 bg-robo-dark p-2 shadow-xl"
            >
              <p class="px-3 py-2 text-xs font-bold uppercase tracking-wide text-slate-400">Persoonlijk</p>

              <RouterLink to="/account" :class="desktopNavLinkClass" :active-class="desktopNavActiveClass" @click="closeAccountMenu">Mijn Account</RouterLink>
              <RouterLink to="/aanmelding/wijzigen" :class="desktopNavLinkClass" :active-class="desktopNavActiveClass" @click="closeAccountMenu">Mijn Aanmelding</RouterLink>

              <template v-if="!auth.isAuthenticated.value">
                <RouterLink to="/login" :class="desktopNavLinkClass" :active-class="desktopNavActiveClass" @click="closeAccountMenu">Inloggen</RouterLink>
                <RouterLink to="/registreren" :class="desktopNavLinkClass" :active-class="desktopNavActiveClass" @click="closeAccountMenu">Registreren</RouterLink>
              </template>

              <template v-else>
                <div v-if="auth.hasRole('moderator')" class="my-2 border-t border-white/10" />

                <RouterLink v-if="auth.hasRole('moderator')" to="/admin" :class="desktopNavLinkClass" :active-class="desktopNavActiveClass" @click="closeAccountMenu">Dashboard</RouterLink>
                <RouterLink v-if="auth.hasRole('moderator')" to="/admin/teams" :class="desktopNavLinkClass" :active-class="desktopNavActiveClass" @click="closeAccountMenu">Admin Teams</RouterLink>
                <RouterLink v-if="auth.hasRole('moderator')" to="/admin/posts" :class="desktopNavLinkClass" :active-class="desktopNavActiveClass" @click="closeAccountMenu">Admin Posts</RouterLink>
                <RouterLink v-if="auth.hasRole('moderator')" to="/admin/pages" :class="desktopNavLinkClass" :active-class="desktopNavActiveClass" @click="closeAccountMenu">Admin Pagina's</RouterLink>
                <RouterLink v-if="auth.hasRole('moderator')" to="/admin/team-updates" :class="desktopNavLinkClass" :active-class="desktopNavActiveClass" @click="closeAccountMenu">Admin Updates</RouterLink>
                <RouterLink v-if="auth.hasRole('moderator')" to="/admin/media" :class="desktopNavLinkClass" :active-class="desktopNavActiveClass" @click="closeAccountMenu">Media Library</RouterLink>
                <RouterLink v-if="auth.hasRole('admin')" to="/admin/users" :class="desktopNavLinkClass" :active-class="desktopNavActiveClass" @click="closeAccountMenu">Admin Users</RouterLink>
                <RouterLink v-if="auth.hasRole('admin')" to="/admin/audit-logs" :class="desktopNavLinkClass" :active-class="desktopNavActiveClass" @click="closeAccountMenu">Audit Logs</RouterLink>

                <div class="my-2 border-t border-white/10" />
                <button
                  type="button"
                  :class="desktopNavLinkClass + ' w-full text-left'"
                  @click="handleLogout"
                >
                  Uitloggen
                </button>
              </template>
            </div>
        </div>
      </div>

      <!-- Mobiel hamburger knop -->
      <button
        class="rounded-lg p-2 text-white hover:bg-white/10 md:hidden"
        :aria-expanded="menuOpen"
        aria-controls="mobile-menu"
        aria-label="Menu openen"
        @click="menuOpen = !menuOpen"
      >
        <span class="block h-0.5 w-6 bg-current transition-transform" :class="{ 'translate-y-1.5 rotate-45': menuOpen }" />
        <span class="my-1.5 block h-0.5 w-6 bg-current transition-opacity" :class="{ 'opacity-0': menuOpen }" />
        <span class="block h-0.5 w-6 bg-current transition-transform" :class="{ '-translate-y-1.5 -rotate-45': menuOpen }" />
      </button>
    </nav>

    <!-- Mobiel menu -->
    <div
      v-show="menuOpen"
      id="mobile-menu"
      class="border-t border-white/10 bg-robo-dark px-6 py-4 md:hidden"
    >
      <ul
        class="space-y-3"
        role="list"
      >
        <li>
          <RouterLink
            to="/programma"
            :class="mobileNavLinkClass"
            :active-class="mobileNavActiveClass"
            @click="sluitMenu"
          >Programma</RouterLink>
        </li>
        <li>
          <RouterLink
            to="/teams"
            :class="mobileNavLinkClass"
            :active-class="mobileNavActiveClass"
            @click="sluitMenu"
          >Teams</RouterLink>
        </li>
        <li>
          <RouterLink
            to="/nieuws"
            :class="mobileNavLinkClass"
            :active-class="mobileNavActiveClass"
            @click="sluitMenu"
          >Nieuws</RouterLink>
        </li>
        <li>
          <RouterLink
            to="/build-hub"
            :class="mobileNavLinkClass"
            :active-class="mobileNavActiveClass"
            @click="sluitMenu"
          >Build Hub</RouterLink>
        </li>
        <li>
          <RouterLink
            to="/bouwen"
            :class="mobileNavLinkClass"
            :active-class="mobileNavActiveClass"
            @click="sluitMenu"
          >Bouwen</RouterLink>
        </li>
        <li>
          <RouterLink
            to="/walter"
            :class="mobileNavLinkClass"
            :active-class="mobileNavActiveClass"
            @click="sluitMenu"
          >Uw Gastheer</RouterLink>
        </li>
        <li>
          <RouterLink
            to="/aanmelden"
            class="block rounded-lg bg-robo-orange px-4 py-2 text-center font-bold text-white hover:bg-robo-orange-dark"
            active-class="bg-robo-orange-dark ring-2 ring-robo-orange/80"
            @click="sluitMenu"
          >
            Aanmelden
          </RouterLink>
        </li>

        <li class="mt-4 border-t border-white/15 pt-4 text-xs font-bold uppercase tracking-wide text-slate-400">Persoonlijk</li>
        <li>
          <RouterLink
            to="/account"
            :class="mobileNavLinkClass"
            :active-class="mobileNavActiveClass"
            @click="sluitMenu"
          >Mijn Account</RouterLink>
        </li>
        <li>
          <RouterLink
            to="/aanmelding/wijzigen"
            :class="mobileNavLinkClass"
            :active-class="mobileNavActiveClass"
            @click="sluitMenu"
          >Mijn Aanmelding</RouterLink>
        </li>
        <template v-if="!auth.isAuthenticated.value">
          <li>
            <RouterLink
              to="/login"
              :class="mobileNavLinkClass"
              :active-class="mobileNavActiveClass"
              @click="sluitMenu"
            >Inloggen</RouterLink>
          </li>
          <li>
            <RouterLink
              to="/registreren"
              :class="mobileNavLinkClass"
              :active-class="mobileNavActiveClass"
              @click="sluitMenu"
            >Registreren</RouterLink>
          </li>
        </template>
        <template v-else>
          <li v-if="auth.hasRole('moderator')">
            <RouterLink
              to="/admin"
              :class="mobileNavLinkClass"
              :active-class="mobileNavActiveClass"
              @click="sluitMenu"
            >Dashboard</RouterLink>
          </li>
          <li v-if="auth.hasRole('moderator')">
            <RouterLink
              to="/admin/teams"
              :class="mobileNavLinkClass"
              :active-class="mobileNavActiveClass"
              @click="sluitMenu"
            >Admin Teams</RouterLink>
          </li>
          <li v-if="auth.hasRole('moderator')">
            <RouterLink
              to="/admin/posts"
              :class="mobileNavLinkClass"
              :active-class="mobileNavActiveClass"
              @click="sluitMenu"
            >Admin Posts</RouterLink>
          </li>
          <li v-if="auth.hasRole('moderator')">
            <RouterLink
              to="/admin/pages"
              :class="mobileNavLinkClass"
              :active-class="mobileNavActiveClass"
              @click="sluitMenu"
            >Admin Pagina's</RouterLink>
          </li>
          <li v-if="auth.hasRole('moderator')">
            <RouterLink
              to="/admin/team-updates"
              :class="mobileNavLinkClass"
              :active-class="mobileNavActiveClass"
              @click="sluitMenu"
            >Admin Updates</RouterLink>
          </li>
          <li v-if="auth.hasRole('moderator')">
            <RouterLink
              to="/admin/media"
              :class="mobileNavLinkClass"
              :active-class="mobileNavActiveClass"
              @click="sluitMenu"
            >Media Library</RouterLink>
          </li>
          <li v-if="auth.hasRole('admin')">
            <RouterLink
              to="/admin/users"
              :class="mobileNavLinkClass"
              :active-class="mobileNavActiveClass"
              @click="sluitMenu"
            >Admin Users</RouterLink>
          </li>
          <li v-if="auth.hasRole('admin')">
            <RouterLink
              to="/admin/audit-logs"
              :class="mobileNavLinkClass"
              :active-class="mobileNavActiveClass"
              @click="sluitMenu"
            >Audit Logs</RouterLink>
          </li>
          <li>
            <button
              type="button"
              :class="mobileNavLinkClass"
              @click="handleLogout"
            >
              Uitloggen
            </button>
          </li>
        </template>
      </ul>
    </div>
  </header>

  <!-- Main content landmark -->
  <div id="main-content">
    <RouterView />
  </div>

  <!-- Footer -->
    <footer class="border-t border-white/10 bg-robo-dark py-8 text-center text-sm text-slate-500">
      <p>© 2026 Roboktober · Hackerspace Drenthe</p>
    </footer>
  </template>
</template>
