/**
 * Roboktober API client.
 *
 * Centralised Axios instance with base URL and default headers.
 * All API calls go through this module — no bare fetch() calls elsewhere.
 *
 * @see PLAN.md §5.3 — API communication
 */

import type {
  AdminAuditLog,
  AdminDashboardSummary,
  AdminPage,
  AdminPageContentUpdatePayload,
  AdminPost,
  AdminPostContentUpdatePayload,
  AdminTeam,
  AdminTeamUpdate,
  AdminTeamUpdateContentUpdatePayload,
  AdminUser,
  AuthResponse,
  AuthUser,
  Edition,
  ForgotPasswordPayload,
  Link,
  LoginPayload,
  Page,
  PaginatedResponse,
  Post,
  RegisterPayload,
  ResetPasswordPayload,
  RichMediaItem,
  RichMediaUploadPayload,
  RegistratiePayload,
  TeamUpdate,
  TeamMembership,
  TeamUpdatePayload,
  UpdateTeamUpdatePayload,
  Team,
  TeamRegistratie,
  UpdateAccountPayload,
  UpdatePasswordPayload,
  UpdateRegistratiePayload,
} from '@/types/api'
import axios from 'axios'

const api = axios.create({
  baseURL: '/api/v1',
  headers: {
    Accept: 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
  withCredentials: false,
})

const storedToken = localStorage.getItem('auth_token')

if (storedToken) {
  api.defaults.headers.common.Authorization = `Bearer ${storedToken}`
}

export function setAuthToken(token: string | null): void {
  if (typeof token === 'string' && token !== '') {
    localStorage.setItem('auth_token', token)
    api.defaults.headers.common.Authorization = `Bearer ${token}`
    return
  }

  localStorage.removeItem('auth_token')
  delete api.defaults.headers.common.Authorization
}

// ---------------------------------------------------------------------------
// Posts
// ---------------------------------------------------------------------------

export async function getPosts(params?: {
  per_page?: number
  categorie?: string
  tag?: string
  page?: number
}): Promise<PaginatedResponse<Post>> {
  const { data } = await api.get<PaginatedResponse<Post>>('/posts', { params })
  return data
}

export async function getPost(slug: string): Promise<Post> {
  const { data } = await api.get<{ data: Post }>(`/posts/${slug}`)
  return data.data
}

// ---------------------------------------------------------------------------
// Teams
// ---------------------------------------------------------------------------

export async function getTeams(): Promise<Team[]> {
  const { data } = await api.get<{ data: Team[] }>('/teams')
  return data.data
}

export async function getTeam(id: number): Promise<Team> {
  const { data } = await api.get<{ data: Team }>(`/teams/${id}`)
  return data.data
}

export async function applyForTeamMembership(teamId: number, requestMessage?: string): Promise<TeamMembership> {
  const { data } = await api.post<{ data: TeamMembership }>(`/teams/${teamId}/membership-requests`, {
    request_message: requestMessage,
  })

  return data.data
}

export async function getMijnTeamMemberships(): Promise<TeamMembership[]> {
  const { data } = await api.get<{ data: TeamMembership[] }>('/teams/mijn/lidmaatschappen')
  return data.data
}

export async function getCaptainTeamMembershipRequests(): Promise<TeamMembership[]> {
  const { data } = await api.get<{ data: TeamMembership[] }>('/teams/mijn/membership-requests')
  return data.data
}

export async function reviewCaptainTeamMembershipRequest(
  id: number,
  status: 'approved' | 'rejected',
): Promise<TeamMembership> {
  const { data } = await api.patch<{ data: TeamMembership }>(`/teams/mijn/membership-requests/${id}`, { status })
  return data.data
}

// ---------------------------------------------------------------------------
// Links
// ---------------------------------------------------------------------------

export async function getLinks(params?: { categorie?: string }): Promise<Link[]> {
  const { data } = await api.get<{ data: Link[] }>('/links', { params })
  return data.data
}

// ---------------------------------------------------------------------------
// Editions
// ---------------------------------------------------------------------------

export async function getEditions(): Promise<Edition[]> {
  const { data } = await api.get<{ data: Edition[] }>('/edities')
  return data.data
}

// ---------------------------------------------------------------------------
// Pages
// ---------------------------------------------------------------------------

export async function getPage(slug: string): Promise<Page> {
  const { data } = await api.get<{ data: Page }>(`/pages/${slug}`)
  return data.data
}

// ---------------------------------------------------------------------------
// Registratie
// ---------------------------------------------------------------------------

export async function registreerTeam(payload: RegistratiePayload): Promise<Team> {
  const formData = new FormData()
  const volwassenen = Number(payload.volwassenen)
  const kinderen = Number(payload.kinderen)

  formData.append('edition_id', String(payload.edition_id))
  formData.append('naam', payload.naam)
  formData.append('contactpersoon', payload.contactpersoon)
  formData.append('email', payload.email)
  formData.append('volwassenen', String(Number.isFinite(volwassenen) ? volwassenen : 0))

  if (Number.isFinite(kinderen)) {
    formData.append('kinderen', String(kinderen))
  }

  if (payload.opmerkingen) {
    formData.append('opmerkingen', payload.opmerkingen)
  }

  payload.robots.forEach((robot, index) => {
    formData.append(`robots[${index}][naam]`, robot.naam)
    formData.append(`robots[${index}][gewichtsklasse]`, robot.gewichtsklasse)

    if (robot.beschrijving) {
      formData.append(`robots[${index}][beschrijving]`, robot.beschrijving)
    }
  })

  if (payload.teamfoto instanceof File) {
    formData.append('teamfoto', payload.teamfoto)
  }

  const { data } = await api.post<{ data: Team }>('/registratie', formData)

  return data.data
}

export async function getMijnRegistratie(): Promise<TeamRegistratie> {
  const { data } = await api.get<{ data: TeamRegistratie }>('/registratie/mijn')

  return data.data
}

export async function updateMijnRegistratie(payload: UpdateRegistratiePayload): Promise<TeamRegistratie> {
  const formData = new FormData()
  const volwassenen = Number(payload.volwassenen)
  const kinderen = Number(payload.kinderen)

  formData.append('edition_id', String(payload.edition_id))
  formData.append('naam', payload.naam)
  formData.append('contactpersoon', payload.contactpersoon)
  formData.append('email', payload.email)
  formData.append('volwassenen', String(Number.isFinite(volwassenen) ? volwassenen : 0))
  formData.append('teamfoto_verwijderen', payload.teamfoto_verwijderen ? '1' : '0')

  if (Number.isFinite(kinderen)) {
    formData.append('kinderen', String(kinderen))
  }

  if (payload.opmerkingen) {
    formData.append('opmerkingen', payload.opmerkingen)
  }

  payload.robots.forEach((robot, index) => {
    if (typeof robot.id === 'number') {
      formData.append(`robots[${index}][id]`, String(robot.id))
    }

    formData.append(`robots[${index}][naam]`, robot.naam)
    formData.append(`robots[${index}][gewichtsklasse]`, robot.gewichtsklasse)

    if (robot.beschrijving) {
      formData.append(`robots[${index}][beschrijving]`, robot.beschrijving)
    }
  })

  if (payload.teamfoto instanceof File) {
    formData.append('teamfoto', payload.teamfoto)
  }

  formData.append('_method', 'PUT')

  const { data } = await api.post<{ data: TeamRegistratie }>('/registratie/mijn', formData)

  return data.data
}

export async function getMijnRegistratieUpdates(): Promise<TeamUpdate[]> {
  const { data } = await api.get<{ data: TeamUpdate[] }>('/registratie/mijn/updates')

  return data.data
}

export async function createMijnRegistratieUpdate(payload: TeamUpdatePayload): Promise<TeamUpdate> {
  const formData = new FormData()

  formData.append('titel', payload.titel)
  formData.append('content', payload.content)
  formData.append('content_format', payload.content_format)

  if (payload.excerpt) {
    formData.append('excerpt', payload.excerpt)
  }

  payload.afbeeldingen?.forEach((bestand, index) => {
    formData.append(`afbeeldingen[${index}]`, bestand)
  })

  const { data } = await api.post<{ data: TeamUpdate }>('/registratie/mijn/updates', formData)

  return data.data
}

export async function updateMijnRegistratieUpdate(id: number, payload: UpdateTeamUpdatePayload): Promise<TeamUpdate> {
  const formData = new FormData()

  formData.append('titel', payload.titel)
  formData.append('content', payload.content)
  formData.append('content_format', payload.content_format)

  if (payload.excerpt) {
    formData.append('excerpt', payload.excerpt)
  }

  payload.afbeeldingen?.forEach((bestand, index) => {
    formData.append(`afbeeldingen[${index}]`, bestand)
  })

  payload.verwijder_afbeelding_ids?.forEach((mediaId, index) => {
    formData.append(`verwijder_afbeelding_ids[${index}]`, String(mediaId))
  })

  const { data } = await api.patch<{ data: TeamUpdate }>(`/registratie/mijn/updates/${id}`, formData)

  return data.data
}

export default api

// ---------------------------------------------------------------------------
// Auth
// ---------------------------------------------------------------------------

export async function registerUser(payload: RegisterPayload): Promise<AuthResponse> {
  const { data } = await api.post<AuthResponse>('/auth/register', payload)
  return data
}

export async function loginUser(payload: LoginPayload): Promise<AuthResponse> {
  const { data } = await api.post<AuthResponse>('/auth/login', payload)
  return data
}

export async function getCurrentUser(): Promise<AuthUser> {
  const { data } = await api.get<{ data: AuthUser }>('/auth/me')
  return data.data
}

export async function logoutUser(): Promise<void> {
  await api.post('/auth/logout')
}

export async function updateAccount(payload: UpdateAccountPayload): Promise<AuthUser> {
  const { data } = await api.patch<{ data: AuthUser }>('/auth/account', payload)
  return data.data
}

export async function updatePassword(payload: UpdatePasswordPayload): Promise<void> {
  await api.patch('/auth/password', payload)
}

export async function requestPasswordReset(payload: ForgotPasswordPayload): Promise<void> {
  await api.post('/auth/forgot-password', payload)
}

export async function resetPassword(payload: ResetPasswordPayload): Promise<void> {
  await api.post('/auth/reset-password', payload)
}

// ---------------------------------------------------------------------------
// Admin teams (API-only moderation)
// ---------------------------------------------------------------------------

export async function getAdminTeams(params?: {
  status?: 'pending' | 'approved' | 'rejected'
  q?: string
  page?: number
}): Promise<PaginatedResponse<AdminTeam>> {
  const { data } = await api.get<PaginatedResponse<AdminTeam>>('/admin/teams', { params })
  return data
}

export async function getAdminTeam(id: number): Promise<AdminTeam> {
  const { data } = await api.get<{ data: AdminTeam }>(`/admin/teams/${id}`)
  return data.data
}

export async function updateAdminTeamStatus(
  id: number,
  payload: { status: 'pending' | 'approved' | 'rejected'; opmerkingen?: string },
): Promise<AdminTeam> {
  const { data } = await api.patch<{ data: AdminTeam }>(`/admin/teams/${id}/status`, payload)
  return data.data
}

export async function getAdminPosts(params?: {
  status?: 'published' | 'draft'
  q?: string
  page?: number
}): Promise<PaginatedResponse<AdminPost>> {
  const { data } = await api.get<PaginatedResponse<AdminPost>>('/admin/posts', { params })
  return data
}

export async function getAdminPost(id: number): Promise<AdminPost> {
  const { data } = await api.get<{ data: AdminPost }>(`/admin/posts/${id}`)
  return data.data
}

export async function updateAdminPostStatus(
  id: number,
  payload: { is_published: boolean; published_at?: string | null },
): Promise<AdminPost> {
  const { data } = await api.patch<{ data: AdminPost }>(`/admin/posts/${id}/status`, payload)
  return data.data
}

export async function updateAdminPostContent(
  id: number,
  payload: AdminPostContentUpdatePayload,
): Promise<AdminPost> {
  const { data } = await api.patch<{ data: AdminPost }>(`/admin/posts/${id}/content`, payload)
  return data.data
}

export async function getAdminPages(params?: {
  status?: 'published' | 'draft'
  q?: string
  page?: number
}): Promise<PaginatedResponse<AdminPage>> {
  const { data } = await api.get<PaginatedResponse<AdminPage>>('/admin/pages', { params })
  return data
}

export async function updateAdminPageStatus(
  id: number,
  payload: { is_published: boolean; published_at?: string | null },
): Promise<AdminPage> {
  const { data } = await api.patch<{ data: AdminPage }>(`/admin/pages/${id}/status`, payload)
  return data.data
}

export async function getAdminPage(id: number): Promise<AdminPage> {
  const { data } = await api.get<{ data: AdminPage }>(`/admin/pages/${id}`)
  return data.data
}

export async function updateAdminPageContent(
  id: number,
  payload: AdminPageContentUpdatePayload,
): Promise<AdminPage> {
  const { data } = await api.patch<{ data: AdminPage }>(`/admin/pages/${id}/content`, payload)
  return data.data
}

export async function getAdminTeamUpdates(params?: {
  status?: 'published' | 'draft'
  q?: string
  page?: number
}): Promise<PaginatedResponse<AdminTeamUpdate>> {
  const { data } = await api.get<PaginatedResponse<AdminTeamUpdate>>('/admin/team-updates', { params })
  return data
}

export async function updateAdminTeamUpdateStatus(
  id: number,
  payload: { is_published: boolean; published_at?: string | null },
): Promise<AdminTeamUpdate> {
  const { data } = await api.patch<{ data: AdminTeamUpdate }>(`/admin/team-updates/${id}/status`, payload)
  return data.data
}

export async function getAdminTeamUpdate(id: number): Promise<AdminTeamUpdate> {
  const { data } = await api.get<{ data: AdminTeamUpdate }>(`/admin/team-updates/${id}`)
  return data.data
}

export async function updateAdminTeamUpdateContent(
  id: number,
  payload: AdminTeamUpdateContentUpdatePayload,
): Promise<AdminTeamUpdate> {
  const { data } = await api.patch<{ data: AdminTeamUpdate }>(`/admin/team-updates/${id}/content`, payload)
  return data.data
}

export async function getAdminUsers(params?: {
  q?: string
  page?: number
}): Promise<PaginatedResponse<AdminUser>> {
  const { data } = await api.get<PaginatedResponse<AdminUser>>('/admin/users', { params })
  return data
}

export async function updateAdminUserRole(
  id: number,
  payload: { role: 'visitor' | 'teamcaptain' | 'moderator' | 'admin' },
): Promise<AdminUser> {
  const { data } = await api.patch<{ data: AdminUser }>(`/admin/users/${id}/role`, payload)
  return data.data
}

export async function getAdminAuditLogs(params?: {
  action?: string
  actor_user_id?: number
  subject_type?: string
  page?: number
}): Promise<PaginatedResponse<AdminAuditLog>> {
  const { data } = await api.get<PaginatedResponse<AdminAuditLog>>('/admin/audit-logs', { params })
  return data
}

export async function getAdminDashboardSummary(): Promise<AdminDashboardSummary> {
  const { data } = await api.get<{ data: AdminDashboardSummary }>('/admin/dashboard-summary')
  return data.data
}

export async function getRichMediaLibrary(params?: {
  q?: string
  page?: number
}): Promise<PaginatedResponse<RichMediaItem>> {
  const { data } = await api.get<PaginatedResponse<RichMediaItem>>('/media', { params })
  return data
}

export async function uploadRichMedia(payload: RichMediaUploadPayload): Promise<{
  data: RichMediaItem
  attached_to: { type: string; id: number; collectie: string } | null
}> {
  const formData = new FormData()

  formData.append('bestand', payload.bestand)

  if (payload.naam) formData.append('naam', payload.naam)
  if (payload.target_type) formData.append('target_type', payload.target_type)
  if (typeof payload.target_id === 'number') formData.append('target_id', String(payload.target_id))
  if (payload.collectie) formData.append('collectie', payload.collectie)
  if (payload.alt_tekst) formData.append('alt_tekst', payload.alt_tekst)
  if (payload.onderschrift) formData.append('onderschrift', payload.onderschrift)
  if (typeof payload.volgorde === 'number') formData.append('volgorde', String(payload.volgorde))

  const { data } = await api.post<{
    data: RichMediaItem
    attached_to: { type: string; id: number; collectie: string } | null
  }>('/media/upload', formData)

  return data
}
