#!/usr/bin/env bash

set -e

export APP_ENV=test

vendor/bin/phpstan analyse
vendor/bin/phpunit --testsuite unit -v
vendor/bin/behat --suite acceptance --tags="~@ignore" -v
vendor/bin/phpunit --testsuite functional -v
