#!/usr/bin/env bash

set -eu

docker-compose run --rm php sh ./slow_tests.sh "$@"
