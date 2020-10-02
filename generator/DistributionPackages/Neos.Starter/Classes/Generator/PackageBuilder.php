<?php


namespace Neos\Starter\Generator;


use Neos\Starter\Api\Configuration;
use Neos\Starter\Utility\YamlWithComments;

class PackageBuilder
{

    private GenerationContextInterface $generator;
    private Result $result;
    private ComposerFileBuilder $composerJson;
    private SiteExportManipulator $siteExport;

    private array $superTypeProcessors = [];

    public function __construct(GenerationContextInterface $generator, Result $result)
    {
        $this->generator = $generator;
        $this->result = $result;
        $this->composerJson = new ComposerFileBuilder($this->generator, $this->result, 'DistributionPackages/' . $generator->getConfiguration()->getSitePackageKey() . '/composer.json');
        $this->siteExport = new SiteExportManipulator($this->generator, $this->result, 'DistributionPackages/' . $generator->getConfiguration()->getSitePackageKey() . '/Resources/Private/Content/Sites.xml');
    }


    public function composerJson(): ComposerFileBuilder
    {
        return $this->composerJson;
    }

    public function addNodeType(string $fileNamePart, array $nodeTypeContent): void
    {
        // call superTypeProcessors
        foreach ($nodeTypeContent as $nodeTypeName => &$nodeTypeConfiguration) {
            $superTypes = $nodeTypeConfiguration['superTypes'] ?? [];
            foreach ($this->superTypeProcessors as $superTypeProcessor) {
                $superTypes = $superTypeProcessor($nodeTypeName, $superTypes);
            }

            if (count($superTypes)) {
                $nodeTypeConfiguration['superTypes'] = $superTypes;
            }
        }

        // TODO: maybe call nodeTypeConstraintProcessor

        $this->addConfiguration('NodeTypes', $fileNamePart, $nodeTypeContent);
    }

    public function addFusion(string $pathAndFileName, StringBuilder $fileContent): void
    {
        $this->result->addStringFile('DistributionPackages/' . $this->generator->getConfiguration()->getSitePackageKey() . "/Resources/Private/Fusion/$pathAndFileName", $fileContent);
    }

    public function addConfiguration(string $type, string $fileNamePart, array $fileContent): void
    {
        $this->result->addYamlFile('DistributionPackages/' . $this->generator->getConfiguration()->getSitePackageKey() . "/Configuration/{$type}.${fileNamePart}.yaml", $fileContent);
    }

    public function generate(): void
    {
        $this->composerJson->generate();
        $this->siteExport->generate();
    }

    public function siteExport(): SiteExportManipulator
    {
        return $this->siteExport;
    }

    public function addSuperTypeProcessor(\Closure $superTypeProcessor)
    {

        $this->superTypeProcessors[] = $superTypeProcessor;
    }


}
