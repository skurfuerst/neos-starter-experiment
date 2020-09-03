<?php


namespace Neos\Starter\Generator;


use Neos\Starter\Api\Dto\Configuration;
use Symfony\Component\Yaml\Yaml;

class PackageBuilder
{

    private Configuration $configuration;
    private ResultFiles $result;
    private ComposerFileBuilder $composerJson;
    private SiteExportManipulator $siteExport;

    public function __construct(Configuration $configuration, ResultFiles $result)
    {
        $this->configuration = $configuration;
        $this->result = $result;
        $this->composerJson = new ComposerFileBuilder($this->configuration, $this->result, 'DistributionPackages/' . $configuration->getSitePackageKey() . '/composer.json');
        $this->siteExport = new SiteExportManipulator($this->configuration, $this->result, 'DistributionPackages/' . $configuration->getSitePackageKey() . '/Resources/Private/Content.xml');
    }


    public function composerJson(): ComposerFileBuilder
    {
        return $this->composerJson;
    }

    public function addNodeType(string $fileNamePart, string $fileContent): void
    {
        Yaml::parse($fileContent); // throws exception if invalid YAML
        $this->result->add('Configuration/NodeTypes.' . $fileNamePart . '.yaml', $fileContent);
    }

    public function addFusion(string $pathAndFileName, string $fileContent): void
    {

    }

    public function addConfiguration(string $type, string $fileNamePart, string $fileContent): void
    {
        $this->result->add("Configuration/{$type}.${fileNamePart}.yaml", $fileContent);
    }

    public function generate(): void
    {
        $this->composerJson->generate();
    }

    public function siteExport(): SiteExportManipulator
    {
        return $this->siteExport;
    }


}
