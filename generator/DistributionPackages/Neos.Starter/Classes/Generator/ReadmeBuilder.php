<?php


namespace Neos\Starter\Generator;


use Neos\Starter\Api\Configuration;

class ReadmeBuilder
{
    private GenerationContextInterface $generator;
    private Result $result;

    private StringBuilder $contents;

    /**
     * ReadmeBuilder constructor.
     * @param Configuration $configuration
     * @param Result $result
     */
    public function __construct(GenerationContextInterface $generator, Result $result)
    {
        $this->generator = $generator;
        $this->result = $result;
        $this->contents = StringBuilder::create();
    }


    /**
     * @param string $readmeSection
     */
    public function addSection(string $readmeSection)
    {
        $this->contents->addString($readmeSection);
    }

    public function addSectionAtEnd(string $readmeSection)
    {
        $this->contents->addString($readmeSection, 'end');
    }

    public function generate(): void
    {
        $this->result->addStringFile("README.md", $this->contents);
    }

}
