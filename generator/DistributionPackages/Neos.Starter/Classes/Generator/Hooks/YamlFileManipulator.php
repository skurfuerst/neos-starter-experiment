<?php

namespace Neos\Starter\Generator\Hooks;


interface YamlFileManipulator
{
    public function transformYamlFile(string $fileName, array $fileContent): array;
}
