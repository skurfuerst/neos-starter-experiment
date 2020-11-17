<?php

declare(strict_types=1);

namespace Neos\Starter\Features;


use Neos\Starter\Api\Configuration;
use Neos\Starter\Generator\DistributionBuilder;
use Neos\Starter\Generator\GenerationContextInterface;

interface FeatureInterface
{

    public static function create(GenerationContextInterface $generationContext, DistributionBuilder $distributionBuilder): self;

    public function registerHooksBeforeActivation();

    public function activate();

    public function deactivate();
}
