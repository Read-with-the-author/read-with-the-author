#!/usr/bin/env bash

set -eu

docker-compose up -d mailhog
docker-compose run --rm php sh ./slow_tests.sh
