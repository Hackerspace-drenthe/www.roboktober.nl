# Deploy automation (Apache server)

Deze map bevat een eenvoudige deploy-opzet voor de server op 192.168.1.10.

## Bestanden

- deploy.sh: Pull + composer install + artisan optimize + migrate
- apache/roboktober.conf: Apache vhost template
- systemd/roboktober-deploy.service: handmatige deploy service
- systemd/roboktober-deploy.timer: optionele periodieke deploy

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
sudo a2enmod rewrite
sudo a2ensite roboktober.conf
sudo systemctl reload apache2
```

4. Laravel init (eenmalig):

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
