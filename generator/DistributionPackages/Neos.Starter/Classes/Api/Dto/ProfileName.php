<?php


namespace Neos\Starter\Api\Dto;


/**
 * @Flow\Proxy(false)
 * @api
 */
final class ProfileName implements \JsonSerializable
{
    private string $name;

    const PROFILE_2020_09_A = '2020-09-a';

    private function __construct(string $name)
    {
        if ($name !== self::PROFILE_2020_09_A) {
            // lateron, support multiple profiles here.
            throw new \RuntimeException('invalid profile');
        }
        $this->name = $name;
    }


    public static function latest(): self
    {
        return new self(self::PROFILE_2020_09_A);
    }

    public function equals(ProfileName $other): bool
    {
        return $this->name === $other->name;
    }

    public function jsonSerialize()
    {
        return $this->name;
    }

    public function __toString(): string
    {
        return 'ProfileName:' . $this->name;
    }
}
