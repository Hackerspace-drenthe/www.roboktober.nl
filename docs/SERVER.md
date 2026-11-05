# Website en server

Dit document beschrijft de Roboktober website en API, lokale ontwikkeling en deployment. Het algemene projectoverzicht staat in de [root-README](../README.md).

## Architectuur

De stack is API-first headless:

- Backend: Laravel API + Filament admin
- Frontend: Vue 3 + Vite
- Data-uitwisseling: JSON REST API

Deze scheiding maakt het mogelijk om frontend en backend onafhankelijk te ontwikkelen en deployen.

Teamregistratie is account-first: een ingelogde gebruiker maakt en beheert een eigen team.

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

- [Backend-handleiding](../roboktober-api/README.md): setup, database, tests en security baseline.
- [Frontend-handleiding](../roboktober-frontend/README.md): setup, dev-server en build-flow.

Voer de onderstaande root-commando's uit vanuit de repository-root, niet vanuit deze documentatiemap.

## Samenhang lokaal

Voor lokale ontwikkeling draai je meestal beide projecten tegelijk:

1. Start de Laravel backend in [roboktober-api](../roboktober-api/).
2. Start de Vite dev server in [roboktober-frontend](../roboktober-frontend/).
3. Frontend proxyt `/api`-verkeer naar de backend.

### Snelle start vanuit de root (1 commando)

Je kunt beide tegelijk starten vanuit de monorepo-root:

```bash
npm run dev
```

Dit start parallel:

- Backend op `http://localhost:8000`
- Frontend op `http://localhost:5173/app/`

Let op: dit root-script gebruikt `npx concurrently`, dus internettoegang voor package-resolutie kan nodig zijn bij de eerste run.

## Docker development (backend + frontend + MariaDB)

Start de volledige lokale stack:

```bash
docker compose up --build
```

Snellere variant voor lokale iteratie (ook met MariaDB):

```bash
docker compose -f docker-compose.fast.yml up -d
```

Deze variant is self-contained:

- De backend-image bevat de benodigde PHP-extensies en Composer.
- Als `vendor/autoload.php` ontbreekt, draait de container automatisch `composer install`.
- Backend `vendor` wordt in een Docker-volume bewaard voor snellere herstarts.

Beide Docker-varianten voeren bij het starten automatisch de migraties uit. Is de database nog leeg, dan draaien ook de seeders om de lokale admin en basiscontent aan te maken. Bij volgende starts blijft bestaande data behouden en worden de seeders overgeslagen.

Draai in de achtergrond:

```bash
docker compose up -d --build
```

Stoppen:

```bash
docker compose down
```

Belangrijke endpoints:

- Backend API: `http://localhost:8000`
- Frontend app: `http://localhost:5173`
- MariaDB: `localhost:13306` (database `roboktober`, user `roboktober`, wachtwoord `roboktober`; uitsluitend voor lokale ontwikkeling)

De containerpoorten blijven intern vast; alleen de hostpoorten zijn configureerbaar:

| Variabele | Standaard | Containerpoort |
| --- | ---: | ---: |
| `MARIADB_HOST_PORT` | `13306` | `3306` |
| `BACKEND_HOST_PORT` | `8000` | `8000` |
| `FRONTEND_HOST_PORT` | `5173` | `5173` |

Bij een lokaal poortconflict kun je een hostpoort per start overschrijven:

```bash
MARIADB_HOST_PORT=23306 BACKEND_HOST_PORT=18000 FRONTEND_HOST_PORT=15173 docker compose up -d --build
```

## Deploy op Apache (192.168.1.10)

Er staat een kant-en-klare deployset in de [deploy-handleiding](../deploy/README.md):

- [deploy.sh](../deploy/deploy.sh): pull + composer + artisan optimize/migrate
- [Apache-configuratie](../deploy/apache/roboktober.conf): Apache vhost template
- [Systemd-service](../deploy/systemd/roboktober-deploy.service): handmatige systemd deploy
- [Systemd-timer](../deploy/systemd/roboktober-deploy.timer): optionele timer (elke 5 min)

Automatische deploy via GitHub Webhook (push naar `master`) staat ook beschreven in de [deploy-handleiding](../deploy/README.md).

## Verdere documentatie

- [Documentatie-index](README.md)
- [Architectuur](ARCHITECTUUR.md)
- [Deploy checklist](DEPLOY-CHECKLIST.md)
