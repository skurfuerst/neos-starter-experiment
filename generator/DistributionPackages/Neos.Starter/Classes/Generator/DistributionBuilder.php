<?php


namespace Neos\Starter\Generator;


use Neos\Starter\Api\Dto\Configuration;
use Neos\Starter\Generator\Dto\Profile;

class DistributionBuilder
{
    private Configuration $configuration;
    private ResultFiles $result;

    private ComposerFileBuilder $composerJson;
    private PackageBuilder $sitePackage;
    private ReadmeBuilder $readme;

    private function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
        $this->result = new ResultFiles();

        $this->composerJson = new ComposerFileBuilder($this->configuration, $this->result, 'composer.json');
        $this->sitePackage = new PackageBuilder($this->configuration, $this->result);
        $this->readme = new ReadmeBuilder($this->configuration, $this->result);
    }


    public static function createBasedOnConfiguration(Configuration $configuration): self
    {
        return new self($configuration);
    }

    public function composerJson(): ComposerFileBuilder
    {
        return $this->composerJson;
    }

    public function sitePackage(): PackageBuilder
    {
        return $this->sitePackage;
    }

    public function readme(): ReadmeBuilder
    {
        return $this->readme;
    }

    public function generate(): ResultFiles
    {
        $this->composerJson->generate();
        $this->sitePackage->generate();
        $this->readme->generate();

        return $this->result;
    }

    public function addFile(string $fileName, string $fileContent)
    {
        $this->result->add($fileName, $fileContent);
    }
}
