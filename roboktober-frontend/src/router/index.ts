/**
 * Roboktober frontend router.
 *
 * All routes use lazy loading (dynamic import) to minimize initial bundle size.
 * Route names match page slugs where possible for easy API integration.
 *
 * @see PLAN.md §6.x — page designs
 */
import { createRouter, createWebHistory } from 'vue-router'
import { useAuth } from '@/composables/useAuth'
import { applySeoMeta } from '@/utils/seo'

type AppRouteMeta = {
  title?: string
  description?: string
  robots?: string
  canonicalPath?: string
  ogType?: 'website' | 'article'
  requiresAuth?: boolean
  minRole?: 'visitor' | 'teamcaptain' | 'moderator' | 'admin'
}

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
      meta: {
        title: 'Roboktober — Combat robots bij Hackerspace Drenthe',
        description: 'Bouw je eigen gevechtsrobot bij Roboktober in Hackerspace Drenthe. Antweight robotwars, workshops en competitie.',
      },
    },
    {
      path: '/programma',
      name: 'programma',
      component: () => import('../views/ProgrammaView.vue'),
      meta: {
        title: 'Programma — Roboktober',
        description: 'Bekijk het programma van Roboktober met kickoff, workshops, keuringen en battle day in Hackerspace Drenthe.',
      },
    },
    {
      path: '/competitie',
      redirect: '/teams/competitie',
    },
    {
      path: '/teams',
      name: 'teams',
      component: () => import('../views/TeamsCompetitionView.vue'),
      meta: {
        title: 'Teams & Robots — Roboktober',
        description: 'Bekijk alle teams en robots die meedoen aan Roboktober, inclusief filters en teamdetails.',
      },
    },
    {
      path: '/teams/competitie',
      name: 'teams-competitie',
      component: () => import('../views/TeamsCompetitionView.vue'),
      meta: {
        title: 'Competitieklassement — Roboktober',
        description: 'Volg het actuele competitieklassement van Roboktober per editie, categorie en robot.',
      },
    },
    {
      path: '/teams/:id',
      name: 'team-detail',
      component: () => import('../views/TeamDetailView.vue'),
      meta: {
        title: 'Team — Roboktober',
        description: 'Bekijk teaminformatie, robots en updates van deelnemers aan Roboktober.',
      },
    },
    {
      path: '/nieuws',
      name: 'nieuws',
      component: () => import('../views/NieuwsView.vue'),
      meta: {
        title: 'Nieuws — Roboktober',
        description: 'Lees het laatste nieuws, updates en aankondigingen rond Roboktober en combat robotics in Drenthe.',
      },
    },
    {
      path: '/nieuws/:slug',
      name: 'nieuws-artikel',
      component: () => import('../views/NieuwsArtikelView.vue'),
      meta: {
        title: 'Artikel — Roboktober',
        description: 'Nieuwsartikel van Roboktober.',
        ogType: 'article',
      },
    },
    {
      path: '/antweight',
      name: 'antweight',
      component: () => import('../views/AntweightView.vue'),
      meta: {
        title: 'Wat is een antweight robot? — Roboktober',
        description: 'Ontdek wat antweight combat robots zijn, hoe je begint en waarom deze klasse perfect is voor starters.',
      },
    },
    {
      path: '/build-hub',
      redirect: '/bouwen/links',
    },
    {
      path: '/aanmelden',
      name: 'aanmelden',
      component: () => import('../views/AanmeldenView.vue'),
      meta: {
        title: 'Aanmelden — Roboktober',
        requiresAuth: true,
      },
    },
    {
      path: '/aanmelding/bewerken',
      name: 'aanmelding-bewerken',
      component: () => import('../views/AanmeldingBewerkenView.vue'),
      meta: {
        title: 'Aanmelding bewerken — Roboktober',
        requiresAuth: true,
      },
    },
    {
      path: '/aanmelding/wijzigen',
      name: 'aanmelding-wijzigen',
      component: () => import('../views/AanmeldingWijzigenView.vue'),
      meta: {
        title: 'Aanmelding wijzigen — Roboktober',
        requiresAuth: true,
      },
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('../views/LoginView.vue'),
      meta: { title: 'Inloggen — Roboktober' },
    },
    {
      path: '/wachtwoord-vergeten',
      name: 'wachtwoord-vergeten',
      component: () => import('../views/ForgotPasswordView.vue'),
      meta: { title: 'Wachtwoord vergeten — Roboktober' },
    },
    {
      path: '/wachtwoord-reset',
      name: 'wachtwoord-reset',
      component: () => import('../views/ResetPasswordView.vue'),
      meta: { title: 'Wachtwoord resetten — Roboktober' },
    },
    {
      path: '/twee-factor',
      name: 'twee-factor-challenge',
      component: () => import('../views/TwoFactorChallengeView.vue'),
      meta: { title: '2FA verificatie — Roboktober' },
    },
    {
      path: '/twee-factor-instellen',
      name: 'twee-factor-setup',
      component: () => import('../views/TwoFactorSetupView.vue'),
      meta: {
        title: '2FA instellen — Roboktober',
        requiresAuth: true,
      },
    },
    {
      path: '/registreren',
      name: 'registreren',
      component: () => import('../views/RegisterView.vue'),
      meta: { title: 'Registreren — Roboktober' },
    },
    {
      path: '/account',
      name: 'account',
      component: () => import('../views/AccountView.vue'),
      meta: {
        title: 'Mijn account — Roboktober',
        requiresAuth: true,
      },
    },
    {
      path: '/admin',
      name: 'admin-dashboard',
      component: () => import('../views/AdminDashboardView.vue'),
      meta: {
        title: 'Admin Dashboard — Roboktober',
        requiresAuth: true,
        minRole: 'moderator',
      },
    },
    {
      path: '/admin/teams',
      name: 'admin-teams',
      component: () => import('../views/AdminTeamsView.vue'),
      meta: {
        title: 'Admin Teams — Roboktober',
        requiresAuth: true,
        minRole: 'moderator',
      },
    },
    {
      path: '/admin/edities',
      name: 'admin-edities',
      component: () => import('../views/AdminEditionsView.vue'),
      meta: {
        title: 'Admin Edities — Roboktober',
        requiresAuth: true,
        minRole: 'moderator',
      },
    },
    {
      path: '/admin/robots',
      name: 'admin-robots',
      component: () => import('../views/AdminRobotsView.vue'),
      meta: {
        title: 'Admin Robots — Roboktober',
        requiresAuth: true,
        minRole: 'moderator',
      },
    },
    {
      path: '/admin/links',
      name: 'admin-links',
      component: () => import('../views/AdminLinksView.vue'),
      meta: {
        title: 'Admin Links — Roboktober',
        requiresAuth: true,
        minRole: 'moderator',
      },
    },
    {
      path: '/admin/competitie',
      name: 'admin-competitie',
      component: () => import('../views/AdminCompetitionView.vue'),
      meta: {
        title: 'Admin Competitie — Roboktober',
        requiresAuth: true,
        minRole: 'moderator',
      },
    },
    {
      path: '/admin/programma',
      name: 'admin-programma',
      component: () => import('../views/AdminProgrammaView.vue'),
      meta: {
        title: 'Admin Programma — Roboktober',
        requiresAuth: true,
        minRole: 'moderator',
      },
    },
    {
      path: '/admin/posts',
      name: 'admin-posts',
      component: () => import('../views/AdminPostsView.vue'),
      meta: {
        title: 'Admin Posts — Roboktober',
        requiresAuth: true,
        minRole: 'moderator',
      },
    },
    {
      path: '/admin/posts/:id/edit',
      name: 'admin-post-edit',
      component: () => import('../views/AdminPostEditView.vue'),
      meta: {
        title: 'Admin Post Bewerken — Roboktober',
        requiresAuth: true,
        minRole: 'moderator',
      },
    },
    {
      path: '/admin/pages',
      name: 'admin-pages',
      component: () => import('../views/AdminPagesView.vue'),
      meta: {
        title: 'Admin Pagina\'s — Roboktober',
        requiresAuth: true,
        minRole: 'moderator',
      },
    },
    {
      path: '/admin/pages/:id/edit',
      name: 'admin-page-edit',
      component: () => import('../views/AdminPageEditView.vue'),
      meta: {
        title: 'Admin Pagina Bewerken — Roboktober',
        requiresAuth: true,
        minRole: 'moderator',
      },
    },
    {
      path: '/admin/team-updates',
      name: 'admin-team-updates',
      component: () => import('../views/AdminTeamUpdatesView.vue'),
      meta: {
        title: 'Admin Team Updates — Roboktober',
        requiresAuth: true,
        minRole: 'moderator',
      },
    },
    {
      path: '/admin/team-updates/:id/edit',
      name: 'admin-team-update-edit',
      component: () => import('../views/AdminTeamUpdateEditView.vue'),
      meta: {
        title: 'Admin Team Update Bewerken — Roboktober',
        requiresAuth: true,
        minRole: 'moderator',
      },
    },
    {
      path: '/admin/media',
      name: 'admin-media',
      component: () => import('../views/AdminMediaLibraryView.vue'),
      meta: {
        title: 'Admin Media Library — Roboktober',
        requiresAuth: true,
        minRole: 'moderator',
      },
    },
    {
      path: '/admin/users',
      name: 'admin-users',
      component: () => import('../views/AdminUsersView.vue'),
      meta: {
        title: 'Admin Gebruikers — Roboktober',
        requiresAuth: true,
        minRole: 'admin',
      },
    },
    {
      path: '/admin/audit-logs',
      name: 'admin-audit-logs',
      component: () => import('../views/AdminAuditLogsView.vue'),
      meta: {
        title: 'Admin Audit Logs — Roboktober',
        requiresAuth: true,
        minRole: 'admin',
      },
    },
    {
      path: '/admin/page-analytics',
      name: 'admin-page-analytics',
      component: () => import('../views/AdminPageAnalyticsView.vue'),
      meta: {
        title: 'Admin Page Analytics — Roboktober',
        requiresAuth: true,
        minRole: 'admin',
      },
    },
    {
      path: '/forbidden',
      name: 'forbidden',
      component: () => import('../views/ForbiddenView.vue'),
      meta: { title: 'Geen toegang — Roboktober' },
    },
    {
      path: '/walter',
      name: 'walter',
      component: () => import('../views/WalterView.vue'),
      meta: {
        title: 'Walter — Gastheer van Roboktober',
        description: 'Maak kennis met Walter (Wallieonline), initiatiefnemer van Roboktober en mentor binnen Hackerspace Drenthe.',
      },
    },
    {
      path: '/bouwen',
      redirect: '/bouwen/bouwgids',
    },
    {
      path: '/bouwen/bouwgids',
      name: 'bouwen-bouwgids',
      component: () => import('../views/BouwenView.vue'),
      meta: {
        title: 'Bouw je antweight — Roboktober',
        description: 'Praktische bouwgids om stap voor stap je eigen antweight robot te bouwen voor Roboktober.',
      },
    },
    {
      path: '/bouwen/links',
      name: 'bouwen-links',
      component: () => import('../views/BuildHubView.vue'),
      meta: {
        title: 'Build Hub — Roboktober',
        description: 'Gebruik de Build Hub met onderdelen, tools en bronnen om je robot sneller te ontwerpen en bouwen.',
      },
    },
    {
      path: '/:slug',
      name: 'pagina',
      component: () => import('../views/PaginaView.vue'),
      meta: {
        title: 'Pagina — Roboktober',
        description: 'Informatiepagina van Roboktober.',
      },
    },
    {
      path: '/:pathMatch(.*)*',
      name: 'niet-gevonden',
      component: () => import('../views/NietGevondenView.vue'),
      meta: {
        title: 'Pagina niet gevonden — Roboktober',
        robots: 'noindex,nofollow',
      },
    },
  ],
})

