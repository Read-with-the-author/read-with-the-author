#!/usr/bin/env bash

set -eu

docker-compose run --rm php php import.php
