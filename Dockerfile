FROM dunglas/frankenphp:php8.5

RUN install-php-extensions pdo_pgsql
