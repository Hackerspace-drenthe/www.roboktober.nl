# Roboktober Frontend

Vue 3 + Vite frontend for Roboktober.

This app provides the public site pages and consumes the Laravel API.

## Current status

**5 November 2026 — confirmed by Rein:** the 2026 project is complete. The website has been operational and stable for several months. Installation and build instructions below are for development, maintenance and recovery.

- Router includes the delivered public pages, including dynamic page and 404 routes.
- API integration uses Axios through a single client module.
- During development, `/api` calls are proxied to `http://localhost:8000`.
- Production build output is written to `../roboktober-api/public/app`.
- Account-first routes are active for registration edit/account/password flows.
- Team detail includes lidmaatschapsaanvraag voor ingelogde gebruikers.
- Dependency audit results are time-sensitive. Run `npm audit --omit=dev` during maintenance; completion is not a fresh dependency audit.

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

### Presentations (direct URLs only)

The Dutch and English PHP Meetup decks live in
[`../presentaties/`](../presentaties/). No navigation/menu link is added.
The production build automatically generates and checks their standalone
resources and full ZIP downloads:

```bash
npm run build
```

For the development server, run `npm run presentations:generate` once before
`npm run dev`, and again after editing the decks. You can run
`npm run presentations:check` separately to verify generated resources and ZIPs.

The generator requires Node, Bash, `zip`, `unzip`, and Python 3 with the
`markdown` module (also required by the presentation package builders).
It builds both full packages from source, including videos, 3D files,
speaker notes and reviewer instructions. It leaves the source decks unchanged.
Generated output in `public/presentaties/` is deliberately not committed
(each ZIP exceeds GitHub's file size limit). The `prebuild-only` hook regenerates
both versions from source, including on a fresh deployment checkout. Vite then
copies this directory to Laravel's `public/app/presentaties/`. A failed package
build or validation stops the website build rather than publishing broken links.

Published production URLs for the completed 2026 edition:

- Overview + downloads: `https://www.roboktober.nl/app/presentaties/php-meetup-groningen-2026-11-05/index.html`
- Dutch: `https://www.roboktober.nl/app/presentaties/php-meetup-groningen-2026-11-05/nl/presentatie/index.html`
- English: `https://www.roboktober.nl/app/presentaties/php-meetup-groningen-2026-11-05/en/presentatie/index.html`
- Full NL ZIP: `https://www.roboktober.nl/app/presentaties/php-meetup-groningen-2026-11-05/roboktober-nl-full.zip`
- Full EN ZIP: `https://www.roboktober.nl/app/presentaties/php-meetup-groningen-2026-11-05/roboktober-en-full.zip`

These are static files, not SPA routes. Keep the explicit `index.html` in
shared links; no router or menu changes are needed. Direct URLs are public,
not access control: speaker notes and packaged sources are available to
anyone with the URL. Rein confirmed the required permissions for this presentation on 4 November 2026. Retain credits and licences; any new or changed media needs its own permission check.

### Event photo and video gallery

The gallery at the bottom of `/nieuws` uses the originals in the repository-root
[fotos/](../fotos/) folder. Add photos to `robots/`, `mensenbezig/` or
`controller/`, and MP4 videos to `video/`. Then run `npm run media:generate`.
This requires FFmpeg with WebP support; ordinary builds do not require FFmpeg.
The script leaves the originals untouched and generates WebP images,
thumbnails, video posters and video copies in `public/media/evenement/`,
plus the reusable catalogue `src/data/event-media.json`.
Commit the generated files with the originals. When removing media, also remove
its generated public files. Only publish material you have permission to share,
including permission from recognizable participants.
The main menu links only to Nieuws. `/galerij` remains available as a standalone
page without a redirect; both pages reuse `src/components/EventGallery.vue`.
The programme page also reuses three generated workshop/robot photos from this
media collection. Its dates, activities and location still come from the API.
The programme page and gallery video section share a kickoff recording card
in `src/components/KickoffRecording.vue`. It links to YouTube without loading
an external video player before a visitor chooses to open it.

### Design gallery by Rein van Hackerspace Drenthe

The 3D print page keeps the original WallieOnline/MakerWorld download and also
shows Rein's designs, grouped by design with newest versions first.
The catalogue is in [src/data/rein-designs.json](src/data/rein-designs.json).
Downloads and static 3D previews are served from
`public/downloads/designs/rein/`, so they work without external model viewers.

After adding or updating a design in [../3dprinter/reinsdesigns](../3dprinter/reinsdesigns),
update the catalogue and run `npm run designs:generate` from this folder.
This requires OpenSCAD and `xvfb-run`; normal builds do not require either tool.
To regenerate only selected designs, pass their catalogue IDs, for example
`npm run designs:generate -- disc-mini disc-wedge`.
Commit the generated downloads and PNG previews along with the catalogue.
Each preview uses the first version's assembly (`previewPart` overrides the part
selector). Preserve original filenames so OpenSCAD `include`/`use` references
continue to work, and list any required companion file as `dependency`.
Disc Mini and Disc Wedge share `disc_geometry.scad`; keep it alongside either
download. The helper is not a separate robot design.
The Mostly Harmless downloads are separate chassis/roof parts, not versions.

- Keep all HTTP calls in `src/api/index.ts`.
- Reuse shared types in `src/types` for API contracts.
- Keep route-level code split via lazy-loaded views in `src/router/index.ts`.
- Auth-required routes use `requiresAuth` in router meta and depend on `useAuth` initialization.
