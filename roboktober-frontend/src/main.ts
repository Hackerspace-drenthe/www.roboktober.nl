import './assets/main.css'

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import { trackPageVisit } from './api'

router.afterEach((to) => {
	const path = to.path

	if (path.startsWith('/admin') || path.startsWith('/forbidden')) {
		return
	}

	void trackPageVisit(path).catch(() => {
		// Visitor tracking is best-effort and should never break UX.
	})
})

const app = createApp(App)

app.use(router)

app.mount('#app')
