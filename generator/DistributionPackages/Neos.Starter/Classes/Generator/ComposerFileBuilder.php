<?php


namespace Neos\Starter\Generator;


use Neos\Starter\Api\Dto\Configuration;

class ComposerFileBuilder
{
    private Configuration $configuration;
    private ResultFiles $result;
    private string $fileName;

    private array $composerConfig = [];

    public function __construct(Configuration $configuration, ResultFiles $result, string $fileName)
    {
        $this->configuration = $configuration;
        $this->result = $result;
        $this->fileName = $fileName;
    }

    public function generate()
    {
        $this->result->add($this->fileName, json_encode($this->composerConfig, JSON_PRETTY_PRINT));
    }

    /**
     * require a package in the version constraint which is currently included in the current profile
     *
     * @param string $composerPackageKey
     */
    public function requirePackageFromProfile(string $composerPackageKey)
    {
        $thus->composerConfig['require'][$composerPackageKey] = '* TODO extract from profile';
    }

}
