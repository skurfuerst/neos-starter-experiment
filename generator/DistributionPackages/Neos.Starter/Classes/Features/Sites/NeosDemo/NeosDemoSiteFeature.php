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
        $finder->notPath('Migrations');
        $finder->notPath('Configuration/Settings.yaml');
        $finder->notPath('Configuration/Policy.yaml');
        $finder->notPath('Readme.rst');

        $finder->notPath('NodeTypes.Document.Chapter.yaml');
        $finder->notPath('Chapter.xlf');
        $finder->notPath('Chapter.fusion');

        $singleLanguage = true;
        if ($singleLanguage) {
            $this->distributionBuilder->sitePackage()->siteExport()->onlyKeepSingleLanguageVariantAndRenameTo('en_US', '');
            $finder->notPath('Resources/Private/Fusion/Document/Fragment/Menu/Language.fusion');

            $contents = file_get_contents(__DIR__ . '/singleLanguage/Language.fusion');
            $contents = str_replace('Neos.Demo', $this->generationContext->getConfiguration()->getSitePackageKey(), $contents);
            $this->distributionBuilder->sitePackage()->addStringFile('Resources/Private/Fusion/Document/Fragment/Menu/Language.fusion', StringBuilder::fromString($contents));
        }

        foreach ($finder->files() as $file) {
            if ($file->getRelativePathname() === 'Resources/Private/Content/Sites.xml') {
                $this->distributionBuilder->sitePackage()->siteExport()->setInitialSiteXml($file->getContents());
            } else {
                $contents = $file->getContents();
                $contents = str_replace('Neos.Demo', $this->generationContext->getConfiguration()->getSitePackageKey(), $contents);
                $this->distributionBuilder->sitePackage()->addStringFile($file->getRelativePathname(), StringBuilder::fromString($contents));
            }
        }

        $neosDemoComposerJson = json_decode(file_get_contents($baseDirectory . '/composer.json'), true);
        foreach ($neosDemoComposerJson['require'] as $package => $version) {
            if ($package !== 'neos/neos') {
                $this->distributionBuilder->sitePackage()->composerJson()->requirePackage($package, $version);
            }
        }

        $readmeSnippet = file_get_contents(__DIR__ . '/README_frontend.md');
        $this->distributionBuilder->readme()->addSection($readmeSnippet);


        $this->distributionBuilder->sitePackage()->addConfiguration('Settings', 'translation', [
            'Neos' => [
                'Neos' => [
                    'userInterface' => [
                        'translation' => [
                            'autoInclude' => [
                                $this->generationContext->getConfiguration()->getSitePackageKey() => [
                                    'Main',
                                    'NodeTypes/*'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);
    }

    public function deactivate()
    {
    }
}
