#!/usr/bin/env bash

# Set script to exit on errors.
set -e

# Get script's absolute location.
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

# Change to repo root.
cd ${DIR};
cd ../..

# Git pull, composer install.
git fetch --prune
git branch -a
git status
if [ -n "$1" ]; then
  git reset --hard "$1"
else
  git pull
fi
composer install --no-interaction --no-dev --prefer-dist

# Drupal routine.
cd web/
../bin/drush state:set system.maintenance_mode 1 -y
../bin/drush cache:rebuild -y
../bin/drush updatedb -y
../bin/drush config:import -y
../bin/drush entity:updates -y
../bin/drush core:cron -y
../bin/drush state:set system.maintenance_mode 0 -y
../bin/drush cache:rebuild -y
