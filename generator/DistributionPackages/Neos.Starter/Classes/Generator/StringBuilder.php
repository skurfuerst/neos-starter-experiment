<?php


namespace Neos\Starter\Generator;

use Neos\Flow\Annotations as Flow;
use Neos\Utility\PositionalArraySorter;

/**
 * @Flow\Proxy(false)
 */
class StringBuilder
{

    private array $stringParts = [];

    public static function create(): self
    {
        return new self();
    }

    public static function fromString(string $string): self
    {
        $builder = self::create();
        $builder->addString($string);

        return $builder;
    }

    public function addString(string $string, string $position = null): self
    {
        $this->stringParts[] = [
            'string' => $string,
            'position' => $position
        ];

        return $this;
    }

    public function build(): string
    {
        $sortedParts = (new PositionalArraySorter($this->stringParts))->toArray();

        return implode("\n", array_map(fn($part) => $part['string'], $sortedParts));
    }


}
