<?php

declare(strict_types=1);

/*
 * This file is part of the Drewlabs package.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Drewlabs\Libman\Utils;

class Strings
{
    /**
     * Convert a string to it camel case representation.
     *
     * @param bool $firstcapital
     *
     * @return mixed
     */
    public static function camelize(string $haystack, $firstcapital = true, string $delimiter = '_')
    {
        $replacePipe = static function (string $haystack) use ($delimiter) {
            return str_replace($delimiter, '', ucwords($haystack, $delimiter));
        };
        $capitalizePipe = static function ($string) use ($firstcapital) {
            return !$firstcapital ? lcfirst($string) : $string;
        };

        return $capitalizePipe($replacePipe($haystack));
    }
}
