<?php

class SeoFeature
{
    public function prepare(\Neos\Starter\Api\Dto\Configuration $configuration, \Neos\Starter\Generator\DistributionBuilder $projectBuilder)
    {
        $projectBuilder->sitePackage()->addSuperTypeProcessor(function (string $nodeTypeName, array $superTypes) {
            $superTypes['Neos.Neos:Document'] = null;
            return $superTypes;
        });
    }

    public function add(\Neos\Starter\Api\Dto\Configuration $configuration, \Neos\Starter\Generator\DistributionBuilder $projectBuilder)
    {
        $projectBuilder->sitePackage()->composerJson()->requirePackageFromProfile('neos/seo');
        $projectBuilder->sitePackage()->composerJson()->requirePackageFromProfile('yoast/seo TODO');

        $projectBuilder->sitePackage()->addNodeType('Document.Foo', "
          '{$configuration->getSitePackageKey()}:Document.Foo':
            abstract: true
            superTypes:
                # TODO: MAYBE DO NOT USE EEL HERE, BUT FIND ANOTHER GOOD WAY TO INJECT COMMENTS INTO YAML; AND THEN USE A BETTER PREPARED MODEL
              \${Neos.Starter.documentSuperTypes()')}
            constraints:
              nodeTypes:
                \${Neos.Starter.nodeTypeConstraints()')}
        ");

        $projectBuilder->sitePackage()->addFusion('Root.fusion', '');
    }

    public function remove(\Neos\Starter\Api\Dto\Configuration $configuration, \Neos\Starter\Generator\DistributionBuilder $projectBuilder)
    {
        $projectBuilder->sitePackage()->siteExport()->removeProperties('titleOverride', 'canonicalLink', 'metaRobotsNoindex', 'openGraphType', 'openGraphTitle', 'openGraphDescription', 'openGraphImage', 'metaDescription', 'metaKeywords', 'metaRobotsNoindex', 'metaRobotsNofollow', 'twitterCardType', 'twitterCardCreator', 'twitterCardTitle', 'twitterCardDescription', 'twitterCardImage', 'xmlSitemapChangeFrequency', 'xmlSitemapPriority');
    }
}
