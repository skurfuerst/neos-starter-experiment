#!/usr/bin/env bash

cd 2020-09-a
composer install
docker build -t profile-2020-09-a .