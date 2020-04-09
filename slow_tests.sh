#!/usr/bin/env bash

set -e

export APP_ENV=test

mkdir -p var/db/test
bin/console doctrine:database:create
bin/console doctrine:migrations:migrate --no-interaction
vendor/bin/phpunit --testsuite integration -v "$@"
