FROM node:22-bookworm-slim AS frontend-builder

WORKDIR /workspace/roboktober-frontend

COPY roboktober-frontend/package.json roboktober-frontend/package-lock.json ./
RUN npm ci

COPY roboktober-frontend ./

# The frontend build writes to ../roboktober-api/public/app
RUN mkdir -p /workspace/roboktober-api/public/app
RUN npm run build


FROM php:8.3-cli-bookworm AS app

ENV APP_ROOT=/var/www/html/roboktober-api

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libicu-dev \
        libxml2-dev \
        libzip-dev \
        libonig-dev \
        libsqlite3-dev \
        default-mysql-client \
    && docker-php-ext-install \
        bcmath \
        intl \
        mbstring \
        pdo \
        pdo_mysql \
        zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY roboktober-api ./roboktober-api
COPY --from=frontend-builder /workspace/roboktober-api/public/app ./roboktober-api/public/app
COPY deploy/railway/start.sh /usr/local/bin/start.sh

WORKDIR ${APP_ROOT}

RUN composer install \
        --no-dev \
        --prefer-dist \
        --no-interaction \
        --no-progress \
        --optimize-autoloader \
    && chmod +x /usr/local/bin/start.sh \
    && mkdir -p storage/framework/{cache,sessions,testing,views} storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8080

CMD ["/usr/local/bin/start.sh"]