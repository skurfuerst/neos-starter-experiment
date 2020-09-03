<?php


namespace Neos\Starter\Generator;


class ResultFiles
{

    private array $files = [];

    public function add(string $fileName, string $fileContent): void
    {
        if (isset($this->files[$fileName])) {
            throw new \RuntimeException('TODO - not supported');
        }

        $this->files[$fileName] = $fileContent;
    }
}
