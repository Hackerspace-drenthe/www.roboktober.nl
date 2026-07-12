# Deploy Checklist Roboktober

Deze checklist is gericht op de huidige repository-structuur:
- Backend: Laravel in roboktober-api
- Frontend: Vue/Vite in roboktober-frontend

## 1. Pre-Deploy (Lokaal)

1. Zorg dat je op de juiste commit staat:
   - git rev-parse --short HEAD
2. Controleer dat je werkboom schoon is:
   - git status --short
3. Run backend kwaliteitschecks:
   - cd roboktober-api
   - ./vendor/bin/pint --test
   - ./vendor/bin/phpstan analyse --no-progress
   - php artisan test --testsuite=Unit
   - php artisan test --testsuite=Feature
4. Run frontend kwaliteitschecks:
   - cd ../roboktober-frontend
   - npm ci
   - npm run lint:check
   - npm run type-check
   - npm run test:unit

## 2. Staging Deploy

Voorkeur (1 commando vanaf lokale machine):

- cp deploy/deploy.env.example deploy/deploy.env
- STAGING_HOST=<host-of-user@host> bash deploy/deploy-staging.sh

Of volledig via deploy.env zonder inline variabelen:

- bash deploy/deploy-staging.sh

1. SSH naar staging host.
2. Ga naar projectmap.
3. Haal nieuwste code op:
   - git fetch origin
   - git checkout master
   - git pull --ff-only origin master
4. Backend dependencies updaten:
   - cd roboktober-api
   - composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
5. Frontend dependencies + build:
   - cd ../roboktober-frontend
   - npm ci
   - npm run build
6. Laravel app optimaliseren:
   - cd ../roboktober-api
   - php artisan migrate --force
   - php artisan optimize:clear
   - php artisan config:cache
   - php artisan route:cache
   - php artisan view:cache
7. Herstart queue workers:
   - php artisan queue:restart
8. Controleer basis-endpoints:
   - curl -i https://<staging-host>/api/v1/posts
   - open https://<staging-host>/app/programma

## 3. Productie Deploy

Voorkeur (1 commando vanaf lokale machine):

- PRODUCTION_CONFIRM=deploy-production PRODUCTION_HOST=<host-of-user@host> bash deploy/deploy-production.sh

Of volledig via deploy.env zonder inline variabelen:

- PRODUCTION_CONFIRM=deploy-production bash deploy/deploy-production.sh

Let op:
- Productie wrapper blokkeert deploys zonder `PRODUCTION_CONFIRM=deploy-production`.

1. Herhaal stap 1 t/m 8 van staging op productie.
2. Doe deploy bij voorkeur in low-traffic window.
3. Bevestig dat release-commit overeenkomt:
   - git rev-parse --short HEAD
4. Voer smoke checks uit:
   - API: /api/v1/posts
   - SPA: /app/programma
   - Auth route: /app/aanmelden
   - Admin redirect/guard: /app/admin/users

## 4. Post-Deploy Verificatie

1. Check applicatielogs:
   - tail -n 200 roboktober-api/storage/logs/laravel.log
2. Check queue gezondheid:
   - php artisan queue:monitor (indien geconfigureerd)
3. Check database migratiestatus:
   - php artisan migrate:status
4. Functionele checks:
   - Teamregistratie aanmaken
   - Login/logout
   - Programma pagina laden
   - Nieuws API laden

## 5. Rollback Plan

1. Bepaal vorige stabiele commit:
   - git log --oneline -n 10
2. Checkout vorige commit of tag:
   - git checkout <previous-stable-commit>
3. Herhaal deployment stappen:
   - composer install
   - npm ci && npm run build
   - php artisan optimize:clear
   - php artisan config:cache
   - php artisan route:cache
   - php artisan view:cache
   - php artisan queue:restart
4. Voer dezelfde smoke checks opnieuw uit.

## 6. Praktische Notities

1. Frontend vereist Node-versie volgens roboktober-frontend/package.json engines.
2. CI draait Node 22.18.0; houd lokale/staging/prod runtime hier zo dicht mogelijk bij.
3. PHPStan staat nu blocking in CI, dus mainline commits moeten static-analysis clean blijven.
4. Bij permissieproblemen op storage eerst ownership/rechten van www-data controleren op:
   - roboktober-api/storage
   - roboktober-api/bootstrap/cache
   - roboktober-api/storage/app/public/team-fotos
