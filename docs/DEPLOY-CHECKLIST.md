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
5. Bepaal expliciet de PHP binary voor serverstappen (voorkomt extension/runtime mismatch):
   - PHP_BIN="$(command -v php8.6 || command -v php8.5 || command -v php8.4 || command -v php8.3 || command -v php)"
   - $PHP_BIN -v

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
   - PHP_BIN="$(command -v php8.6 || command -v php8.5 || command -v php8.4 || command -v php8.3 || command -v php)"
   - $PHP_BIN artisan migrate --force
   - $PHP_BIN artisan optimize:clear
   - $PHP_BIN artisan config:cache
   - $PHP_BIN artisan route:cache
   - $PHP_BIN artisan view:cache
7. Herstart queue workers:
   - $PHP_BIN artisan queue:restart
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
5. Verifieer mailtransport (SMTP) expliciet:
   - cd roboktober-api
   - PHP_BIN="$(command -v php8.6 || command -v php8.5 || command -v php8.4 || command -v php8.3 || command -v php)"
   - $PHP_BIN -r 'require __DIR__."/vendor/autoload.php"; $app=require __DIR__."/bootstrap/app.php"; $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap(); try { Illuminate\Support\Facades\Mail::raw("Deploy SMTP smoke", function($m){ $m->to(config("mail.from.address"))->subject("Deploy SMTP smoke");}); echo "MAIL_OK\n"; } catch (Throwable $e) { echo "MAIL_ERR: ".$e->getMessage()."\n"; }'
   - Verwacht resultaat: `MAIL_OK`

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
5. Controleer dat er geen recente SMTP errors loggen:
   - tail -n 120 roboktober-api/storage/logs/laravel.log | grep -Ei 'smtp|mailer|authentication required|failed to authenticate|connection could not be established' || echo 'NO_RECENT_SMTP_ERRORS'

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
5. Voor elke destructieve data-operatie (delete/truncate) eerst een DB-backup maken en pad noteren in change-log.
6. Volledige test-suites draaien lokaal/CI; productiehost is bedoeld voor smoke- en runtime-verificatie.
