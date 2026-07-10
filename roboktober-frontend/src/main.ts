import './assets/main.css'

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import { useAnalytics } from './composables/useAnalytics'

const analytics = useAnalytics()

analytics.init()

router.afterEach((to, from) => {
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
