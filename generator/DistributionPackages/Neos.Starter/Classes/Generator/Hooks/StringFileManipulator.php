<?php

namespace Neos\Starter\Generator\Hooks;


use Neos\Starter\Generator\StringBuilder;

interface StringFileManipulator
{
    public function transformStringFile(string $fileName, StringBuilder $fileContent): StringBuilder;
}
