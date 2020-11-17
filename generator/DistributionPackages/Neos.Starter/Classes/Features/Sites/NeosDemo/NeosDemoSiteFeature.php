<?php

declare(strict_types=1);

namespace Neos\Starter\Features\Sites\NeosDemo;

use Neos\Flow\Annotations as Flow;
use Neos\Starter\Features\AbstractFeature;
use Neos\Starter\Generator\StringBuilder;
use Symfony\Component\Finder\Finder;

/**
 * @Flow\Proxy(false)
 */
class NeosDemoSiteFeature extends AbstractFeature
{
    public function registerHooksBeforeActivation()
    {
    }

    public function activate()
    {
        $baseDirectory = __DIR__ . '/../../../../../../subtrees/Neos.Demo';

        $finder = new Finder();
        $finder->ignoreDotFiles(false);
        $finder->files()->in($baseDirectory);
        $finder->notName('composer.json');
        $finder->notName('*Flickr*');
        $finder->notName('*Registration*');
        $finder->notName('*YouTube*');
        $finder->notPath('Classes');
        $finder->notPath('Private/Partials');
        $finder->notPath('Private/Templates/');
        $finder->notPath('Private/Content');
        $finder->notPath('Migrations');
        $finder->notPath('Configuration/Settings.yaml');
        $finder->notPath('Configuration/Policy.yaml');
        $finder->notPath('Readme.rst');

        foreach ($finder->files() as $file) {
            $contents = $file->getContents();
            $contents = str_replace('Neos.Demo', $this->generationContext->getConfiguration()->getSitePackageKey(), $contents);
            $this->distributionBuilder->sitePackage()->addStringFile($file->getRelativePathname(), StringBuilder::fromString($contents));
        }

        $readmeSnippet = file_get_contents(__DIR__ . '/README_frontend.md');
        $this->distributionBuilder->readme()->addSection($readmeSnippet);
    }

    public function deactivate()
    {
    }
}