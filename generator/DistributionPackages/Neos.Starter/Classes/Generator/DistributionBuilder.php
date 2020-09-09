<?php


namespace Neos\Starter\Generator;

use Neos\Flow\Annotations as Flow;
use Neos\Starter\Api\Configuration;
use Neos\Starter\Utility\YamlWithComments;

/**
 * @Flow\Proxy(false)
 */
class DistributionBuilder
{
    private Configuration $configuration;
    private Result $result;

    private ComposerFileBuilder $composerJson;
    private PackageBuilder $sitePackage;
    private ReadmeBuilder $readme;

    private function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
        $this->result = new Result();

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

    public function generate(): Result
    {
        $this->composerJson->generate();
        $this->sitePackage->generate();
        $this->readme->generate();

        return $this->result;
    }

    public function addYamlFile(string $fileName, array $fileContent)
    {
        $this->result->addYamlFile($fileName, $fileContent);
    }

    public function addFile(string $fileName, StringBuilder $fileContent)
    {
        $this->result->addStringFile($fileName, $fileContent);
    }
}
