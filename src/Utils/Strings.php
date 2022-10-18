<?php

namespace Drewlabs\Libman\Utils;

class Strings
{
    /**
     * Convert a string to it camel case representation
     * 
     * @param string $haystack 
     * @param bool $firstcapital 
     * @param string $delimiter 
     * @return mixed 
     */
    public static function camelize(string $haystack, $firstcapital = true, string $delimiter = '_')
    {
        $replacePipe = function (string $haystack) use ($delimiter) {
            return str_replace($delimiter, '', ucwords($haystack, $delimiter));
        };
        $capitalizePipe = function ($string) use ($firstcapital) {
            return !$firstcapital ? lcfirst($string) : $string;
        };
        return $capitalizePipe($replacePipe($haystack));
    }
}