router.beforeEach(async (to) => {
  const auth = useAuth()

  if (!auth.initialized.value) {
    await auth.initAuth()
  }

  if (
    auth.isAuthenticated.value
    && auth.user.value
    && !auth.user.value.two_factor_enabled
    && to.name !== 'twee-factor-setup'
  ) {
    return {
      name: 'twee-factor-setup',
      query: { redirect: to.fullPath },
    }
  }

  const meta = (to.meta ?? {}) as AppRouteMeta

  if (meta.requiresAuth && !auth.isAuthenticated.value) {
    return {
      name: 'login',
      query: { redirect: to.fullPath },
    }
  }

  if (meta.minRole && !auth.hasRole(meta.minRole)) {
    return {
      name: 'forbidden',
    }
  }

  return true
})

router.afterEach((to) => {
  const meta = (to.meta ?? {}) as AppRouteMeta
  const title = typeof meta.title === 'string' ? meta.title : 'Roboktober'
  const isPrivateRoute = to.path.startsWith('/admin') || to.path.startsWith('/account') || Boolean(meta.requiresAuth)

  applySeoMeta({
    title,
    description: meta.description,
    canonicalPath: meta.canonicalPath ?? to.fullPath,
    robots: meta.robots ?? (isPrivateRoute ? 'noindex,nofollow' : 'index,follow'),
    ogType: meta.ogType,
  })
})

export default router
