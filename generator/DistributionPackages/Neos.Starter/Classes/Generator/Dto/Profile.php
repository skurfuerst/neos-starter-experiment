<?php


namespace Neos\Starter\Generator\Dto;


use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Proxy(false)
 */
class Profile
{
    private array $composerRequires;

    /**
     * Profile constructor.
     * @param array $composerRequires
     */
    private function __construct(array $composerRequires)
    {
        $this->composerRequires = $composerRequires;
    }


    public static function fromComposerJsonString(string $composerJsonString): self
    {
        $composerJsonFile = json_decode($composerJsonString, true);
        return new self($composerJsonFile['require']);
    }

    public function getVersionConstraintForComposerKey(string $composerPackageKey): string
    {
        return $this->composerRequires[$composerPackageKey];
    }


}
