<?php

declare(strict_types=1);

namespace Neos\Starter\Features\Neos;

use Neos\Flow\Annotations as Flow;
use Neos\Starter\Api\Configuration;
use Neos\Starter\Features\FeatureInterface;
use Neos\Starter\Generator\DistributionBuilder;
use Neos\Starter\Generator\StringBuilder;
use Neos\Starter\Utility\YamlWithComments;

/**
 * @Flow\Proxy(false)
 */
class NeosFeature implements FeatureInterface
{
    public function registerHooksBeforeActivation(Configuration $configuration, DistributionBuilder $projectBuilder)
    {
    }

    public function activate(Configuration $configuration, DistributionBuilder $distribution)
    {
        $readmeStart = file_get_contents(__DIR__ . '/README_start.md');
        $readmeStart = str_replace('{SITE_PACKAGE_KEY}', $configuration->getSitePackageKey(), $readmeStart);
        $distribution->readme()->addSection($readmeStart);

        $distribution->composerJson()->merge(json_decode(file_get_contents(__DIR__ . '/composer.json'), true));

        $distribution->readme()->addSectionAtEnd(file_get_contents(__DIR__ . '/README_end.md'));

        $distribution->addYamlFile('docker-compose.yml', [
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
                        'SITE_IMPORT_PACKAGE_KEY' => $configuration->getSitePackageKey(),
                        'ADMIN_USERNAME##' => YamlWithComments::comment('auto creation of admin user for Neos backend'),
                        'ADMIN_USERNAME' => 'admin',
                        'ADMIN_PASSWORD' => 'password',
                    ],
                    'volumes' => [
                        './composer.json:/app/composer.json:cached',
                        './composer.lock:/app/composer.lock:cached',
                        './DistributionPackages/:/app/DistributionPackages/:ro,cached',
                        YamlWithComments::comment('Content is writable to enable content dumps from inside the container'),
                        "./DistributionPackages/{$configuration->getSitePackageKey()}/Resources/Private/Content:/app/DistributionPackages/{$configuration->getSitePackageKey()}/Resources/Private/Content/:cached",
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
        ]);

        $distribution->addFile('Dockerfile.dev', StringBuilder::fromString(file_get_contents(__DIR__ . '/Dockerfile.dev')));
    }

    public function deactivate(Configuration $configuration, DistributionBuilder $projectBuilder)
    {
    }
}
