FROM php:8.2-fpm-alpine

# Добавляем сертификаты и меняем зеркало
RUN apk add --no-cache ca-certificates && update-ca-certificates && \
    sed -i 's/dl-cdn.alpinelinux.org/mirrors.tuna.tsinghua.edu.cn/g' /etc/apk/repositories

ARG UID=1000
ARG GID=1000

# Настройка PHP-FPM
RUN sed -i 's/^pm = .*/pm = dynamic/' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i 's/^pm.max_children = .*/pm.max_children = 10/' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i 's/^pm.start_servers = .*/pm.start_servers = 2/' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i 's/^pm.min_spare_servers = .*/pm.min_spare_servers = 1/' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i 's/^pm.max_spare_servers = .*/pm.max_spare_servers = 3/' /usr/local/etc/php-fpm.d/www.conf

# Системные зависимости
RUN apk update && apk add --no-cache \
    nodejs \
    npm \
    git \
    openssh-client \
    shadow \
    libcurl \
    libpng \
    libpq \
    libzip \
    libjpeg-turbo \
    libzip \
    icu-libs \
    bash \
    imagemagick \
    redis \
    ttf-dejavu

# Build-зависимости
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
    pkgconf \
    imagemagick-dev \
    nasm \
    ${PHPIZE_DEPS}

# PHP расширения
RUN docker-php-ext-configure gd --with-jpeg && \
    docker-php-ext-install -j$(nproc) \
        bcmath \
        pdo \
        pdo_pgsql \
        pdo_mysql \
        gd \
        intl \
        pcntl \
        zip \
        soap \
        curl \
        exif \
        simplexml \
        sockets && \
    pecl install redis imagick && \
    docker-php-ext-enable redis imagick

# Очистка временных зависимостей
RUN apk del .build-deps && rm -rf /tmp/pear /var/cache/apk/* /usr/share/man

# Установка Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY php.ini /usr/local/etc/php/conf.d/php.ini

# Настройка пользователя и прав
RUN mkdir -p /.config/psysh \
    /var/www/html/storage/logs \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/storage/framework/cache \
    /var/www/html/bootstrap/cache \
    && groupadd --gid ${GID} greenpowered \
    && useradd --uid ${UID} --gid greenpowered --shell /bin/bash --create-home greenpowered \
    && chown -R greenpowered:greenpowered /.config /var/www/html /var/www/html/storage \
    && chmod -R 0700 /.config \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

USER greenpowered
WORKDIR /var/www/html
EXPOSE 9000
