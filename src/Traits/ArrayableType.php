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

namespace Drewlabs\Libman\Traits;

use Drewlabs\Libman\Utils\Strings;

trait ArrayableType
{
    /**
     * Creates an instance of the class from array of properties.
     *
     * @throws ReflectionException
     *
     * @return static
     */
    public static function fromArray(array $attributes)
    {
        /**
         * @var static
         */
        $instance = (new \ReflectionClass(__CLASS__))->newInstanceWithoutConstructor();
        foreach ($attributes as $name => $value) {
            if (method_exists($instance, $method = sprintf('set%s', Strings::camelize($name)))) {
                $instance->$method($value);
                continue;
            }
            if (property_exists($instance, $name)) {
                $instance->{$name} = $value;
                continue;
            }
        }

        return $instance;
    }
}
