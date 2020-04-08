#!/usr/bin/env bash

set -eu

MACHINE_ID=$(cat machine_id)
eval "$(docker-machine env "${MACHINE_ID}")"

docker network create traefik || true

DOCKER_COMPOSE="docker-compose -f docker-compose.yml -f docker-compose.prod.yml"
${DOCKER_COMPOSE} pull

${DOCKER_COMPOSE} up -d --no-build --remove-orphans --force-recreate

eval "$(docker-machine env -u)"
