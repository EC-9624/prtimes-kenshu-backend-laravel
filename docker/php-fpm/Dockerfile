FROM php:8.3.21-fpm-alpine3.20

RUN apk add --no-cache \
    postgresql-dev \
    libpq \
    autoconf \
    build-base \
    && docker-php-ext-install pdo_pgsql pgsql pdo_mysql

RUN chown www-data:www-data /var/log/ && chmod 755 /var/log/

RUN mkdir -p /var/tmp/php_upload_tmp \
    && chown www-data:www-data /var/tmp/php_upload_tmp \
    && chmod 755 /var/tmp/php_upload_tmp

WORKDIR /var/www/

COPY --from=composer/composer:latest /usr/bin/composer /usr/bin/composer

COPY docker/php-fpm/php.ini /usr/local/etc/php/conf.d/custom.ini
# FIX: Copy the *contents* of my-app from the build context (which is the project root)
# into the /var/www/ directory inside the container.
COPY my-app/ /var/www/
