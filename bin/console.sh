#!/usr/bin/env bash

set -eu

docker-compose run --rm php bin/console "$@"
