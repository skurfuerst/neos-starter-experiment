<?php


namespace Neos\Starter\Generator\Dto;


use Neos\Starter\Api\Dto\Configuration;
use Neos\Starter\Api\Dto\PackageList;
use Neos\Starter\Api\Dto\PackageListItem;

class Profile
{
    private PackageList $includedPackages;

    public function fromComposerJsonString(string $composerJsonString): self
    {
        $composerJsonFile = json_decode($composerJsonString, trur);
        $composerRequires = $composerJsonFile['require'];


    }

    public function ensureConfigurationMatchesProfile(Configuration $configuration): void
    {
        $packagesNotCoveredByProfile = $configuration->getActivatedPackages()->subtract($this->includedPackages);
        if ($packagesNotCoveredByProfile->isEmpty()) {
            throw new \RuntimeException('TODO: too many packages specified');
        }
    }

    public function calculatePackagesToRemove(Configuration $configuration): PackageList
    {

    }


}
