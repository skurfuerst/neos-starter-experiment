<?php


namespace Neos\Starter\Api\Dto;


/**
 * @Flow\Proxy(false)
 * @api
 */
final class PackageListItem implements \JsonSerializable
{
    private string $composerName;

    private function __construct(string $composerName)
    {
        $this->composerName = $composerName;
    }


    public static function fromString(string $composerName): self
    {
        return new self($composerName);
    }

    public function equals(PackageListItem $other): bool
    {
        return $this->composerName === $other->composerName;
    }

    public function jsonSerialize()
    {
        return [
            'composerName' => $this->composerName
        ];
    }

    public function __toString(): string
    {
        return 'Package List Item:' . $this->composerName;
    }
}
