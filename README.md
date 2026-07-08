# Roboktober Monorepo

Dit is de centrale repository voor de Roboktober website en API van Hackerspace Drenthe.

Roboktober is een oktober-evenement rond combat robots, met focus op bouwen, leren en community.

## Projectdoelen (uit PLAN.md)

1. Mensen warm maken voor het event met teaser-content, planning en sfeer.
2. Deelnemers helpen bouwen met complete en praktische bouwinformatie.
3. Assets duurzaam beschikbaar houden, zoals BOM, STL, bouwdocs, blog en links.
4. Een herbruikbare basis neerzetten voor toekomstige Roboktober-edities.

## Context

- Eerste editie: 2026.
- Publieke domeinnaam: https://roboktober.nl.
- Website-taal: Nederlands.
- Teamregistratie: account-first (ingelogde gebruiker maakt/beheert eigen team).

## Architectuur

De stack is API-first headless:

- Backend: Laravel API + Filament admin
- Frontend: Vue 3 + Vite
- Data-uitwisseling: JSON REST API

Deze scheiding maakt het mogelijk om frontend en backend onafhankelijk te ontwikkelen en deployen.

## Repository-structuur

- roboktober-api: Laravel backend, API en admin panel
- roboktober-frontend: Vue frontend voor de publieke website
- PLAN.md: functioneel en technisch masterplan
- PLAN-CHANGES.md: aanvullingen en wijzigingen op het plan

## Huidige scope (MVP richting)

- Publieke pagina's voor home, programma, aanmelden, teams, nieuws en build-hub
- Teamregistratie via API (auth vereist)
- Accountbeheer via API (account wijzigen, wachtwoord wijzigen, wachtwoord reset)
- Teamlidmaatschap-flow via API (aanvragen, captain review)
- Nieuws, pagina's en links via API
- Beheer via Filament
- Frontend build naar backend public map

## Installatie en ontwikkeling

Gebruik de project-specifieke handleidingen:

- Zie roboktober-api/README.md voor backend setup, database, tests en security baseline.
- Zie roboktober-frontend/README.md voor frontend setup, dev-server en build-flow.

## Samenhang lokaal

Voor lokale ontwikkeling draai je meestal beide projecten tegelijk:

1. Start de Laravel backend in roboktober-api.
2. Start de Vite dev server in roboktober-frontend.
3. Frontend proxyt /api-verkeer naar de backend.

### Snelle start vanuit de root (1 commando)

Je kunt nu ook beide tegelijk starten vanuit de monorepo-root:

```bash
npm run dev
```

Dit start parallel:
- backend op `http://localhost:8000`
- frontend op `http://localhost:5173/app/`

Let op: dit root-script gebruikt `npx concurrently`, dus internettoegang voor package-resolutie kan nodig zijn bij de eerste run.

## Bron van waarheid

Voor productkeuzes, doelen, informatie-architectuur en functionele eisen blijft PLAN.md de bron van waarheid.

## Deploy op Apache (192.168.1.10)

Er staat nu een kant-en-klare deployset in [deploy/README.md](deploy/README.md):

- [deploy/deploy.sh](deploy/deploy.sh): pull + composer + artisan optimize/migrate
- [deploy/apache/roboktober.conf](deploy/apache/roboktober.conf): Apache vhost template
- [deploy/systemd/roboktober-deploy.service](deploy/systemd/roboktober-deploy.service): handmatige systemd deploy
- [deploy/systemd/roboktober-deploy.timer](deploy/systemd/roboktober-deploy.timer): optionele timer (elke 5 min)

## Deploy op Railway (publieke container)

Wil je snel publiek testen met een container, gebruik Railway met de root-Dockerfile in deze repo.

### Waarom deze setup veilig is

- Deploys gebeuren handmatig via GitHub Actions (`workflow_dispatch`), niet automatisch op elke push.
- Secrets blijven in GitHub/Railway en staan niet in de code.
- Migrations zijn opt-in via `RUN_MIGRATIONS=true`.

### 1. Railway service aanmaken

1. Maak in Railway een nieuw project en koppel deze GitHub-repository.
2. Laat Railway bouwen met de `Dockerfile` op repo-root.
3. Voeg een managed database toe (MySQL/Postgres) of gebruik een externe database.

### 2. Railway environment variables

Zet minimaal deze variabelen:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://<jouw-railway-url>`
- `APP_KEY=<base64:...>`
- `LOG_CHANNEL=stack`
- `LOG_LEVEL=info`
- `DB_CONNECTION=mysql` (of postgres, passend bij je database)
- `DB_HOST=...`
- `DB_PORT=...`
- `DB_DATABASE=...`
- `DB_USERNAME=...`
- `DB_PASSWORD=...`

Optioneel:

- `RUN_MIGRATIONS=true` om bij elke start automatisch `php artisan migrate --force` uit te voeren.

### 3. Deploy hook aanmaken in Railway

1. Open je service in Railway.
2. Maak een Deploy Hook aan.
3. Kopieer de hook URL.

### 4. GitHub secret toevoegen

Voeg in GitHub repository secrets toe:

- `RAILWAY_DEPLOY_HOOK_URL` = de deploy hook URL uit Railway.

### 5. Handmatig deployen via GitHub

1. Ga naar Actions in GitHub.
2. Start workflow `Deploy to Railway (manual)`.
3. Vul eventueel een reden in en run de workflow.

De workflow staat in [`.github/workflows/deploy-railway.yml`](.github/workflows/deploy-railway.yml).

### 6. Eerste productie-checklist

1. Zet `APP_KEY` (vereist).
2. Controleer dat database bereikbaar is vanuit Railway.
3. Draai 1 keer migrations (met `RUN_MIGRATIONS=true` of handmatig in Railway shell).
4. Verifieer:
	- `/` of `/app/` laadt
	- `/api/v1/posts` geeft 200
	- admin login werkt op `/admin`
