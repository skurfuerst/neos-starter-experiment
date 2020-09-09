<?php


namespace Neos\Starter\Generator;


use Neos\Starter\Api\Configuration;
use Neos\Utility\Arrays;

class ComposerFileBuilder
{
    private Configuration $configuration;
    private Result $result;
    private string $fileName;

    private array $composerConfig = [];

    public function __construct(Configuration $configuration, Result $result, string $fileName)
    {
        $this->configuration = $configuration;
        $this->result = $result;
        $this->fileName = $fileName;
    }

    public function merge(array $mergeWith): void
    {
        $this->composerConfig = Arrays::arrayMergeRecursiveOverrule($this->composerConfig, $mergeWith);
    }

    public function generate()
    {
        $this->result->addJsonFile($this->fileName, $this->composerConfig);
    }

    /**
     * require a package in the version constraint which is currently included in the current profile
     *
     * @param string $composerPackageKey
     */
    public function requirePackageFromProfile(string $composerPackageKey)
    {
        $this->composerConfig['require'][$composerPackageKey] = '* TODO extract from profile';
    }

}
