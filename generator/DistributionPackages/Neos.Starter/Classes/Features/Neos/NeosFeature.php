<?php

declare(strict_types=1);

namespace Neos\Starter\Features\Neos;

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
class NeosFeature extends AbstractFeature
{
    public function registerHooksBeforeActivation()
    {
    }

    public function activate()
    {
        $readmeStart = file_get_contents(__DIR__ . '/README_start.md');
        $readmeStart = str_replace('{SITE_PACKAGE_KEY}', $this->generationContext->getConfiguration()->getSitePackageKey(), $readmeStart);
        $this->distributionBuilder->readme()->addSection($readmeStart);

        $this->distributionBuilder->composerJson()->merge(json_decode(file_get_contents(__DIR__ . '/composer.json'), true));
        $this->distributionBuilder->composerJson()->merge([
            'name' => $this->generationContext->getConfiguration()->getSiteComposerKey() . '-distribution'
        ]);

        $classNamePrefix = $this->generationContext->getConfiguration()->getSiteClassNamePrefix() . '\\';
        $this->distributionBuilder->sitePackage()->composerJson()->merge([
            'name' => $this->generationContext->getConfiguration()->getSiteComposerKey(),
            'type' => 'neos-site',
            'autoload' => [
                'psr-4' => [
                    $classNamePrefix => 'Classes'
                ]
            ],
            'extra' => [
                'neos' => [
                    'package-key' => $this->generationContext->getConfiguration()->getSitePackageKey(),
                ],
                'applied-flow-migrations' => [
                    "TYPO3.FLOW3-201201261636",
                    "TYPO3.Fluid-201205031303",
                    "TYPO3.FLOW3-201205292145",
                    "TYPO3.FLOW3-201206271128",
                    "TYPO3.FLOW3-201209201112",
                    "TYPO3.Flow-201209251426",
                    "TYPO3.Flow-201211151101",
                    "TYPO3.Flow-201212051340",
                    "TYPO3.TypoScript-130516234520",
                    "TYPO3.TypoScript-130516235550",
                    "TYPO3.TYPO3CR-130523180140",
                    "TYPO3.Flow-201310031523",
                    "TYPO3.Flow-201405111147",
                    "TYPO3.Neos-201407061038",
                    "TYPO3.Neos-201409071922",
                    "TYPO3.TYPO3CR-140911160326",
                    "TYPO3.Neos-201410010000",
                    "TYPO3.TYPO3CR-141101082142",
                    "TYPO3.Neos-20141113115300",
                    "TYPO3.Fluid-20141113120800",
                    "TYPO3.Flow-20141113121400",
                    "TYPO3.Fluid-20141121091700",
                    "TYPO3.Neos-20141218134700",
                    "TYPO3.Fluid-20150214130800",
                    "TYPO3.Neos-20150303231600",
                    "TYPO3.TYPO3CR-20150510103823",
                    "TYPO3.Flow-20151113161300",
                    "TYPO3.Form-20160601101500",
                    "TYPO3.Flow-20161115140400",
                    "TYPO3.Flow-20161115140430",
                    "Neos.Flow-20161124204700",
                    "Neos.Flow-20161124204701",
                    "Neos.Twitter.Bootstrap-20161124204912",
                    "Neos.Form-20161124205254",
                    "Neos.Flow-20161124224015",
                    "Neos.Party-20161124225257",
                    "Neos.Eel-20161124230101",
                    "Neos.Setup-20161124230842",
                    "Neos.Imagine-20161124231742",
                    "Neos.Media-20161124233100",
                    "Neos.Neos-20161125002322",
                    "Neos.ContentRepository-20161125012000",
                    "Neos.Fusion-20161125013710",
                    "Neos.Setup-20161125014759",
                    "Neos.Fusion-20161125104701",
                    "Neos.Neos-20161125104802",
                    "Neos.Neos-20161125122412",
                    "Neos.Flow-20161125124112",
                    "TYPO3.FluidAdaptor-20161130112935",
                    "Neos.Fusion-20161201202543",
                    "Neos.Neos-20161201222211",
                    "Neos.Fusion-20161202215034",
                    "Neos.Fusion-20161219092345",
                    "Neos.ContentRepository-20161219093512",
                    "Neos.Media-20161219094126",
                    "Neos.Neos-20161219094403",
                    "Neos.Neos-20161219122512",
                    "Neos.Fusion-20161219130100",
                    "Neos.Neos-20161220163741",
                    "Neos.Neos-20170115114620",
                    "Neos.Fusion-20170120013047",
                    "Neos.Flow-20170125103800",
                    "Neos.Seo-20170127154600",
                    "Neos.Flow-20170127183102",
                    "Neos.Fusion-20180211175500",
                    "Neos.Fusion-20180211184832",
                    "Neos.Flow-20180415105700"
                ]
            ]
        ]);

        $this->distributionBuilder->composerJson()->requirePackage($this->generationContext->getConfiguration()->getSiteComposerKey(), '@dev');

        $this->distributionBuilder->addFile('composer.lock', StringBuilder::fromString('{}'));

        $this->distributionBuilder->readme()->addSectionAtEnd(file_get_contents(__DIR__ . '/README_end.md'));

        $homepageNodeTypeName = $this->generationContext->getConfiguration()->getSitePackageKey() . ':Document.HomePage';
        $pageNodeTypeName = $this->generationContext->getConfiguration()->getSitePackageKey() . ':Document.Page';

/*        $this->distributionBuilder->sitePackage()->siteExport()->setInitialSiteXml('<?xml version="1.0" encoding="UTF-8"?>
<root>
 <site name="Site" state="1" siteResourcesPackageKey="' . $this->generationContext->getConfiguration()->getSitePackageKey() . '" siteNodeName="site">
  <nodes formatVersion="2.0">
   <node identifier="' . Uuid::uuid4()->toString() . '" nodeName="site">
    <variant sortingIndex="100" workspace="live" nodeType="' . $homepageNodeTypeName . '" version="1" removed="" hidden="" hiddenInIndex="">
     <dimensions>
     </dimensions>
     <accessRoles __type="array"/>
     <properties>
      <title __type="string">Home</title>
      <uriPathSegment __type="string">home</uriPathSegment>
     </properties>
    </variant>

    <node nodeName="main">
     <variant sortingIndex="100" workspace="live" nodeType="Neos.Neos:ContentCollection" version="1" removed="" hidden="" hiddenInIndex="">
      <dimensions>
      </dimensions>
      <accessRoles __type="array"/>
      <properties>
      </properties>
     </variant>
    </node>
  </nodes>
 </site>
</root>');
*/
        $this->addComposerRequirementFromProfile('neos/neos', $this->distributionBuilder->composerJson());
        $this->addComposerRequirementFromProfile('neos/neos-ui', $this->distributionBuilder->composerJson());
        $this->distributionBuilder->composerJson()->requirePackage('typo3fluid/fluid', '2.6.9'); // 2.6.10 breaks

        // Ensure the site package is loaded after neos/neos, so that it can override settings lateron.
        $this->distributionBuilder->sitePackage()->composerJson()->requirePackage('neos/neos', '*');
        $this->distributionBuilder->sitePackage()->composerJson()->requirePackage('neos/neos-ui', '*');

        $this->distributionBuilder->sitePackage()->addNodeType('Document.HomePage', [
            $homepageNodeTypeName . '##' => YamlWithComments::comment(StringOutdenter::outdent('
                The Homepage node is used for the root page only - and it should contain site-global properties,
                like a background color or the logo (if this should be configurable for an Editor).

                In case you want to build e.g. a shared footer, add an auto-created child node to the homepage node
                containing the footer elements.

                In Fusion, the homepage(=root) node can always be reached with ${q(site)}.
            ')),
            $homepageNodeTypeName => [
                'ui' => [
                    // TODO: add to the comment where the translation file can be found.
                    'label##' => YamlWithComments::comment('You can also use the special label "i18n", then the label is loaded from a translation file.'),
                    'label' => 'Homepage',
                    'icon' => 'globe',
                    'help' => [
                        'message##' => YamlWithComments::comment('the help message text supports markdown.'),
                        'message' => 'The homepage type is only used for the root page, containing global configuration.'
                    ]
                ],
                'superTypes' => [
                    $pageNodeTypeName => true
                ]
            ]
        ]);
    }

    public function deactivate()
    {
    }
}
