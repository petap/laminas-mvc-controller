FROM phpdockerio/php74-fpm:latest

# Install selected extensions and other stuff
RUN apt-get update \
    && apt-get -y --no-install-recommends install git php-xdebug php7.4-bcmath php7.4-intl \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

ARG UID=1000
ARG GID=1000

RUN usermod -u "$UID" www-data
RUN groupmod -g "$GID" www-data

WORKDIR /var/www/laminas-mvc-controller