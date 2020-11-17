<?php

declare(strict_types=1);

namespace Neos\Starter\Features\Frontend\WebpackBuildSetup;

use Gedmo\Tree\Mapping\Driver\Yaml;
use Neos\Flow\Annotations as Flow;
use Neos\Starter\Api\Configuration;
use Neos\Starter\Features\AbstractFeature;
use Neos\Starter\Features\FeatureInterface;
use Neos\Starter\Generator\DistributionBuilder;
use Neos\Starter\Generator\GenerationContextInterface;
use Neos\Starter\Generator\StringBuilder;
use Neos\Starter\Utility\StringOutdenter;
use Neos\Starter\Utility\YamlWithComments;
use Ramsey\Uuid\Uuid;

/**
 * @Flow\Proxy(false)
 */
class WebpackBuildSetupFeature extends AbstractFeature
{
    public function registerHooksBeforeActivation()
    {
    }

    public function activate()
    {
        // Taken from Neos.Demo
        $files = [
            '.editorconfig',
            '.eslintignore',
            '.eslintrc',
            '.gitignore',
            '.jshintrc',
            '.nvmrc',
            '.prettierignore',
            '.prettierrc',
            '.stylelintrc',
            '.yarnclean',
            'babel.config.js',
            'package.json',
            'postcss.config.js',
            'webpack.config.js',
            'webpack.packages.js',
        ];
        foreach ($files as $file) {
            $this->distributionBuilder->sitePackage()->addStringFile($file, StringBuilder::fromFileContents(__DIR__ . '/' . $file));
        }

        $readmeSnippet = file_get_contents(__DIR__ . '/README_frontend.md');
        $this->distributionBuilder->readme()->addSection($readmeSnippet);

    }

    public function deactivate()
    {
    }
}
