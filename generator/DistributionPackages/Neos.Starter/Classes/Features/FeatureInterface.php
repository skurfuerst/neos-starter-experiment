<?php

declare(strict_types=1);

namespace Neos\Starter\Features;


use Neos\Starter\Api\Configuration;
use Neos\Starter\Generator\DistributionBuilder;

interface FeatureInterface
{

    public function registerHooksBeforeActivation(Configuration $configuration, DistributionBuilder $projectBuilder);

    public function activate(Configuration $configuration, DistributionBuilder $distribution);

    public function deactivate(Configuration $configuration, DistributionBuilder $projectBuilder);
}
