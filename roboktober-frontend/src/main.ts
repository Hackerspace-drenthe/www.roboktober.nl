import './assets/main.css'

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import { useAnalytics } from './composables/useAnalytics'

const CHUNK_RELOAD_KEY = 'roboktober:chunk-reload-target'

function isChunkLoadError(error: unknown): boolean {
	const message = error instanceof Error ? error.message : String(error)

	return /Failed to fetch dynamically imported module|Importing a module script failed|Loading chunk\s+\d+\s+failed|error loading dynamically imported module/i.test(message)
}

const analytics = useAnalytics()

analytics.init()

router.onError((error, to) => {
	if (!isChunkLoadError(error)) {
		return
	}

	const target = to?.fullPath ?? `${window.location.pathname}${window.location.search}${window.location.hash}`
	const previousTarget = sessionStorage.getItem(CHUNK_RELOAD_KEY)

	// Reload once per route to recover from stale hashed chunks after deploy.
	if (previousTarget === target) {
		sessionStorage.removeItem(CHUNK_RELOAD_KEY)
		return
	}

	sessionStorage.setItem(CHUNK_RELOAD_KEY, target)
	window.location.assign(target)
})

router.afterEach((to, from) => {
	sessionStorage.removeItem(CHUNK_RELOAD_KEY)

	const path = to.path

	if (path.startsWith('/admin') || path.startsWith('/forbidden')) {
		return
	}

	void analytics.track('page_view', {
		pagePath: path,
		routeName: typeof to.name === 'string' ? to.name : undefined,
		referrerPath: from.path,
		payload: {
			full_path: to.fullPath,
		},
	}).catch(() => {
		// Visitor tracking is best-effort and should never break UX.
	})
})

const app = createApp(App)

app.use(router)

app.mount('#app')
