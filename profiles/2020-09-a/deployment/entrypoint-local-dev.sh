#!/bin/bash
set -ex

composer install
composer remove $PACKAGES_TO_REMOVE
# TODO: add new site package
./flow doctrine:migrate
./flow user:create --roles Administrator admin password LocalDev Admin || true
./flow resource:publish

./flow site:import --package-key $SITE_PACKAGE_KEY

# start nginx
nginx &

exec /usr/local/sbin/php-fpm
