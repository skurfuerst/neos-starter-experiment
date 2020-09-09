<?php
declare(strict_types=1);

namespace Neos\Starter\Api\Dto;
use Exception;
use Neos\Flow\Annotations as Flow;
use Traversable;

/**
 * @Flow\Proxy(false)
 */
final class Features implements \JsonSerializable, \IteratorAggregate
{
    /**
     * @var array|Feature[]
     */
    protected $features = [];

    private function __construct(array $features)
    {
        foreach ($features as $feature) {
            assert($feature instanceof Feature);
        }

        $this->features = $features;
    }

    public static function createEmpty(): self
    {
        return new self([]);
    }

    public static function fromArray(array $array): self
    {
        $features = [];
        foreach ($array as $rawFeature) {
            if (is_string($rawFeature)) {
                $features[] = Feature::fromString($rawFeature);
            } else {
                $features[] = Feature::fromArray($rawFeature);
            }

        }

        return new self($features);
    }




    public function jsonSerialize(): array
    {
        return $this->features;
    }

    /**
     * @return Feature[]|\iterable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->features);
    }
}
