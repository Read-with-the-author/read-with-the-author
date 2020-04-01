FROM php:7.4-alpine
COPY php.ini /usr/local/etc/php/

# For Inky
RUN apk add libxslt-dev && docker-php-ext-configure xsl --with-xsl && docker-php-ext-install xsl
