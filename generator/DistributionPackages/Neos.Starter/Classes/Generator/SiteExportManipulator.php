<?php


namespace Neos\Starter\Generator;


class SiteExportManipulator
{
    private GenerationContextInterface $generator;
    private Result $result;
    private string $fileName;

    private string $siteXml;

    public function __construct(GenerationContextInterface $generator, Result $result, string $fileName)
    {
        $this->generator = $generator;
        $this->result = $result;
        $this->fileName = $fileName;
    }

    public function removeProperties(string ...$propertiesToRemove): void
    {

    }

    public function setInitialSiteXml(string $siteXml)
    {
        $this->siteXml = $siteXml;
    }

    public function generate()
    {
        $this->result->addStringFile($this->fileName, StringBuilder::fromString($this->siteXml));
    }
}
