<?php
declare(strict_types=1);

namespace Neos\Starter\Api\Dto;

/**
 * Main configuration DTO class; i.e. the main interface from the outside world
 */
class Configuration
{

    private ProjectName $projectName;

    private ProfileName $profileName;

    private PackageList $activatedPackages;

    /**
     * @return ProfileName
     */
    public function getProfileName(): ProfileName
    {
        return $this->profileName;
    }

    /**
     * @return PackageList
     */
    public function getActivatedPackages(): PackageList
    {
        return $this->activatedPackages;
    }

    public function getSitePackageKey(): string
    {

    }


}
