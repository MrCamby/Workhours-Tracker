FROM php:apache

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions mysqli

ADD www /var/www/html

ENV DATABASE_SERVER="database"
ENV DATABASE_DATABASE="workhours"
ENV DATABASE_USER="user"
ENV DATABASE_PASSWORD="password"