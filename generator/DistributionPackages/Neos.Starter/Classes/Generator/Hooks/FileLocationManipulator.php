<?php

namespace Neos\Starter\Generator\Hooks;


interface FileLocationManipulator
{
    public function transformFileName(string $fileName): string;
}
