#!/usr/bin/env bash

set -e

vendor/bin/phpunit --testsuite integration -v
