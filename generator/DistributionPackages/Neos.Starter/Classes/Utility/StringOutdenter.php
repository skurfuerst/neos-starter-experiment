<?php

declare(strict_types=1);

namespace Neos\Starter\Utility;


use Neos\Starter\Generator\StringBuilder;
use Symfony\Component\Yaml\Yaml;

class StringOutdenter
{

    public static function outdent(string $comment): string
    {
        $lines = explode("\n", $comment);
        $whitespaces = '';
        foreach ($lines as $line) {
            if (strlen(trim($line)) > 0) {
                // we found the first non-empty line -> check the beginning whitespace.
                preg_match('/^\s*/', $line, $matches);
                $whitespaces = $matches[0];
                break;
            }
        }

        foreach ($lines as &$line) {
            if (strpos($line, $whitespaces) === 0) {
                $line = substr($line, strlen($whitespaces));
            }
        }

        return trim(implode("\n", $lines));
    }
}
