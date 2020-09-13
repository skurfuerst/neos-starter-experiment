<?php

declare(strict_types=1);

namespace Neos\Starter\Features;


use Neos\Starter\Api\Configuration;
use Neos\Starter\Generator\ComposerFileBuilder;
use Neos\Starter\Generator\DistributionBuilder;
use Neos\Starter\Generator\GenerationContextInterface;

abstract class AbstractFeature implements FeatureInterface
{

    protected GenerationContextInterface $generationContext;

    protected DistributionBuilder $distributionBuilder;

    protected function __construct(GenerationContextInterface $generationContext, DistributionBuilder $distributionBuilder)
    {
        $this->generationContext = $generationContext;
        $this->distributionBuilder = $distributionBuilder;
    }

    public static function create(GenerationContextInterface $generationContext, DistributionBuilder $distributionBuilder): self
    {
        return new static($generationContext, $distributionBuilder);
    }

    public function addComposerRequirementFromProfile(string $composerPackageKey, ComposerFileBuilder $composerFileBuilder): void
    {
        $versionConstraint = $this->generationContext->getCurrentlyActiveProfile()->getVersionConstraintForComposerKey($composerPackageKey);
        $composerFileBuilder->requirePackage($composerPackageKey, $versionConstraint);
    }

}
