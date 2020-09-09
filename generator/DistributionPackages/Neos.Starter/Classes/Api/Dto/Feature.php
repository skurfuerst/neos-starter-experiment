<?php
declare(strict_types=1);

namespace Neos\Starter\Api\Dto;

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Proxy(false)
 * @api
 */
final class Feature implements \JsonSerializable
{
    private string $featureName;

    private function __construct(string $featureName)
    {
        $this->featureName = $featureName;
    }


    public static function fromString(string $featureName): self
    {
        return new self($featureName);
    }

    public static function fromArray(array $in): self
    {
        return new self(
            $in['name']
        );
    }

    public function equals(Feature $other): bool
    {
        return $this->featureName === $other->featureName;
    }

    public function jsonSerialize()
    {
        return [
            'name' => $this->featureName
        ];
    }

    public function __toString(): string
    {
        return 'Package List Item:' . $this->featureName;
    }

    public function getClassName(): string
    {
        return 'Neos\\Starter\\Features\\' . $this->featureName . '\\' . $this->featureName . 'Feature';
    }
}
