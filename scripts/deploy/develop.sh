#!/usr/bin/env bash

git pull origin develop --verbose
composer install --dry-run --verbose
