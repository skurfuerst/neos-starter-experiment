<?php


namespace Neos\Starter\Generator;


use Neos\Starter\Api\Dto\Configuration;

class ReadmeBuilder
{
    private Configuration $configuration;
    private ResultFiles $result;

    /**
     * ReadmeBuilder constructor.
     * @param Configuration $configuration
     * @param ResultFiles $result
     */
    public function __construct(Configuration $configuration, ResultFiles $result)
    {
        $this->configuration = $configuration;
        $this->result = $result;
    }


    /**
     * @param string $readmeSection
     */
    public function addSection(string $readmeSection)
    {

    }

    public function addSectionAtEnd(string $readmeSection)
    {

    }


}
