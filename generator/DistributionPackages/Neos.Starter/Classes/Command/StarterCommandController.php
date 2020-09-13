<?php

declare(strict_types=1);

namespace Neos\Starter\Command;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\CommandController;
use Neos\Starter\Api\Configuration;
use Neos\Starter\Generator\Generator;

class StarterCommandController extends CommandController
{

    public function kickstartCommand(string $manifestFile, string $outputFolder)
    {
        $manifestFileContents = json_decode(file_get_contents($manifestFile), true);
        $configuration = Configuration::fromArray($manifestFileContents);

        $generator = new Generator($configuration);
        $result = $generator->generate();
        $result->writeToFolder($outputFolder);
    }
}
