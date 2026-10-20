
FROM dunglas/frankenphp:php8.4-trixie

RUN install-php-extensions mysqli pdo_mysql

WORKDIR /app

COPY . /app

RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

EXPOSE 80

CMD ["frankenphp", "run", "--config", "/app/Caddyfile.vercel"]
