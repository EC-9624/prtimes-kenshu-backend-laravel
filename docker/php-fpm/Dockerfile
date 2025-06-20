FROM php:8.3.21-fpm-alpine3.20

RUN apk add --no-cache \
    postgresql-dev \
    libpq \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libwebp-dev \
    libxpm-dev \
    autoconf \
    build-base \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
        --with-xpm \
    && docker-php-ext-install \
        pdo_pgsql \
        pgsql \
        pdo_mysql \
        gd

RUN chown www-data:www-data /var/log/ && chmod 755 /var/log/

RUN mkdir -p /var/tmp/php_upload_tmp \
    && chown www-data:www-data /var/tmp/php_upload_tmp \
    && chmod 755 /var/tmp/php_upload_tmp

WORKDIR /var/www/

COPY --from=composer/composer:latest /usr/bin/composer /usr/bin/composer

COPY docker/php-fpm/php.ini /usr/local/etc/php/conf.d/custom.ini

COPY my-app/ /var/www/
