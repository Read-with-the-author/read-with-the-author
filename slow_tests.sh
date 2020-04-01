#!/usr/bin/env bash

set -e

export APP_ENV=test

vendor/bin/phpunit --testsuite integration -v "$@"
