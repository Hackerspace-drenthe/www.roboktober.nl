import { sendSessionEndAnalyticsEvent, trackAnalyticsEvent } from '@/api'
import type { AnalyticsEventType } from '@/types/api'

type TrackOptions = {
  eventName?: string
  pagePath?: string
  routeName?: string
  referrerPath?: string
  payload?: Record<string, unknown>
}

const SESSION_ID_KEY = 'analytics_session_id'
const SESSION_STARTED_KEY = 'analytics_session_started'

let initialized = false
let sessionId = ''
let sessionEnded = false

const startedForms = new WeakSet<HTMLFormElement>()

function createSessionId(): string {
  if (typeof crypto !== 'undefined' && typeof crypto.randomUUID === 'function') {
    return crypto.randomUUID().replace(/-/g, '')
  }

  return `${Date.now().toString(36)}${Math.random().toString(36).slice(2, 16)}`
}

function getSessionId(): string {
  if (sessionId !== '') {
    return sessionId
  }

  const stored = sessionStorage.getItem(SESSION_ID_KEY)
  if (stored && stored.length >= 16) {
    sessionId = stored
    return sessionId
  }

  sessionId = createSessionId()
  sessionStorage.setItem(SESSION_ID_KEY, sessionId)
  return sessionId
}

function normalizePath(path?: string): string | undefined {
  if (!path || !path.startsWith('/')) {
    return undefined
  }

  if (path.startsWith('/api')) {
    return undefined
  }

  return path
}

async function track(eventType: AnalyticsEventType, options: TrackOptions = {}): Promise<void> {
  const pagePath = normalizePath(options.pagePath ?? window.location.pathname)
  const referrerPath = normalizePath(options.referrerPath)

  await trackAnalyticsEvent({
    session_id: getSessionId(),
    event_type: eventType,
    event_name: options.eventName,
    page_path: pagePath,
    route_name: options.routeName,
    referrer_path: referrerPath,
    payload: options.payload,
  })
}

function shouldTrackCurrentPath(): boolean {
  const path = window.location.pathname
  return !path.startsWith('/admin') && !path.startsWith('/forbidden')
}

function attachClickTracking(): void {
  document.addEventListener('click', (event) => {
    if (!shouldTrackCurrentPath()) {
      return
    }

    const target = event.target as HTMLElement | null
    const clickable = target?.closest('a, button, [role="button"]') as HTMLElement | null

    if (!clickable) {
      return
    }

    const label = (clickable.textContent ?? '').replace(/\s+/g, ' ').trim().slice(0, 80)

    void track('click', {
      eventName: label || clickable.tagName.toLowerCase(),
      payload: {
        element: clickable.tagName.toLowerCase(),
      },
    }).catch(() => {
      // Analytics events are best-effort and must not affect UX.
    })
  })
}

function attachFormTracking(): void {
  document.addEventListener('focusin', (event) => {
    if (!shouldTrackCurrentPath()) {
      return
    }

    const target = event.target as HTMLElement | null
    const form = target?.closest('form') as HTMLFormElement | null

    if (!form || startedForms.has(form)) {
      return
    }

    startedForms.add(form)

    void track('form_start', {
      eventName: form.getAttribute('name') ?? form.getAttribute('id') ?? 'form',
      payload: {
        form_id: form.getAttribute('id') ?? null,
        form_name: form.getAttribute('name') ?? null,
      },
    }).catch(() => {
      // Analytics events are best-effort and must not affect UX.
    })
  })

  document.addEventListener('submit', (event) => {
    if (!shouldTrackCurrentPath()) {
      return
    }

    const form = event.target as HTMLFormElement | null
    if (!form) {
      return
    }

    void track('form_submit', {
      eventName: form.getAttribute('name') ?? form.getAttribute('id') ?? 'form',
      payload: {
        form_id: form.getAttribute('id') ?? null,
        form_name: form.getAttribute('name') ?? null,
        action: form.getAttribute('action') ?? null,
      },
    }).catch(() => {
      // Analytics events are best-effort and must not affect UX.
    })
  })
}

function sendSessionEnd(): void {
  if (sessionEnded) {
    return
  }

  sessionEnded = true

  void sendSessionEndAnalyticsEvent({
    session_id: getSessionId(),
    event_type: 'session_end',
    page_path: normalizePath(window.location.pathname),
    occurred_at: new Date().toISOString(),
  }).catch(() => {
    // Session-end tracking is best effort.
  })
}

function attachSessionLifecycleTracking(): void {
  const started = sessionStorage.getItem(SESSION_STARTED_KEY)

  if (!started) {
    sessionStorage.setItem(SESSION_STARTED_KEY, '1')

    void track('session_start', {
      pagePath: window.location.pathname,
      payload: {
        referrer: document.referrer || null,
      },
    }).catch(() => {
      // Analytics events are best-effort and must not affect UX.
    })
  }

  window.addEventListener('beforeunload', () => {
    sendSessionEnd()
  })
}

export function useAnalytics() {
  function init(): void {
    if (initialized || typeof window === 'undefined') {
      return
    }

    initialized = true
    getSessionId()
    attachSessionLifecycleTracking()
    attachClickTracking()
    attachFormTracking()
  }

  return {
    init,
    track,
    getSessionId,
  }
}
