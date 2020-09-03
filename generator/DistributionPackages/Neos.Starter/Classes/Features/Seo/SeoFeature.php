<?php

class SeoFeature
{

    public function add(\Neos\Starter\Api\Dto\Configuration $configuration, \Neos\Starter\Generator\DistributionBuilder $projectBuilder)
    {
        $projectBuilder->sitePackage()->composerJson()->requirePackageFromProfile('neos/seo');
        $projectBuilder->sitePackage()->composerJson()->requirePackageFromProfile('yoast/seo TODO');

        $projectBuilder->sitePackage()->addNodeType('Document.Foo', "
          '{$configuration->getSitePackageKey()}:Document.Foo':
            abstract: true
        ");

        $projectBuilder->sitePackage()->addFusion('Root.fusion', '');
    }

    public function remove(\Neos\Starter\Api\Dto\Configuration $configuration, \Neos\Starter\Generator\DistributionBuilder $projectBuilder)
    {
        $projectBuilder->sitePackage()->siteExport()->removeProperties('titleOverride', 'canonicalLink', 'metaRobotsNoindex', 'openGraphType', 'openGraphTitle', 'openGraphDescription', 'openGraphImage', 'metaDescription', 'metaKeywords', 'metaRobotsNoindex', 'metaRobotsNofollow', 'twitterCardType', 'twitterCardCreator', 'twitterCardTitle', 'twitterCardDescription', 'twitterCardImage', 'xmlSitemapChangeFrequency', 'xmlSitemapPriority');
    }
}
