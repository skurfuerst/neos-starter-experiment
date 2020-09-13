<?php

declare(strict_types=1);

namespace Neos\Starter\Features\Deployment\DockerCompose;

use Neos\Flow\Annotations as Flow;
use Neos\Starter\Api\Configuration;
use Neos\Starter\Features\AbstractFeature;
use Neos\Starter\Features\Deployment\Redis\RedisFeature;
use Neos\Starter\Features\Deployment\Vips\VipsFeature;
use Neos\Starter\Features\FeatureInterface;
use Neos\Starter\Generator\DistributionBuilder;
use Neos\Starter\Generator\GenerationContextInterface;
use Neos\Starter\Generator\StringBuilder;
use Neos\Starter\Utility\YamlWithComments;

/**
 * @Flow\Proxy(false)
 */
class DockerComposeFeature extends AbstractFeature
{
    public function registerHooksBeforeActivation()
    {
    }

    public function activate()
    {
        $this->buildDockerComposeFile();
        $this->buildDockerfile();
    }

    public function deactivate()
    {
    }

    private function buildDockerComposeFile()
    {
        $dockerComposeFile = [
            'version##' => YamlWithComments::comment('
                ##################################################
                ##### DEVELOPMENT ENVIRONMENT           ##########
                ##################################################

                # Public ports:
                #  - 8081 -> Neos
                #  - 13306 -> maria db (used for Neos)
            '),
            'version' => '3.5',
            'services' => [
                'neos##' => YamlWithComments::comment('Neos CMS (php-fpm)'),
                'neos' => [
                    'build' => [
                        'context' => '.',
                        'dockerfile' => './Dockerfile.dev'
                    ],
                    'environment' => [
                        'FLOW_CONTEXT' => 'Development/Docker',
                        'COMPOSER_CACHE_DIR' => '/composer_cache',
                        'DB_NEOS_HOST##' => YamlWithComments::comment('DB connection'),
                        'DB_NEOS_HOST' => 'maria-db',
                        'DB_NEOS_PORT' => 3306,
                        'DB_NEOS_PASSWORD' => 'neos',
                        'DB_NEOS_USER' => 'neos',
                        'DB_NEOS_DATABASE' => 'neos',
                        'SITE_IMPORT_PACKAGE_KEY##' => YamlWithComments::comment('auto site import'),
                        'SITE_IMPORT_PACKAGE_KEY' => $this->generationContext->getConfiguration()->getSitePackageKey(),
                        'ADMIN_USERNAME##' => YamlWithComments::comment('auto creation of admin user for Neos backend'),
                        'ADMIN_USERNAME' => 'admin',
                        'ADMIN_PASSWORD' => 'password',
                    ],
                    'volumes' => [
                        './composer.json:/app/composer.json:cached',
                        './composer.lock:/app/composer.lock:cached',
                        './DistributionPackages/:/app/DistributionPackages/:ro,cached',
                        YamlWithComments::comment('Content is writable to enable content dumps from inside the container'),
                        "./DistributionPackages/{$this->generationContext->getConfiguration()->getSitePackageKey()}/Resources/Private/Content:/app/DistributionPackages/{$this->generationContext->getConfiguration()->getSitePackageKey()}/Resources/Private/Content/:cached",
                        './Configuration/Development/Docker/:/app/Configuration/Development/Docker/:ro,cached',
                        YamlWithComments::comment('Explicitly set up Composer cache for faster fetching of packages'),
                        './tmp/composer_cache:/composer_cache:cached',
                    ],
                    'ports' => [
                        '8081:8081'
                    ],
                    'depends_on' => [
                        'maria-db'
                    ]
                ],
                'maria-db##' => YamlWithComments::comment('Maria DB'),
                'maria-db' => [
                    'image' => 'mariadb:10.3',
                    'ports' => [
                        '13306:3306'
                    ],
                    'environment' => [
                        'MYSQL_ROOT_PASSWORD' => 'neos',
                        'MYSQL_DATABASE' => 'neos',
                        'MYSQL_USER' => 'neos',
                        'MYSQL_PASSWORD' => 'neos',
                    ],
                    'command##' => YamlWithComments::comment('use Unicode encoding as default!'),
                    'command' => ['mysqld', '--character-set-server=utf8mb4', '--collation-server=utf8mb4_unicode_ci']
                ]
            ]
        ];

        if ($this->generationContext->isFeatureEnabled(RedisFeature::class)) {
            $dockerComposeFile['services']['redis'] = [
                'image' => 'redis:5.0.4',
                'ports' => [
                    '16379:6379'
                ],
            ];
        }

        $this->distributionBuilder->addYamlFile('docker-compose.yml', $dockerComposeFile);

    }

    private function buildDockerfile()
    {

        $dockerfile = StringBuilder::fromString('
            FROM php:7.4.10-fpm-buster
        ');

        $aptPackages = 'unzip git';
        $extraInstallLinesArray = [];

        if ($this->generationContext->isFeatureEnabled(VipsFeature::class)) {
            $aptPackages .= 'libvips42 libvips-dev';
            $extraInstallLinesArray[] = '    pecl install vips && \\';
            $extraInstallLinesArray[] = '    echo "extension = vips.so" > /usr/local/etc/php/conf.d/vips.ini && \\';
        }

        if ($this->generationContext->isFeatureEnabled(RedisFeature::class)) {
            $extraInstallLinesArray[] = '    pecl install redis && docker-php-ext-enable redis && \\';
        }

        $extraInstallLines = implode("\n", $extraInstallLinesArray);
        $dockerfile->addString("
            # Install dependencies
            RUN apt-get update -y && \\
                apt-get install --no-install-recommends -y libicu-dev libxslt1-dev $aptPackages nginx-light supervisor procps && \\
                mkdir -p /var/log/supervisor && \\
                docker-php-ext-install intl bcmath pdo pdo_mysql mysqli xsl && \\
                echo 'memory_limit = 2G' > /usr/local/etc/php/conf.d/memory-limit-php.ini && \\
                echo 'post_max_size = 50M' > /usr/local/etc/php/conf.d/upload-limit-php.ini && \\
                echo 'upload_max_filesize = 50M' >> /usr/local/etc/php/conf.d/upload-limit-php.ini && \\
                {$extraInstallLines}
                rm -rf /var/lib/apt/lists/* && \\
                rm -Rf /usr/local/etc/php-fpm.*
        ");
        $dockerfile->addString("
            # install composer
            RUN curl --silent --show-error https://getcomposer.org/installer | php
            RUN mv composer.phar /usr/local/bin/composer
            RUN composer config --global cache-dir /composer_cache
        ");

        $dockerfile->addString("
            # application entrypoint
            ADD /deployment/local-dev/neos/entrypoint.sh /

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

        $this->distributionBuilder->addFile('Dockerfile.dev', $dockerfile);
        $this->distributionBuilder->addFile('deployment/config-files/php-fpm.conf', StringBuilder::fromString(file_get_contents(__DIR__ . '/php-fpm.conf')));
        $this->distributionBuilder->addFile('deployment/config-files/nginx.conf', StringBuilder::fromString(file_get_contents(__DIR__ . '/nginx.conf')));
    }
}
