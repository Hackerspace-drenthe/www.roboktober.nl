# Deploy automation (Apache server)

Deze map bevat een eenvoudige deploy-opzet voor de server op 192.168.1.10.

## Bestanden

- deploy.sh: Pull + composer install + artisan optimize + migrate
- apache/roboktober.conf: Apache vhost template
- systemd/roboktober-deploy.service: handmatige deploy service
- systemd/roboktober-deploy.timer: optionele periodieke deploy

Deploy.sh detecteert automatisch een beschikbare PHP-versie (voorkeur: hoog naar laag, standaard php8.6 -> php8.5 -> php8.4 -> php8.3 -> php) en valideert vereiste extensies voor Laravel.

## Eenmalige setup op de server

1. Repo naar server:

```bash
cd /var/www
git clone https://github.com/Hackerspace-drenthe/www.roboktober.nl.git
cd /var/www/www.roboktober.nl
```

2. Script uitvoerbaar maken:

```bash
chmod +x deploy/deploy.sh
```

3. Apache config installeren:

```bash
sudo cp deploy/apache/roboktober.conf /etc/apache2/sites-available/roboktober.conf
sudo a2enmod rewrite headers ssl
sudo a2ensite roboktober.conf
sudo systemctl reload apache2
```

4. TLS certificaat aanvragen:

```bash
sudo certbot --apache -d roboktober.nl -d www.roboktober.nl
```

5. Laravel init (eenmalig):

```bash
cd /var/www/www.roboktober.nl/roboktober-api
cp .env.example .env
php artisan key:generate
php artisan migrate --force
```

## Deploy handmatig

```bash
cd /var/www/www.roboktober.nl
bash deploy/deploy.sh
```

## Auto deploy via GitHub webhook

Deze setup gebruikt een webhook endpoint op de server die bij een push naar `master` de systemd deploy-service start.

1. Webhook config op server plaatsen:

```bash
sudo mkdir -p /etc/roboktober
sudo cp deploy/github-webhook.env.example /etc/roboktober/github-webhook.env
sudo chown root:www-data /etc/roboktober/github-webhook.env
sudo chmod 640 /etc/roboktober/github-webhook.env
```

2. Zet in `/etc/roboktober/github-webhook.env` een sterke unieke `GITHUB_WEBHOOK_SECRET`.

3. Sta alleen het deploy-commando toe via sudoers voor de webserver-user:

```bash
echo 'www-data ALL=NOPASSWD:/bin/systemctl start roboktober-deploy.service' | sudo tee /etc/sudoers.d/roboktober-deploy-hook >/dev/null
sudo chmod 440 /etc/sudoers.d/roboktober-deploy-hook
sudo visudo -cf /etc/sudoers.d/roboktober-deploy-hook
```

4. Webhook endpoint controleren:

```bash
curl -i https://roboktober.nl/github-deploy-hook.php
```

Je krijgt `405 Method not allowed` bij GET, dat is correct.

5. In GitHub repo settings:

- Ga naar Settings > Webhooks > Add webhook
- Payload URL: `https://roboktober.nl/github-deploy-hook.php`
- Content type: `application/json`
- Secret: exact dezelfde waarde als `GITHUB_WEBHOOK_SECRET`
- Events: alleen `Just the push event`
- Active: aan

6. Verifiëren na een push:

```bash
sudo systemctl status roboktober-deploy.service --no-pager -n 100
journalctl -u roboktober-deploy.service -n 100 --no-pager
```

## Deploy via systemd (on-demand)

1. Units installeren:

```bash
sudo cp deploy/systemd/roboktober-deploy.service /etc/systemd/system/
sudo cp deploy/systemd/roboktober-deploy.timer /etc/systemd/system/
sudo systemctl daemon-reload
```

2. Handmatige run:

```bash
sudo systemctl start roboktober-deploy.service
sudo systemctl status roboktober-deploy.service
```

## Deploy via systemd timer (elke 5 min)

```bash
sudo systemctl enable --now roboktober-deploy.timer
systemctl list-timers | grep roboktober
```

## Belangrijke notities

- Pas in de service file de `User=` aan naar de gebruiker die eigenaar is van `/var/www/www.roboktober.nl`.
- Standaard verwacht deploy.sh een schone server worktree.
- Frontend build op server is standaard uit (`BUILD_FRONTEND=false`) omdat assets al in git staan.
- Wil je handmatig pinnen op een specifieke PHP-versie, zet dan `PHP_BIN` in de service of shell (bijvoorbeeld `PHP_BIN=php8.4`).
- Endpoint implementatie staat in `roboktober-api/public/github-deploy-hook.php`.
