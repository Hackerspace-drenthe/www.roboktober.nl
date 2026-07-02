/**
 * Roboktober API client.
 *
 * Centralised Axios instance with base URL and default headers.
 * All API calls go through this module — no bare fetch() calls elsewhere.
 *
 * @see PLAN.md §5.3 — API communication
 */

import type {
  Link,
  Page,
  PaginatedResponse,
  Post,
  RegistratiePayload,
  Team,
} from '@/types/api'
import axios from 'axios'

const api = axios.create({
  baseURL: '/api/v1',
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
  withCredentials: false,
})

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

// ---------------------------------------------------------------------------
// Links
// ---------------------------------------------------------------------------

export async function getLinks(params?: { categorie?: string }): Promise<Link[]> {
  const { data } = await api.get<{ data: Link[] }>('/links', { params })
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
  const { data } = await api.post<{ data: Team }>('/registratie', payload)
  return data.data
}

export default api
