SHELL=/bin/bash

export HOST_UID := $(shell id -u)
export HOST_GID := $(shell id -g)

ifeq ($(COMPOSER_HOME),)
export COMPOSER_HOME=~/.composer
endif


~/.composer:
	mkdir -p ~/.composer

vendor: ~/.composer composer.json composer.lock
	${PHP_RUN} composer install --prefer-dist --ansi

.PHONY: up
up: vendor
	docker-compose up -d nginx
