#!/usr/bin/env bash

set -eu

docker-compose run --rm php sh ./fast_tests.sh "$@"
