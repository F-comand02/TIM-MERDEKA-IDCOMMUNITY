FROM dunglas/frankenphp:php8.4-trixie

RUN install-php-extensions mysqli pdo_mysql

WORKDIR /app

COPY . /app

EXPOSE 8080

CMD ["frankenphp", "run", "--config", "/app/Caddyfile"]