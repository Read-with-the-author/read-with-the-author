SHELL=/bin/bash

export HOST_UID := $(shell id -u)
export HOST_GID := $(shell id -g)

ifeq ($(COMPOSER_HOME),)
export COMPOSER_HOME=~/.composer
endif

RUN_PHP:=docker-compose run --rm php

~/.composer:
	mkdir -p ~/.composer

vendor: ~/.composer composer.json composer.lock
	${PHP_RUN} composer install --prefer-dist --ansi

.PHONY: up
up: vendor
	docker-compose up -d nginx

.PHONY: migrations
migrations:
	${RUN_PHP} bin/console doctrine:migrations:diff

.PHONY: migrate
migrate:
	${RUN_PHP} bin/console doctrine:migrations:migrate --no-interaction
