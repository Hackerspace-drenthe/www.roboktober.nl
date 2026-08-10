# Roboktober API

Laravel 13 backend for Roboktober.

This project provides:
- Public JSON API for posts, teams, pages, links, and registration.
- Account-first auth flow with Sanctum bearer tokens.
- Team membership workflow (apply/review) for existing teams.
- Filament admin panel for content and moderation.
- Mail notification flow for new team registrations.

## Current status

- API v1 routes are active under `/api/v1`.
- Public frontend assets are served from `public/app`.
- Registration endpoint has validation, throttling, and privacy-safe output.
- Account endpoints include password reset and account/password updates.
- Dependency audits are currently clean:
	- `composer audit`: no known advisories.

## Security baseline

- Strict request validation via FormRequest classes.
- Registration endpoint rate limiting:
	- `POST /api/v1/registratie`: 5/min per IP, 20/hour per e-mailadres.
	- account-gebonden registratiebeheer (`/api/v1/registratie/mijn*`): user-based limieten.
- API responses include security headers:
	- `X-Content-Type-Options: nosniff`
	- `X-Frame-Options: DENY`
	- `Referrer-Policy: no-referrer`
	- `Permissions-Policy`
	- `Content-Security-Policy`
- Team email addresses are not exposed in public API responses.

## Requirements

- PHP 8.3+
- Composer 2+
- Node.js 22+
- npm 10+
- MySQL 8+ (or compatible MariaDB)

## Installation (local)

1. Go to the backend folder:

```bash
cd roboktober-api
```

2. Install PHP dependencies:

```bash
composer install
```

3. Create your environment file:

```bash
cp .env.example .env
```

4. Update database settings in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=roboktober
DB_USERNAME=roboktober
DB_PASSWORD=...
```

5. Generate app key and run migrations + seeders:

```bash
php artisan key:generate
php artisan migrate --seed
```

6. Install frontend build dependencies for backend assets:

```bash
npm install --ignore-scripts
```

7. Build backend-side assets:

```bash
npm run build
```

8. Start the API locally:

```bash
php artisan serve
```

The API will be available at `http://localhost:8000/api/v1`.

## One-command setup

For a fresh local setup you can also use:

```bash
composer setup
```

## Admin panel

- URL: `http://localhost:8000/admin`
- Seeded user (after `php artisan migrate --seed`):
	- Email: `admin@hackerspace-drenthe.nl`
	- Password: `password`

Change the seeded credentials immediately outside local development.

## API endpoints (public)

- `GET /api/v1/posts`
- `GET /api/v1/posts/{slug}`
- `GET /api/v1/teams`
- `GET /api/v1/teams/{id}`
- `GET /api/v1/teams/{id}/robots`
- `GET /api/v1/links`
- `GET /api/v1/pages/{slug}`
- `POST /api/v1/registratie` (auth required)

## API endpoints (auth/account)

- `POST /api/v1/auth/register`
- `POST /api/v1/auth/login`
- `POST /api/v1/auth/logout`
- `GET /api/v1/auth/me`
- `POST /api/v1/auth/forgot-password`
- `POST /api/v1/auth/reset-password`
- `PATCH /api/v1/auth/account`
- `PATCH /api/v1/auth/password`

## API endpoints (teamcaptain/teammembers)

- `GET /api/v1/registratie/mijn`
- `PUT /api/v1/registratie/mijn`
- `GET /api/v1/registratie/mijn/updates`
- `POST /api/v1/registratie/mijn/updates`
- `POST /api/v1/teams/{team}/membership-requests`
- `GET /api/v1/teams/mijn/lidmaatschappen`
- `GET /api/v1/teams/mijn/membership-requests` (teamcaptain/moderator/admin)
- `PATCH /api/v1/teams/mijn/membership-requests/{teamMembership}` (teamcaptain/moderator/admin)

## Development commands

- Run tests:

```bash
composer test
```

- Static analysis:

```bash
./vendor/bin/phpstan analyse --no-progress
```

- Code style:

```bash
./vendor/bin/pint
```

- Full local dev loop (server + queue + logs + vite):

```bash
composer dev
```

## Testing policy (lokaal/CI vs productie)

- Volledige test-suites horen in lokale dev en CI te draaien.
- Productiehosts zijn primair voor runtime- en smoke-verificatie, niet voor volledige dev test-tooling.
- Aanbevolen minimale productiechecks na deploy:
	- `GET /api/v1/posts`
	- kritieke frontend route laden
	- SMTP smoke check (zie runbook hieronder)

## Operations quick runbook (productie)

### 1. SMTP verificatie

Gebruik dezelfde Laravel app-context voor een realistische test:

```bash
cd /var/www/www.roboktober.nl/roboktober-api
PHP_BIN="$(command -v php8.6 || command -v php8.5 || command -v php8.4 || command -v php8.3 || command -v php)"
$PHP_BIN -r 'require __DIR__."/vendor/autoload.php"; $app=require __DIR__."/bootstrap/app.php"; $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap(); try { Illuminate\Support\Facades\Mail::raw("SMTP smoke", function($m){ $m->to(config("mail.from.address"))->subject("SMTP smoke");}); echo "MAIL_OK\n"; } catch (Throwable $e) { echo "MAIL_ERR: ".$e->getMessage()."\n"; }'
```

Verwachte output:
- `MAIL_OK`

Bij fouten:
- controleer `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS`
- refresh config cache:

```bash
$PHP_BIN artisan config:clear
$PHP_BIN artisan config:cache
```

### 2. Database backup (voor mutaties)

```bash
cd /var/www/www.roboktober.nl/roboktober-api
mkdir -p storage/backups
TS="$(date +%Y%m%d_%H%M%S)"
mysqldump --single-transaction --quick --routines --triggers --events \
	-h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" \
	| gzip > "storage/backups/roboktober_${TS}.sql.gz"
ls -lh "storage/backups/roboktober_${TS}.sql.gz"
```

### 3. Veilige data cleanup flow

1. Dry-run met aantallen en voorbeelden.
2. Team-confirmatie op exacte scope.
3. Uitvoering in veilige delete-volgorde.
4. After-check met before/after aantallen.
5. Resultaat loggen in release notes of operationele changelog.
