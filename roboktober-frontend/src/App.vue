<script setup lang="ts">
/**
 * Root application component.
 *
 * Contains the skip-nav link (WCAG 2.2 AA) and main navigation.
 * Navigation is sticky and collapses to hamburger on mobile.
 *
 * @see PLAN.md §4 — WCAG 2.2 AA compliance
 */
import { ref } from 'vue'
import { RouterLink, RouterView } from 'vue-router'

const menuOpen = ref(false)

const desktopNavLinkClass =
  'rounded-md px-3 py-2 text-slate-300 transition hover:bg-white/10 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-robo-orange/80'
const desktopNavActiveClass = 'bg-robo-orange/90 text-white shadow-sm'

const mobileNavLinkClass =
  'block rounded-md px-3 py-2 text-slate-300 transition hover:bg-white/10 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-robo-orange/80'
const mobileNavActiveClass = 'bg-white/10 text-white'

function sluitMenu(): void {
  menuOpen.value = false
}
</script>

<template>
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
      <ul
        class="hidden items-center gap-6 md:flex"
        role="list"
      >
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
