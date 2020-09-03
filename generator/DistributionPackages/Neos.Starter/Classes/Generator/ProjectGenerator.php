<?php


namespace Neos\Starter\Generator;


use Neos\Starter\Api\Dto\Configuration;
use Neos\Starter\Generator\Dto\GeneratedProject;

/**
 * @Flow\Scope("singleton")
 * @api
 */
final class ProjectGenerator
{

    protected ProfileLoader $profileLoader;

    public function generate(Configuration $configuration): GeneratedProject
    {
        $profile = $this->profileLoader->load($configuration->getProfileName());
        $profile->ensureConfigurationMatchesProfile($configuration);

        $packagesToRemove = $profile->calculatePackagesToRemove($configuration);

        //$configuration->getActivatedPackages()->
    }
}
