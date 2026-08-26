FROM dunglas/frankenphp:php8.4-trixie

RUN install-php-extensions mysqli pdo_mysql

WORKDIR /app

COPY . /app

RUN php -m | grep mysqli

EXPOSE 8080