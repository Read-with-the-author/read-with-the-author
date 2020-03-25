#!/usr/bin/env bash

set -eu

docker-compose run --rm php sh ./run_tests.sh
