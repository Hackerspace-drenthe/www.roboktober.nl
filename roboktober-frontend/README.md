# Roboktober Frontend

Vue 3 + Vite frontend for Roboktober.

This app provides the public site pages and consumes the Laravel API.

## Current status

- Router includes all planned public pages, including dynamic page and 404 routes.
- API integration uses Axios through a single client module.
- During development, `/api` calls are proxied to `http://localhost:8000`.
- Production build output is written to `../roboktober-api/public/app`.
- Account-first routes are active for registration edit/account/password flows.
- Team detail includes lidmaatschapsaanvraag voor ingelogde gebruikers.
- Dependency audit status:
  - `npm audit --omit=dev`: no known vulnerabilities.

## Requirements

- Node.js 22.18+ (or 24.12+)
- npm 10+

## Installation (local)

1. Go to the frontend folder:

```bash
cd roboktober-frontend
```

2. Install dependencies:

```bash
npm install
```

3. Start development server:

```bash
npm run dev
```

Default local URL is shown by Vite (usually `http://localhost:5173`).

## Backend connection

- The frontend API base path is `/api/v1`.
- Vite dev server proxies `/api` to `http://localhost:8000`.
- Optional: set `VITE_API_PROXY_TARGET` when backend runs on another port (e.g. `http://127.0.0.1:8010`).
- Make sure the backend is running in `roboktober-api`:

```bash
php artisan serve
```

## Build and deploy flow

To produce the deployable frontend bundle into the backend public folder:

```bash
npm run build
```

This writes files to:
- `../roboktober-api/public/app`

## Useful commands

- Lint (Oxlint + ESLint with auto-fix):

```bash
npm run lint
```

- Type checking:

```bash
npm run type-check
```

- Preview production build:

```bash
npm run preview
```

- Format source files:

```bash
npm run format
```

## Notes for contributors

- Keep all HTTP calls in `src/api/index.ts`.
- Reuse shared types in `src/types` for API contracts.
- Keep route-level code split via lazy-loaded views in `src/router/index.ts`.
- Auth-required routes use `requiresAuth` in router meta and depend on `useAuth` initialization.
