<?php
declare(strict_types=1);

namespace Neos\Starter\Api\Dto;

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Proxy(false)
 * @api
 */
final class ProjectName implements \JsonSerializable
{
    private string $name;

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function fromString(string $name): self
    {
        return new self($name);
    }

    public function equals(ProjectName $other): bool
    {
        return $this->name === $other->name;
    }

    public function jsonSerialize()
    {
        return $this->name;
    }

    public function __toString(): string
    {
        return 'ProjectName:' . $this->name;
    }

    public function toPackageKey(): string
    {
        return preg_replace('/[^a-zA-Z0-9]/', '.', $this->name);
    }

    public function toComposerKey()
    {
        return strtolower(preg_replace('/[^a-zA-Z0-9]/', '/', $this->name));
    }
}
