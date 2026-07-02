/**
 * Roboktober frontend router.
 *
 * All routes use lazy loading (dynamic import) to minimize initial bundle size.
 * Route names match page slugs where possible for easy API integration.
 *
 * @see PLAN.md §6.x — page designs
 */
import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),

  // Scroll to top on route change, restore position on back/forward
  scrollBehavior(_to, _from, savedPosition) {
    if (savedPosition) return savedPosition
    return { top: 0, behavior: 'smooth' }
  },

  routes: [
    {
      path: '/',
      name: 'home',
      component: () => import('../views/HomeView.vue'),
      meta: { title: 'Roboktober — Combat robots bij Hackerspace Drenthe' },
    },
    {
      path: '/programma',
      name: 'programma',
      component: () => import('../views/ProgrammaView.vue'),
      meta: { title: 'Programma — Roboktober' },
    },
    {
      path: '/teams',
      name: 'teams',
      component: () => import('../views/TeamsView.vue'),
      meta: { title: 'Teams & Robots — Roboktober' },
    },
    {
      path: '/teams/:id',
      name: 'team-detail',
      component: () => import('../views/TeamDetailView.vue'),
      meta: { title: 'Team — Roboktober' },
    },
    {
      path: '/nieuws',
      name: 'nieuws',
      component: () => import('../views/NieuwsView.vue'),
      meta: { title: 'Nieuws — Roboktober' },
    },
    {
      path: '/nieuws/:slug',
      name: 'nieuws-artikel',
      component: () => import('../views/NieuwsArtikelView.vue'),
      meta: { title: 'Artikel — Roboktober' },
    },
    {
      path: '/build-hub',
      name: 'build-hub',
      component: () => import('../views/BuildHubView.vue'),
      meta: { title: 'Build Hub — Roboktober' },
    },
    {
      path: '/aanmelden',
      name: 'aanmelden',
      component: () => import('../views/AanmeldenView.vue'),
      meta: { title: 'Aanmelden — Roboktober' },
    },
    {
      path: '/walter',
      name: 'walter',
      component: () => import('../views/WalterView.vue'),
      meta: { title: 'Walter — Gastheer van Roboktober' },
    },
    {
      path: '/bouwen',
      name: 'bouwen',
      component: () => import('../views/BouwenView.vue'),
      meta: { title: 'Bouw je antweight — Roboktober' },
    },
    {
      path: '/:slug',
      name: 'pagina',
      component: () => import('../views/PaginaView.vue'),
    },
    {
      path: '/:pathMatch(.*)*',
      name: 'niet-gevonden',
      component: () => import('../views/NietGevondenView.vue'),
      meta: { title: 'Pagina niet gevonden — Roboktober' },
    },
  ],
})

// Update document title on route change
router.afterEach((to) => {
  if (typeof to.meta.title === 'string') {
    document.title = to.meta.title
  }
})

export default router
