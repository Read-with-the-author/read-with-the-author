# Copied from https://github.com/dunglas/symfony-docker

# the different stages of this Dockerfile are meant to be built into separate images
# https://docs.docker.com/develop/develop-images/multistage-build/#stop-at-a-specific-build-stage
# https://docs.docker.com/compose/compose-file/#target


# https://docs.docker.com/engine/reference/builder/#understand-how-arg-and-from-interact
ARG PHP_VERSION=7.4
ARG NGINX_VERSION=1.17

# "php" stage
FROM php:${PHP_VERSION}-fpm-alpine AS symfony_php

# For Inky
RUN apk add libxslt-dev && docker-php-ext-configure xsl --with-xsl && docker-php-ext-install xsl

# Install pcntl for long-running processes
RUN docker-php-ext-install pcntl

# persistent / runtime deps
RUN apk add --no-cache \
        acl \
        fcgi \
        file \
        gettext \
        git \
        jq \
    ;

ARG APCU_VERSION=5.1.18
RUN set -eux; \
	apk add --no-cache --virtual .build-deps \
	    $PHPIZE_DEPS \
	    icu-dev \
	    libzip-dev \
	    zlib-dev \
	; \
	\
	docker-php-ext-configure zip; \
	docker-php-ext-install -j$(nproc) \
	    intl \
	    zip \
	; \
	pecl install \
	    apcu-${APCU_VERSION} \
	; \
	pecl clear-cache; \
	docker-php-ext-enable \
	    apcu \
	    opcache \
	; \
	\
	runDeps="$( \
	    scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
	        | tr ',' '\n' \
	        | sort -u \
	        | awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
	)"; \
	apk add --no-cache --virtual .phpexts-rundeps $runDeps; \
	\
	apk del .build-deps

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN ln -s $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini
COPY docker/php/conf.d/symfony.ini $PHP_INI_DIR/conf.d/symfony.ini

RUN set -eux; \
	{ \
		echo '[www]'; \
		echo 'ping.path = /ping'; \
	} | tee /usr/local/etc/php-fpm.d/docker-healthcheck.conf

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
# install Symfony Flex globally to speed up download of Composer packages (parallelized prefetching)
RUN set -eux; \
	composer global require "symfony/flex" --prefer-dist --no-progress --no-suggest --classmap-authoritative; \
	composer clear-cache
ENV PATH="${PATH}:/root/.composer/vendor/bin"

WORKDIR /srv/app

# build for production
ARG APP_ENV=prod

# Allow to use development versions of Symfony
ARG STABILITY="stable"
ENV STABILITY ${STABILITY:-stable}

# Allow to select skeleton version
ARG SYMFONY_VERSION=""

# Download the Symfony skeleton and leverage Docker cache layers
RUN composer create-project "symfony/skeleton ${SYMFONY_VERSION}" . --stability=$STABILITY --prefer-dist --no-dev --no-progress --no-scripts --no-interaction; \
	composer clear-cache

###> recipes ###
###< recipes ###

VOLUME /srv/app/var

COPY docker/php/docker-healthcheck.sh /usr/local/bin/docker-healthcheck
RUN chmod +x /usr/local/bin/docker-healthcheck

HEALTHCHECK --interval=10s --timeout=3s --retries=3 CMD ["docker-healthcheck"]

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

# "nginx" stage
# depends on the "php" stage above
FROM nginx:${NGINX_VERSION}-alpine AS symfony_nginx

COPY docker/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf

WORKDIR /srv/app

COPY ./public public/

FROM symfony_php as symfony_php_production
COPY . .

RUN set -eux; \
	mkdir -p var/cache var/log; \
	composer install --prefer-dist --no-progress --no-suggest --no-interaction --no-dev; \
    composer dump-autoload --classmap-authoritative --no-dev; sync
