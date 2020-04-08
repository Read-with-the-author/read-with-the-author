SHELL=/bin/bash

export HOST_UID := $(shell id -u)
export HOST_GID := $(shell id -g)

ifeq ($(COMPOSER_HOME),)
export COMPOSER_HOME=~/.composer
endif

RUN_PHP:=docker-compose run --rm php
DOCKER_COMPOSE_DEV:=docker-compose
DOCKER_COMPOSE_PROD:=docker-compose -f docker-compose.yml -f docker-compose.prod.yml

HOSTNAME:=noback.readwiththeauthor.localhost
HOSTS_ENTRY:=127.0.0.1 ${HOSTNAME}

.PHONY: hosts-entry
ifeq ($(PLATFORM), $(filter $(PLATFORM), Darwin Linux))
hosts-entry:
	(grep "$(HOSTS_ENTRY)" /etc/hosts) || echo '$(HOSTS_ENTRY)' | sudo tee -a /etc/hosts
else
hosts-entry:
	$(warning Make sure to add "${HOSTS_ENTRY}" to your operating system's hosts file)
endif

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

.PHONY: up
up: hosts-entry down
	${DOCKER_COMPOSE_DEV} up -d

.PHONY: down
down:
	${DOCKER_COMPOSE_DEV} down --remove-orphans

.PHONY: deploy
deploy:
	./deploy.sh

.PHONY: release
release: build push deploy
