# build the "1brc:latest" image:
# docker build --tag "1brc" .

FROM php:8.3-cli

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN install-php-extensions opcache xdebug
