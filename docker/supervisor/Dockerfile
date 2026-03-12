FROM php:8.2-fpm-alpine

ARG UID=1000
ARG GID=1000

# Устанавливаем redis и необходимые зависимости
RUN apk add --no-cache --virtual .build-deps \
    autoconf \
    g++ \
    gcc \
    libc-dev \
    make \
    linux-headers \
    build-base \
    postgresql-dev \
    postgresql-libs \
    libjpeg-turbo-dev \
    libpng-dev \
    icu-dev \
    libzip-dev \
    libxml2-dev \
    curl-dev \
    pcre-dev \
    ${PHPIZE_DEPS} \
    && apk add --no-cache \
        postgresql-libs \
        libpng \
        icu \
        libzip \
        libjpeg-turbo \
        imagemagick \
        imagemagick-dev \
        libwebp-dev \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo bcmath pdo_pgsql pdo_mysql gd intl pcntl zip soap curl exif simplexml \
    && pecl install redis imagick \
    && docker-php-ext-enable redis imagick \
    && docker-php-ext-enable redis \
    && apk del .build-deps \
    && rm -rf /tmp/pear /var/cache/apk/* /usr/share/man /var/lib/apt/lists/*

# Supervisor
RUN apk update && apk add --no-cache supervisor redis \
    && rm -rf /var/cache/apk/* /usr/share/man /var/lib/apt/lists/*

RUN mkdir -p "/etc/supervisor/logs"

# Создание пользователя и нужных папок
RUN mkdir -p /.config/psysh \
    /var/www/html/storage/logs \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/storage/framework/cache \
    /var/www/html/bootstrap/cache \
    && addgroup -g ${GID} greenpowered \
    && adduser -D -u ${UID} -G greenpowered -s /bin/bash greenpowered \
    && chown -R greenpowered:greenpowered /.config /var/www/html /var/www/html/storage \
    && chmod -R 0700 /.config \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]
