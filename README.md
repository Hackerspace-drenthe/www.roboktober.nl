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

Automatische deploy via GitHub Webhook (push naar `master`) staat ook beschreven in [deploy/README.md](deploy/README.md).
