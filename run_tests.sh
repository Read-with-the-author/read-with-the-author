#!/usr/bin/env bash

set -e

export APP_ENV=test

vendor/bin/phpstan analyse
vendor/bin/phpunit --testsuite unit -v "$@"
vendor/bin/behat --suite acceptance --tags="~@ignore" -v

mkdir -p var/db/test
bin/console doctrine:database:create
bin/console doctrine:migrations:migrate --no-interaction
vendor/bin/phpunit --testsuite integration -v "$@" --exclude-group slow --exclude-group internet
