import { fileURLToPath, URL } from 'node:url'

import tailwindcss from '@tailwindcss/vite'
import vue from '@vitejs/plugin-vue'
import { defineConfig } from 'vite'
import vueDevTools from 'vite-plugin-vue-devtools'

// https://vite.dev/config/
// @see PLAN.md §5.3 — frontend setup, API communication
export default defineConfig({
  plugins: [vue(), vueDevTools(), tailwindcss()],

  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url)),
    },
  },

  server: {
    // Proxy /api calls to Laravel during local development
    proxy: {
      '/api': {
        target: 'http://localhost:8000',
        changeOrigin: true,
      },
    },
  },

  build: {
    // Output to Laravel public/app/ — assets worden geserveerd op /app/assets/
    outDir: '../roboktober-api/public/app',
    emptyOutDir: true,
  },

  // Zorgt dat asset-paden in de gebouwde HTML beginnen met /app/
  base: '/app/',
})
