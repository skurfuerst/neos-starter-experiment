<?php

declare(strict_types=1);

namespace Neos\Starter\Utility;


use Symfony\Component\Yaml\Yaml;

class YamlWithComments
{

    public static function comment(string $comment): string
    {
        return '##' . base64_encode($comment);
    }

    public static function dump(array $input): string
    {
        $sortedInput = self::sort($input);

        $yamlAsString = Yaml::dump($sortedInput, 100);

        // first, replace the comment keys of the form 'bla##': ...
        $yamlAsString = preg_replace_callback("|^(\s*)'[^']+##': '##([a-zA-Z0-9+=/]+)'$|m", function ($a) {
            $indentation = $a[1];
            $comment = base64_decode($a[2]);
            $commentLines = explode("\n", $comment);
            $prefix = $indentation . '# ';
            return "\n" . $prefix . implode("\n" . $prefix, $commentLines);
        }, $yamlAsString);

        // second, replace the comment keys of the form - '##...'
        $yamlAsString = preg_replace_callback("|^(\s*)- '##([a-zA-Z0-9+=/]+)'$|m", function ($a) {
            $indentation = $a[1];
            $comment = base64_decode($a[2]);
            $commentLines = explode("\n", $comment);
            $prefix = $indentation . '# ';
            return "\n" . $prefix . implode("\n" . $prefix, $commentLines);
        }, $yamlAsString);

        return $yamlAsString;
    }

    /**
     * sort the keys appended with "##" (signifying comments) BEFORE the entry where the comment should apply.
     *
     * @param array $in
     * @return array
     */
    private static function sort(array $in): array
    {
        $keysToSort = [];
        foreach ($in as $key => $value) {
            if (is_string($key) && str_ends_with($key, '##')) {
                $keysToSort[] = $key;
            }
        }

        foreach ($keysToSort as $key) {
            // https://stackoverflow.com/a/38655962
            $new_element = [$key => $in[$key]];

            // if needed, find the insertion index by key
            $index = array_search(substr($key, 0, -2), array_keys($in));
            if ($index !== false) {
                // add element at index (note the last array_slice argument)
                $in = array_slice($in, 0, $index, true) + $new_element + array_slice($in, $index, null, true);
            }
        }

        foreach ($in as $key => $value) {
            if (is_array($value)) {
                $in[$key] = self::sort($value);
            }
        }

        return $in;
    }
}
