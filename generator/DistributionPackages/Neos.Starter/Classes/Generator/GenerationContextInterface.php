<?php


namespace Neos\Starter\Generator;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\ObjectManagement\ObjectManagerInterface;
use Neos\Flow\Reflection\ReflectionService;
use Neos\Starter\Api\Configuration;
use Neos\Starter\Features\FeatureInterface;
use Neos\Starter\Generator\Dto\Profile;
use Neos\Starter\Generator\Hooks\JsonFileManipulator;
use Neos\Starter\Generator\Hooks\StringFileManipulator;
use Neos\Starter\Generator\Hooks\YamlFileManipulator;
use Neos\Starter\Utility\YamlWithComments;

interface GenerationContextInterface
{
    public function getConfiguration(): Configuration;
    public function getCurrentlyActiveProfile(): Profile;

    public function isFeatureEnabled(string $class);
}
