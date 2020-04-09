#!/usr/bin/env bash

set -eu

time docker-compose run --rm php sh ./run_tests.sh "$@"
