<?php

declare(strict_types=1);

namespace Neos\Starter\Features\Neos;

use Neos\Flow\Annotations as Flow;
use Neos\Starter\Api\Configuration;
use Neos\Starter\Features\AbstractFeature;
use Neos\Starter\Features\FeatureInterface;
use Neos\Starter\Generator\DistributionBuilder;
use Neos\Starter\Generator\GenerationContextInterface;
use Neos\Starter\Generator\StringBuilder;
use Neos\Starter\Utility\YamlWithComments;

/**
 * @Flow\Proxy(false)
 */
class NeosFeature extends AbstractFeature
{
    public function registerHooksBeforeActivation()
    {
    }

    public function activate()
    {
        $readmeStart = file_get_contents(__DIR__ . '/README_start.md');
        $readmeStart = str_replace('{SITE_PACKAGE_KEY}', $this->generationContext->getConfiguration()->getSitePackageKey(), $readmeStart);
        $this->distributionBuilder->readme()->addSection($readmeStart);

        $this->distributionBuilder->composerJson()->merge(json_decode(file_get_contents(__DIR__ . '/composer.json'), true));

        $this->distributionBuilder->readme()->addSectionAtEnd(file_get_contents(__DIR__ . '/README_end.md'));

        $this->addComposerRequirementFromProfile('neos/neos', $this->distributionBuilder->composerJson());
    }

    public function deactivate()
    {
    }
}
