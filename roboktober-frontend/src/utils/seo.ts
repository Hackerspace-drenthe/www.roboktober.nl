type OgType = 'website' | 'article'

export type SeoMetaInput = {
  title: string
  description?: string | null
  canonicalPath?: string | null
  robots?: string | null
  ogType?: OgType
  image?: string | null
}

const SITE_NAME = 'Roboktober'
const PROD_CANONICAL_ORIGIN = 'https://www.roboktober.nl'
const DEFAULT_DESCRIPTION =
  'Roboktober is het combat robotics evenement van Hackerspace Drenthe. Bouw je eigen robot, leer techniek en beleef de arena.'
const DEFAULT_IMAGE = `${PROD_CANONICAL_ORIGIN}/app/favicon.ico`

function getSeoOrigin(): string {
  if (typeof window === 'undefined') {
    return PROD_CANONICAL_ORIGIN
  }

  return import.meta.env.PROD ? PROD_CANONICAL_ORIGIN : window.location.origin
}

function normalizeCanonicalPath(path: string | null | undefined): string {
  const raw = (path ?? '/').trim()
  const withLeadingSlash = raw.startsWith('/') ? raw : `/${raw}`

  if (withLeadingSlash === '/app' || withLeadingSlash === '/app/') {
    return '/'
  }

  if (withLeadingSlash.startsWith('/app/')) {
    return withLeadingSlash.slice(4)
  }

  return withLeadingSlash
}

function absoluteUrl(url: string | null | undefined): string {
  if (!url) {
    return DEFAULT_IMAGE
  }

  if (/^https?:\/\//i.test(url)) {
    return url
  }

  return `${getSeoOrigin()}${url.startsWith('/') ? '' : '/'}${url}`
}

function upsertMetaTag(attribute: 'name' | 'property', key: string, content: string): void {
  const selector = `meta[${attribute}="${key}"]`
  let tag = document.head.querySelector(selector) as HTMLMetaElement | null

  if (!tag) {
    tag = document.createElement('meta')
    tag.setAttribute(attribute, key)
    document.head.appendChild(tag)
  }

  tag.setAttribute('content', content)
}

function upsertCanonical(url: string): void {
  let tag = document.head.querySelector('link[rel="canonical"]') as HTMLLinkElement | null

  if (!tag) {
    tag = document.createElement('link')
    tag.setAttribute('rel', 'canonical')
    document.head.appendChild(tag)
  }

  tag.setAttribute('href', url)
}

export function applySeoMeta(input: SeoMetaInput): void {
  if (typeof document === 'undefined') {
    return
  }

  const title = input.title.includes(SITE_NAME) ? input.title : `${input.title} — ${SITE_NAME}`
  const description = input.description?.trim() || DEFAULT_DESCRIPTION
  const canonicalPath = normalizeCanonicalPath(input.canonicalPath)
  const canonicalUrl = `${getSeoOrigin()}${canonicalPath}`
  const ogType = input.ogType ?? 'website'
  const robots = input.robots ?? 'index,follow'
  const image = absoluteUrl(input.image)

  document.title = title

  upsertMetaTag('name', 'description', description)
  upsertMetaTag('name', 'robots', robots)
  upsertMetaTag('property', 'og:title', title)
  upsertMetaTag('property', 'og:description', description)
  upsertMetaTag('property', 'og:url', canonicalUrl)
  upsertMetaTag('property', 'og:type', ogType)
  upsertMetaTag('property', 'og:image', image)
  upsertMetaTag('name', 'twitter:card', 'summary_large_image')
  upsertMetaTag('name', 'twitter:title', title)
  upsertMetaTag('name', 'twitter:description', description)
  upsertMetaTag('name', 'twitter:image', image)
  upsertCanonical(canonicalUrl)
}

export function upsertJsonLd(id: string, payload: unknown): void {
  if (typeof document === 'undefined') {
    return
  }

  const selector = `script[type="application/ld+json"][data-id="${id}"]`
  let script = document.head.querySelector(selector) as HTMLScriptElement | null

  if (!script) {
    script = document.createElement('script')
    script.type = 'application/ld+json'
    script.setAttribute('data-id', id)
    document.head.appendChild(script)
  }

  script.textContent = JSON.stringify(payload)
}

export function removeJsonLd(id: string): void {
  if (typeof document === 'undefined') {
    return
  }

  const selector = `script[type="application/ld+json"][data-id="${id}"]`
  document.head.querySelector(selector)?.remove()
}
