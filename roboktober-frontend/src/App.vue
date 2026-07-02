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
        <li><RouterLink to="/programma" class="text-slate-300 hover:text-white">Programma</RouterLink></li>
        <li><RouterLink to="/teams" class="text-slate-300 hover:text-white">Teams</RouterLink></li>
        <li><RouterLink to="/nieuws" class="text-slate-300 hover:text-white">Nieuws</RouterLink></li>
        <li><RouterLink to="/build-hub" class="text-slate-300 hover:text-white">Build Hub</RouterLink></li>
        <li><RouterLink to="/bouwen" class="text-slate-300 hover:text-white">Bouwen</RouterLink></li>
        <li><RouterLink to="/walter" class="text-slate-300 hover:text-white">Uw Gastheer</RouterLink></li>
        <li>
          <RouterLink
            to="/aanmelden"
            class="rounded-lg bg-robo-orange px-4 py-2 font-bold text-white hover:bg-robo-orange-dark"
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
        <li><RouterLink to="/programma" class="block py-1 text-slate-300 hover:text-white" @click="sluitMenu">Programma</RouterLink></li>
        <li><RouterLink to="/teams" class="block py-1 text-slate-300 hover:text-white" @click="sluitMenu">Teams</RouterLink></li>
        <li><RouterLink to="/nieuws" class="block py-1 text-slate-300 hover:text-white" @click="sluitMenu">Nieuws</RouterLink></li>
        <li><RouterLink to="/build-hub" class="block py-1 text-slate-300 hover:text-white" @click="sluitMenu">Build Hub</RouterLink></li>
        <li><RouterLink to="/bouwen" class="block py-1 text-slate-300 hover:text-white" @click="sluitMenu">Bouwen</RouterLink></li>
        <li><RouterLink to="/walter" class="block py-1 text-slate-300 hover:text-white" @click="sluitMenu">Uw Gastheer</RouterLink></li>
        <li>
          <RouterLink
            to="/aanmelden"
            class="block rounded-lg bg-robo-orange px-4 py-2 text-center font-bold text-white hover:bg-robo-orange-dark"
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


<style scoped>
header {
  line-height: 1.5;
  max-height: 100vh;
}

.logo {
  display: block;
  margin: 0 auto 2rem;
}

nav {
  width: 100%;
  font-size: 12px;
  text-align: center;
  margin-top: 2rem;
}

nav a.router-link-exact-active {
  color: var(--color-text);
}

nav a.router-link-exact-active:hover {
  background-color: transparent;
}

nav a {
  display: inline-block;
  padding: 0 1rem;
  border-left: 1px solid var(--color-border);
}

nav a:first-of-type {
  border: 0;
}

@media (min-width: 1024px) {
  header {
    display: flex;
    place-items: center;
    padding-right: calc(var(--section-gap) / 2);
  }

  .logo {
    margin: 0 2rem 0 0;
  }

  header .wrapper {
    display: flex;
    place-items: flex-start;
    flex-wrap: wrap;
  }

  nav {
    text-align: left;
    margin-left: -1rem;
    font-size: 1rem;

    padding: 1rem 0;
    margin-top: 1rem;
  }
}
</style>
