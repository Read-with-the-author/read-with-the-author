SHELL=/bin/bash

export HOST_UID := $(shell id -u)
export HOST_GID := $(shell id -g)

ifeq ($(COMPOSER_HOME),)
export COMPOSER_HOME=~/.composer
endif

RUN_PHP:=docker-compose run --rm php
DOCKER_COMPOSE_DEV:=docker-compose
DOCKER_COMPOSE_PROD:=docker-compose -f docker-compose.yml

.PHONY: migrations
migrations:
	${RUN_PHP} bin/console doctrine:migrations:diff

.PHONY: migrate
migrate:
	${RUN_PHP} bin/console doctrine:migrations:migrate --no-interaction

.PHONY: push
push:
	${DOCKER_COMPOSE_DEV} push

.PHONY: build
build:
	${DOCKER_COMPOSE_DEV} build

.PHONY: push-prod
push-prod:
	${DOCKER_COMPOSE_PROD} push

.PHONY: up
up: down
	${DOCKER_COMPOSE_DEV} up -d

up-prod: down
	${DOCKER_COMPOSE_PROD} up -d

.PHONY: down
down:
	${DOCKER_COMPOSE_DEV} down
