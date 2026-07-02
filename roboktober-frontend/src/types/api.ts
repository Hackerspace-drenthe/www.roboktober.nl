/**
 * Roboktober API TypeScript types.
 *
 * Mirror of the Laravel API Resources defined in roboktober-api.
 * @see PLAN.md §5.3 — API response examples
 */

// ---------------------------------------------------------------------------
// Media
// ---------------------------------------------------------------------------

export interface Media {
  id: number
  naam: string
  url: string
  mime_type: string
  extensie: string
  grootte: number
  meta: Record<string, unknown> | null
  versie: string | null
  downloads: number
  alt_tekst: string | null
  onderschrift: string | null
  volgorde: number | null
}

// ---------------------------------------------------------------------------
// Team
// ---------------------------------------------------------------------------

export type TeamStatus = 'pending' | 'approved' | 'rejected'

export interface Team {
  id: number
  naam: string
  status: TeamStatus
  status_label: string
  foto: Media | null
  robots: Robot[]
}

// ---------------------------------------------------------------------------
// Robot
// ---------------------------------------------------------------------------

export type Gewichtsklasse = 'antweight' | 'beetleweight' | 'featherweight'
export type RobotStatus = 'in_ontwikkeling' | 'gereed' | 'battle_ready'

export interface Robot {
  id: number
  naam: string
  gewichtsklasse: Gewichtsklasse
  gewichtsklasse_label: string
  beschrijving: string | null
  status: RobotStatus
  status_label: string
  team: Team | null
  foto: Media | null
  bijlagen: Media[]
}

// ---------------------------------------------------------------------------
// Post
// ---------------------------------------------------------------------------

export interface Post {
  id: number
  slug: string
  titel: string
  excerpt: string | null
  content: string
  content_format: 'html' | 'markdown'
  categorie: string | null
  tags: string[] | null
  is_published: boolean
  published_at: string | null
  featured: Media | null
  gallery: Media[]
  bijlagen: Media[]
}

// ---------------------------------------------------------------------------
// Page
// ---------------------------------------------------------------------------

export interface Page {
  id: number
  slug: string
  titel: string
  content: string
  content_format: 'html' | 'markdown'
  seo: Record<string, string> | null
  published_at: string | null
  hero: Media | null
}

// ---------------------------------------------------------------------------
// Link
// ---------------------------------------------------------------------------

export type LinkCategorie =
  | 'wallie'
  | 'community'
  | 'competitie'
  | 'tools'
  | 'onderdelen'
  | 'documentatie'

export interface Link {
  id: number
  titel: string
  url: string
  beschrijving: string | null
  categorie: LinkCategorie
  categorie_label: string
  eigenaar: string | null
  og_image: string | null
  favicon: string | null
  verified_at: string | null
}

// ---------------------------------------------------------------------------
// API response wrappers
// ---------------------------------------------------------------------------

export interface PaginatedResponse<T> {
  data: T[]
  links: {
    first: string | null
    last: string | null
    prev: string | null
    next: string | null
  }
  meta: {
    current_page: number
    from: number | null
    last_page: number
    per_page: number
    to: number | null
    total: number
  }
}

// ---------------------------------------------------------------------------
// Registration form
// ---------------------------------------------------------------------------

export interface RegistratiePayload {
  naam: string
  contactpersoon: string
  email: string
  volwassenen: number
  kinderen?: number
}
