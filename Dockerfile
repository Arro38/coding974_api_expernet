FROM dunglas/frankenphp:php8.5

# Extensions indispensables pour Symfony
RUN install-php-extensions \
    pdo_pgsql \
    zip \
    intl \
    opcache \
    mbstring \
    xml \
    ctype \
    iconv

# Extensions très souvent utiles (recommandées)
RUN install-php-extensions \
    apcu \
    exif

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Répertoire de travail (important avec FrankenPHP)
WORKDIR /app
