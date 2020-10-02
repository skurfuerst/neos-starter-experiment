<?php
declare(strict_types=1);

namespace Neos\Starter\Api;

use Neos\Flow\Annotations as Flow;
use Neos\Starter\Api\Dto\Features;
use Neos\Starter\Api\Dto\ProfileName;
use Neos\Starter\Api\Dto\ProjectName;

/**
 * Main configuration DTO class; i.e. the main interface from the outside world
 *
 * @Flow\Proxy(false)
 */
class Configuration implements \JsonSerializable
{

    private ProjectName $projectName;

    private ProfileName $profileName;

    private Features $features;

    /**
     * @return ProfileName
     */
    public function getProfileName(): ProfileName
    {
        return $this->profileName;
    }

    /**
     * @return ProjectName
     */
    public function getProjectName(): ProjectName
    {
        return $this->projectName;
    }

    /**
     * @return Features
     */
    public function getFeatures(): Features
    {
        return $this->features;
    }

    public static function fromArray(array $in): self
    {
        $config = new self();
        $config->projectName = ProjectName::fromString($in['projectName']);
        $config->profileName = isset($in['profileName']) ? ProfileName::fromString($in['profileName']) : ProfileName::latest();
        $config->features = Features::fromArray($in['features']);

        return $config;
    }

    public function jsonSerialize()
    {
        return [
            'projectName' => $this->projectName,
            'profileName' => $this->profileName,
            'features' => $this->features,
        ];
    }

    public function getSitePackageKey(): string
    {
        return $this->projectName->toPackageKey();
    }

    public function getSiteClassNamePrefix(): string
    {
        return $this->projectName->toClassNamePrefix();
    }

    public function getSiteComposerKey(): string
    {
        return $this->projectName->toComposerKey();
    }
}
