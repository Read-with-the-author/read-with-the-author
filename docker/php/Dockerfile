FROM php:7.4-alpine
COPY php.ini /usr/local/etc/php/

# For Inky
RUN apk add libxslt-dev && docker-php-ext-configure xsl --with-xsl && docker-php-ext-install xsl

# Install Composer
RUN apk --no-cache add libzip-dev git \
    && docker-php-ext-install zip
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
