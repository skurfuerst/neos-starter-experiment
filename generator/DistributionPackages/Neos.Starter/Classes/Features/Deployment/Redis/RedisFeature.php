<?php

declare(strict_types=1);

namespace Neos\Starter\Features\Deployment\Redis;

use Neos\Flow\Annotations as Flow;
use Neos\Starter\Api\Configuration;
use Neos\Starter\Features\AbstractFeature;
use Neos\Starter\Features\FeatureInterface;
use Neos\Starter\Generator\DistributionBuilder;
use Neos\Starter\Generator\GenerationContextInterface;
use Neos\Starter\Generator\StringBuilder;
use Neos\Starter\HookImpl\ComposerFileUtility;
use Neos\Starter\Utility\YamlWithComments;

/**
 * @Flow\Proxy(false)
 */
class RedisFeature extends AbstractFeature
{
    public function registerHooksBeforeActivation()
    {
    }

    public function activate()
    {
        $this->addComposerRequirementFromProfile('sandstorm/optimizedrediscachebackend', $this->distributionBuilder->sitePackage()->composerJson());
        $this->distributionBuilder->addYamlFile('Configuration/Caches.Redis.yaml', [
            // TODO: other caches
            'Neos_Fusion_Content' => [
                'backend' => 'Sandstorm\OptimizedRedisCacheBackend\OptimizedRedisCacheBackend',
                'backendOptions' => [
                    'hostname' => '%env:NEOS_REDIS_HOST%',
                    'port' => '%env:NEOS_REDIS_PORT%',
                    'database' => 11,
                    'defaultLifetime' => 0,
                ]
            ]
        ]);
    }

    public function deactivate()
    {
    }
}
