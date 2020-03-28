#!/usr/bin/env bash

set -e

export APP_ENV=true

vendor/bin/phpunit --testsuite integration -v
