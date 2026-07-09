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
  beschrijving: string | null
  edition: Edition | null
  status: TeamStatus
  status_label: string
  captain: {
    naam: string
    foto?: Media | null
  }
  leden: {
    volwassenen: number
    kinderen: number
    totaal: number
  }
  foto: Media | null
  captain_foto: Media | null
  leden_fotos: Media[]
  robots: Robot[]
  updates?: TeamUpdate[]
}

export interface TeamUpdate {
  id: number
  titel: string
  excerpt: string | null
  content: string
  content_format: 'html' | 'markdown'
  published_at: string | null
  afbeeldingen: Media[]
}

// ---------------------------------------------------------------------------
// Edition
// ---------------------------------------------------------------------------

export interface Edition {
  id: number
  naam: string
  omschrijving: string | null
  locatie: string
  afbeelding_url: string | null
  start_at: string
  end_at: string | null
  is_done: boolean
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
  awesomeness_score: number
  awesomeness_votes_count: number
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

export type RichMediaTargetType = 'post' | 'page' | 'team' | 'team_update' | 'robot' | 'user'

export interface RichMediaItem {
  id: number
  naam: string
  url: string
  mime_type: string
  extensie: string
  grootte: number
  downloads: number
  created_at: string | null
  html_snippet: string
  markdown_snippet: string
}

export interface RichMediaUploadPayload {
  bestand: File
  naam?: string
  target_type?: RichMediaTargetType
  target_id?: number
  collectie?: 'featured' | 'gallery' | 'bijlagen' | 'hero' | 'foto' | 'default'
  alt_tekst?: string
  onderschrift?: string
  volgorde?: number
}

// ---------------------------------------------------------------------------
// Registration form
// ---------------------------------------------------------------------------

export interface RegistratiePayload {
  edition_id: number
  naam: string
  contactpersoon: string
  email: string
  volwassenen: number
  kinderen?: number
  opmerkingen?: string
  teamfoto?: File | null
  robots: RegistratieRobotPayload[]
}

export interface RegistratieRobotPayload {
  naam: string
  gewichtsklasse: Gewichtsklasse
  beschrijving?: string
}

export interface TeamRegistratie {
  id: number
  edition_id: number | null
  naam: string
  contactpersoon: string
  email: string
  volwassenen: number
  kinderen: number | null
  opmerkingen: string | null
  status: TeamStatus
  status_label: string
  foto: Media | null
  robots: TeamRegistratieRobot[]
}

export interface TeamRegistratieRobot {
  id: number
  naam: string
  gewichtsklasse: Gewichtsklasse
  gewichtsklasse_label: string
  beschrijving: string | null
  foto: Media | null
}

export interface UpdateRegistratiePayload {
  edition_id: number
  naam: string
  contactpersoon: string
  email: string
  volwassenen: number
  kinderen?: number
  opmerkingen?: string
  teamfoto?: File | null
  teamfoto_verwijderen?: boolean
  robots: UpdateRegistratieRobotPayload[]
}

export interface UpdateRegistratieRobotPayload extends RegistratieRobotPayload {
  id?: number
}

export interface TeamUpdatePayload {
  titel: string
  excerpt?: string
  content: string
  content_format: 'html' | 'markdown'
  afbeeldingen?: File[]
}

export interface UpdateTeamUpdatePayload extends TeamUpdatePayload {
  verwijder_afbeelding_ids?: number[]
}

export type TeamMembershipStatus = 'pending' | 'approved' | 'rejected'

export interface TeamMembership {
  id: number
  status: TeamMembershipStatus
  status_label: string
  request_message: string | null
  team: {
    id: number
    naam: string
  } | null
  user: {
    id: number
    name: string
    email: string
  } | null
  reviewed_at: string | null
  created_at: string | null
  updated_at: string | null
}

// ---------------------------------------------------------------------------
// Auth & Admin
// ---------------------------------------------------------------------------

export type UserRole = 'visitor' | 'teamcaptain' | 'moderator' | 'admin'

export interface AuthUser {
  id: number
  name: string
  email: string
  role: UserRole
  role_label: string
  profile_photo: Media | null
}

export interface AuthResponse {
  data: AuthUser
  token: string
  token_type: 'Bearer'
}

export interface RegisterPayload {
  name: string
  email: string
  password: string
  password_confirmation: string
}

export interface LoginPayload {
  email: string
  password: string
  device_name?: string
}

export interface UpdateAccountPayload {
  name: string
  email: string
}

export interface UpdatePasswordPayload {
  current_password: string
  password: string
  password_confirmation: string
}

export interface ForgotPasswordPayload {
  email: string
}

export interface ResetPasswordPayload {
  email: string
  token: string
  password: string
  password_confirmation: string
}

export interface AdminTeam {
  id: number
  naam: string
  contactpersoon: string
  email: string
  edition_id: number | null
  edition: Edition | null
  volwassenen: number
  kinderen: number | null
  status: TeamStatus
  status_label: string
  opmerkingen: string | null
  captain_user_id: number | null
  captain: AuthUser | null
  foto: Media | null
  robots: Robot[]
  created_at: string | null
  updated_at: string | null
}

export interface AdminPost {
  id: number
  slug: string
  titel: string
  excerpt: string | null
  content: string
  content_format: 'html' | 'markdown'
  categorie: string | null
  tags: string[]
  is_published: boolean
  published_at: string | null
  created_at: string | null
  updated_at: string | null
}

export interface AdminPostContentUpdatePayload {
  titel: string
  excerpt?: string | null
  content: string
  content_format: 'html' | 'markdown'
  categorie?: string | null
  tags?: string[]
}

export interface AdminPage {
  id: number
  slug: string
  titel: string
  content: string
  content_format: 'html' | 'markdown'
  seo: Record<string, unknown> | null
  is_published: boolean
  published_at: string | null
  created_at: string | null
  updated_at: string | null
}

export interface AdminPageContentUpdatePayload {
  titel: string
  content: string
  content_format: 'html' | 'markdown'
  seo?: Record<string, unknown> | null
}

export interface AdminTeamUpdate {
  id: number
  team_id: number
  team: { id: number | null; naam: string | null } | null
  titel: string
  excerpt: string | null
  content: string
  content_format: 'html' | 'markdown'
  is_published: boolean
  published_at: string | null
  created_at: string | null
  updated_at: string | null
}

export interface AdminTeamUpdateContentUpdatePayload {
  titel: string
  excerpt?: string | null
  content: string
  content_format: 'html' | 'markdown'
}

export interface AdminUser {
  id: number
  name: string
  email: string
  role: UserRole
  role_label: string
  created_at: string | null
  updated_at: string | null
}

export interface AdminAuditLog {
  id: number
  actor_user_id: number
  actor: {
    id: number | null
    name: string | null
    email: string | null
    role: UserRole | null
  } | null
  action: string
  subject_type: string
  subject_id: number
  before: Record<string, unknown> | null
  after: Record<string, unknown> | null
  context: Record<string, unknown> | null
  created_at: string | null
}

export interface AdminDashboardSummary {
  stats: {
    pending_teams: number
    draft_posts: number
    draft_pages: number
    draft_team_updates: number
  }
  pending_teams: Array<{
    id: number
    naam: string
    contactpersoon: string
    created_at: string | null
  }>
  recent_activity: Array<{
    id: number
    action: string
    subject_type: string
    subject_id: number
    actor: {
      id: number | null
      name: string | null
      email: string | null
    }
    created_at: string | null
  }>
}
