<?php


namespace Neos\Starter\Api\Dto;


final class PackageList implements \JsonSerializable
{
    /**
     * @var array|PackageListItem[]
     */
    protected $packages = [];

    private function __construct(array $packages)
    {
        foreach ($packages as $package) {
            assert($package instanceof PackageListItem);
        }

        $this->packages = $packages;
    }

    public static function createEmpty(): self
    {
        return new self([]);
    }

    public static function fromArray(array $array): self
    {
        $packages = [];
        foreach ($array as $rawPackageListItem) {
            $packages[] = PackageListItem::fromString($rawPackageListItem);
        }

        return new self($packages);
    }


    public function jsonSerialize(): array
    {
        return $this->packages;
    }

    public function subtract(PackageList $other): self
    {

    }

    public function isEmpty(): bool
    {

    }
}
