<?php

namespace Neos\Starter\Generator\Hooks;


interface JsonFileManipulator
{
    public function transformJsonFile(string $fileName, array $fileContent): array;
}
