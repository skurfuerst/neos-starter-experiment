<?php

declare(strict_types=1);

namespace Neos\Starter\Features\Packages\Seo;


use Neos\Starter\Features\AbstractFeature;

class SeoFeature extends AbstractFeature
{
    public function registerHooksBeforeActivation()
    {
        $this->distributionBuilder->sitePackage()->addSuperTypeProcessor(function (string $nodeTypeName, array $superTypes) {
            $superTypes['Neos.Neos:Document'] = null;
            return $superTypes;
        });
    }

    public function activate()
    {
        $this->addComposerRequirementFromProfile('neos/seo', $this->distributionBuilder->composerJson());
        $this->addComposerRequirementFromProfile('yoast/seo', $this->distributionBuilde->composerJson());

        /*$this->distributionBuilder->sitePackage()->addNodeType('Document.Foo', [
            "{$configuration->getSitePackageKey()}:Document.Foo" => [
                'abstract' => true,
                'constraints' => []
            ]
        ]);

        $projectBuilder->sitePackage()->addFusion('Root.fusion', '');*/
    }

    public function deactivate()
    {
        $this->distributionBuilder->sitePackage()->siteExport()->removeProperties('titleOverride', 'canonicalLink', 'metaRobotsNoindex', 'openGraphType', 'openGraphTitle', 'openGraphDescription', 'openGraphImage', 'metaDescription', 'metaKeywords', 'metaRobotsNoindex', 'metaRobotsNofollow', 'twitterCardType', 'twitterCardCreator', 'twitterCardTitle', 'twitterCardDescription', 'twitterCardImage', 'xmlSitemapChangeFrequency', 'xmlSitemapPriority');
    }
}
