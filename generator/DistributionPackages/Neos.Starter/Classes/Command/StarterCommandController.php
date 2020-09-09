<?php

declare(strict_types=1);

namespace Neos\Starter\Command;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\CommandController;
use Neos\Starter\Api\Configuration;
use Neos\Starter\Features\FeatureInterface;
use Neos\Starter\Generator\DistributionBuilder;

class StarterCommandController extends CommandController
{

    public function kickstartCommand(string $manifestFile, string $outputFolder)
    {
        $manifestFileContents = json_decode(file_get_contents($manifestFile), true);
        $configuration = Configuration::fromArray($manifestFileContents);
        $distributionBuilder = DistributionBuilder::createBasedOnConfiguration($configuration);

        $instanciatedFeatures = [];
        foreach ($configuration->getFeatures() as $feature) {
            $featureClassName = $feature->getClassName();
            $instanciatedFeatures[] = new $featureClassName();
        }

        foreach ($instanciatedFeatures as $instanciatedFeature) {
            assert($instanciatedFeature instanceof FeatureInterface);
            $instanciatedFeature->registerHooksBeforeActivation($configuration, $distributionBuilder);
        }

        foreach ($instanciatedFeatures as $instanciatedFeature) {
            assert($instanciatedFeature instanceof FeatureInterface);
            $instanciatedFeature->activate($configuration, $distributionBuilder);
        }

        // TODO: non-instanciated features

        $result = $distributionBuilder->generate();
        $result->writeToFolder($outputFolder);
    }
}
