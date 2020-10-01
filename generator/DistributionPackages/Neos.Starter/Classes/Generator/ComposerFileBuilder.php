<?php


namespace Neos\Starter\Generator;


use Neos\Starter\Api\Configuration;
use Neos\Utility\Arrays;

class ComposerFileBuilder
{
    private GenerationContextInterface $generator;
    private Result $result;
    private string $fileName;

    private array $composerConfig = [];

    public function __construct(GenerationContextInterface $generator, Result $result, string $fileName)
    {
        $this->generator = $generator;
        $this->result = $result;
        $this->fileName = $fileName;
    }

    public function merge(array $mergeWith): void
    {
        $this->composerConfig = Arrays::arrayMergeRecursiveOverrule($this->composerConfig, $mergeWith);
    }

    public function generate()
    {
        if (empty($this->composerConfig['require-dev'])) {
            $this->composerConfig['require-dev'] = new \stdClass();
        }
        $this->result->addJsonFile($this->fileName, $this->composerConfig);
    }

    /**
     * @param string $composerPackageKey
     * @param string $versionConstraint
     */
    public function requirePackage(string $composerPackageKey, string $versionConstraint)
    {
        $this->composerConfig['require'][$composerPackageKey] = $versionConstraint;
    }

}
