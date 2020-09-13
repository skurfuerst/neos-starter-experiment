<?php

declare(strict_types=1);

namespace Neos\Starter\Features\Deployment\Vips;

use Neos\Flow\Annotations as Flow;
use Neos\Starter\Api\Configuration;
use Neos\Starter\Features\AbstractFeature;
use Neos\Starter\Features\FeatureInterface;
use Neos\Starter\Generator\ComposerFileBuilder;
use Neos\Starter\Generator\DistributionBuilder;
use Neos\Starter\Generator\StringBuilder;
use Neos\Starter\Utility\YamlWithComments;

/**
 * @Flow\Proxy(false)
 */
class VipsFeature extends AbstractFeature
{
    public function registerHooksBeforeActivation()
    {
    }

    public function activate()
    {
        $this->addComposerRequirementFromProfile('rokka/imagine-vips', $this->distributionBuilder->sitePackage()->composerJson());
        $this->distributionBuilder->composerJson()->merge([
            'config' => [
                'platform' => [
                    'ext-vips' => '1.0.9'
                ]
            ]
        ]);

        $this->distributionBuilder->addYamlFile('Configuration/Settings.vips.yaml', [
            'Neos' => [
                'Imagine' => [
                    'driver' => 'Vips',
                    'enabledDrivers' => [
                        'Vips' => true,
                        'Gd' => true,
                        'Imagick' => true,
                    ]
                ],
                'Media' => [
                    'image' => [
                        'defaultOptions##' => YamlWithComments::comment('The Vips driver does not support interlace'),
                        'defaultOptions' => [
                            'interlace' => null
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
