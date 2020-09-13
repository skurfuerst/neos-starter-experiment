<?php


namespace Neos\Starter\Generator;

use Neos\Flow\Annotations as Flow;
use Neos\Starter\Api\Configuration;
use Neos\Starter\Generator\Hooks\JsonFileManipulator;
use Neos\Starter\Generator\Hooks\StringFileManipulator;
use Neos\Starter\Generator\Hooks\YamlFileManipulator;
use Neos\Starter\Utility\YamlWithComments;

/**
 * @Flow\Proxy(false)
 */
class DistributionBuilder
{
    private GenerationContextInterface $generator;
    private Result $result;

    private ComposerFileBuilder $composerJson;
    private PackageBuilder $sitePackage;
    private ReadmeBuilder $readme;

    public function __construct(GenerationContextInterface $generator)
    {
        $this->generator = $generator;
        $this->result = new Result();

        $this->composerJson = new ComposerFileBuilder($this->generator, $this->result, 'composer.json');
        $this->sitePackage = new PackageBuilder($this->generator, $this->result);
        $this->readme = new ReadmeBuilder($this->generator, $this->result);
    }

    public function onStringFile(StringFileManipulator $manipulator): void
    {
        $this->result->onStringFile($manipulator);
    }

    public function onYamlFile(YamlFileManipulator $manipulator): void
    {
        $this->result->onYamlFile($manipulator);
    }
    public function onJsonFile(JsonFileManipulator $manipulator): void
    {
        $this->result->onJsonFile($manipulator);
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
