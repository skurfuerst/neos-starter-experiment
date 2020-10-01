<?php


namespace Neos\Starter\Generator;


use Neos\Starter\Generator\Hooks\FileLocationManipulator;
use Neos\Starter\Generator\Hooks\JsonFileManipulator;
use Neos\Starter\Generator\Hooks\StringFileManipulator;
use Neos\Starter\Generator\Hooks\YamlFileManipulator;
use Neos\Starter\Utility\YamlWithComments;
use Neos\Utility\Files;

class Result
{

    private array $files = [];

    private array $permissions = [];

    /**
     * @var YamlFileManipulator[]
     */
    private array $yamlFileManipulators = [];

    /**
     * @var JsonFileManipulator[]
     */
    private array $jsonFileManipulators = [];


    /**
     * @var StringFileManipulator[]
     */
    private array $stringFileManipulators = [];

    /**
     * @var FileLocationManipulator[]
     */
    private array $fileLocationManipulators = [];

    public function addYamlFile(string $fileName, array $fileContent): void
    {
        foreach ($this->yamlFileManipulators as $yamlFileManipulator) {
            $fileContent = $yamlFileManipulator->transformYamlFile($fileName, $fileContent);
        }
        $this->addStringFile($fileName, YamlWithComments::dump($fileContent));
    }

    public function onYamlFile(YamlFileManipulator $manipulator)
    {
        $this->yamlFileManipulators[] = $manipulator;
    }

    public function addJsonFile(string $fileName, array $fileContent): void
    {
        foreach ($this->jsonFileManipulators as $jsonFileManipulator) {
            $fileContent = $jsonFileManipulator->transformJsonFile($fileName, $fileContent);
        }
        $this->addStringFile($fileName, StringBuilder::fromString(json_encode($fileContent, JSON_PRETTY_PRINT)));
    }

    public function onJsonFile(JsonFileManipulator $manipulator)
    {
        $this->jsonFileManipulators[] = $manipulator;
    }

    public function addStringFile(string $fileName, StringBuilder $fileContent): void
    {
        foreach ($this->stringFileManipulators as $stringFileManipulator) {
            $fileContent = $stringFileManipulator->transformStringFile($fileName, $fileContent);
        }

        foreach ($this->fileLocationManipulators as $fileLocationManipulator) {
            $fileName = $fileLocationManipulator->transformFileName($fileName);
        }

        if (isset($this->files[$fileName])) {
            throw new \RuntimeException('TODO - not supported');
        }

        $this->files[$fileName] = $fileContent->build();
    }

    public function onStringFile(StringFileManipulator $manipulator)
    {
        $this->stringFileManipulators[] = $manipulator;
    }

    public function writeToFolder(string $baseDirectory)
    {
        foreach ($this->files as $fileName => $fileContent) {
            $pathAndFilename = Files::concatenatePaths([$baseDirectory, $fileName]);
            Files::createDirectoryRecursively(dirname($pathAndFilename));

            file_put_contents($pathAndFilename, $fileContent);
        }

        foreach ($this->permissions as $fileName => $permission) {
            $pathAndFilename = Files::concatenatePaths([$baseDirectory, $fileName]);
            chmod($pathAndFilename, $permission);
        }
    }

    public function setPermissions(string $fileName, int $permissions)
    {
        $this->permissions[$fileName] = $permissions;
    }
}
