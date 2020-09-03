<?php

declare(strict_types=1);

namespace Neos\Starter\Features\Neos;


class NeosFeature
{

    public function generate(\Neos\Starter\Api\Dto\Configuration $configuration, \Neos\Starter\Generator\DistributionBuilder $distribution)
    {
        $distribution->readme()->addSection( "
            # Neos Project {$configuration->getSitePackageKey()}

            This project was kickstarted by https://start.neos.io. This README explains the main features and how to get started.

            ## Prerequisites

            - You need [Docker](https://docker.com) installed. The instructions work on Mac, Linux and Windows.
            - You need Composer installed on your host machine (and thus PHP).

            ## Development Setup

            Preparation:

            - Run `composer install`
            - Run `docker-compose pull`
            - Run `docker-compose build --pull`

            Start everything:

            - Run `docker-compose up -d`

            ## Features
        ");

        $distribution->readme()->addSectionAtEnd( "
            ## Production Deployment Recommendations

            We suggest you use one of the following deployment strategies:

            - Docker on production. (TODO: Gitlab file, ...)
            - Ansistrano
        ");

        $distribution->addFile('docker-compose.yml', "
            ##################################################
            ##### DEVELOPMENT ENVIRONMENT           ##########
            ##################################################

            # Public ports:
            #  - 8081 -> Neos
            #  - 13306 -> maria db (used for Neos)

            version: '3.5'
            services:
              #####
              # Neos CMS (php-fpm)
              neos:
                build:
                  context: .
                  dockerfile: ./Dockerfile.dev
                environment:
                  FLOW_CONTEXT: 'Development/Docker'
                  COMPOSER_CACHE_DIR: '/composer_cache'
                  # DB connection
                  DB_NEOS_HOST: 'maria-db'
                  DB_NEOS_PORT: 3306
                  DB_NEOS_PASSWORD: 'neos'
                  DB_NEOS_USER: 'neos'
                  DB_NEOS_DATABASE: 'neos'
                  # auto site import
                  SITE_IMPORT_PACKAGE_KEY: '{$configuration->getSitePackageKey()}'
                  # auto creation of admin user for Neos backend
                  ADMIN_USERNAME: 'admin'
                  ADMIN_PASSWORD: 'password'
                  NGINX_HOST: 'localhost'
                  NGINX_PORT: 8081
                volumes:
                  - ./composer.json:/app/composer.json:cached
                  - ./composer.lock:/app/composer.lock:cached
                  - ./DistributionPackages/:/app/DistributionPackages/:ro,cached
                  # Content is writable to enable content dumps from inside the container
                  - ./DistributionPackages/{$configuration->getSitePackageKey()}/Resources/Private/Content:/app/DistributionPackages/{$configuration->getSitePackageKey()}/Resources/Private/Content/:cached
                  - ./Configuration/Development/Docker/:/app/Configuration/Development/Docker/:ro,cached
                  # Explicitly set up Composer cache for faster fetching of packages
                  - ./tmp/composer_cache:/composer_cache:cached
                ports:
                  - 8081:8081
                depends_on:
                  - maria-db

              #####
              # Maria DB
              maria-db:
                image: mariadb:10.3
                ports:
                  - 13306:3306
                environment:
                  MYSQL_ROOT_PASSWORD: neos
                  MYSQL_DATABASE: neos
                  MYSQL_USER: neos
                  MYSQL_PASSWORD: neos
                # use Unicode encoding as default!
                command: ['mysqld', '--character-set-server=utf8mb4', '--collation-server=utf8mb4_unicode_ci']

        ");

        $distribution->addFile('Dockerfile.dev', "
            FROM php:7.4.10-fpm-buster

            # Install intl, bcmath, pdo, pdo_mysql, mysqli, libvips
            RUN apt-get update -y && \\
                apt-get install --no-install-recommends -y libicu-dev libxslt1-dev nginx-light libvips42 libvips-dev supervisor procps && \\
                mkdir -p /var/log/supervisor && \\
                rm -rf /var/lib/apt/lists/* && \\
                docker-php-ext-install intl bcmath pdo pdo_mysql mysqli xsl && \\
                pecl install vips && \\
                echo 'extension=vips.so' > /usr/local/etc/php/conf.d/vips.ini && \\
                pecl install redis && docker-php-ext-enable redis

            # install git and unzip for Composer
            RUN apt-get update -y && \\
                apt-get install --no-install-recommends -y unzip git && \\
                rm -rf /var/lib/apt/lists/*

            # install composer
            RUN curl --silent --show-error https://getcomposer.org/installer | php
            RUN mv composer.phar /usr/local/bin/composer
            RUN composer config --global cache-dir /composer_cache

            # application entrypoint
            ADD /deployment/local-dev/neos/entrypoint.sh /
            ADD /deployment/config-files/memory-limit-php.ini /usr/local/etc/php/conf.d/memory-limit-php.ini
            ADD /deployment/config-files/upload-limit-php.ini /usr/local/etc/php/conf.d/upload-limit-php.ini

            RUN rm -Rf /usr/local/etc/php-fpm.*
            ADD deployment/config-files/php-fpm.conf /usr/local/etc/php-fpm.conf

            ADD /deployment/config-files/nginx.template.conf /etc/nginx/nginx.template
            RUN mkdir -p /var/lib/nginx /usr/local/var/log/ & \
                chown -R www-data /var/lib/nginx /usr/local/var/log/ /etc/nginx/

            # cleanup & chown
            RUN mkdir -p /app/Data/Persistent /app/Configuration/Development/Docker /composer_cache && \
                chown -R www-data /app /composer_cache && \
                apt-get clean

            WORKDIR /app

            USER www-data
            ENTRYPOINT [ \"/entrypoint.sh\" ]
        ");
    }
}
