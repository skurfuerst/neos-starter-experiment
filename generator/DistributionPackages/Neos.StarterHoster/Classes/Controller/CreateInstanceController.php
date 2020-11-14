<?php

declare(strict_types=1);

namespace Neos\StarterHoster\Controller;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;
use Neos\Starter\Api\Configuration;
use Neos\Starter\Generator\Generator;
use Neos\Utility\Files;
use Ramsey\Uuid\Uuid;

class CreateInstanceController extends ActionController
{

    public function indexAction()
    {
        // TODO: ex1.json from somewhere else
        $manifestFileContents = json_decode(file_get_contents('examples/ex1.json'), true);
        $outputFolder = 'outtest';
        Uuid::uuid4()->toString();

        $configuration = Configuration::fromArray($manifestFileContents);
        $generator = new Generator($configuration);
        $result = $generator->generate();
        $outputFolderDistDir = Files::concatenatePaths([$outputFolder, 'dist']);
        $result->writeToFolder($outputFolderDistDir);
    }
}
