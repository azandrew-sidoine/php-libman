<?php

namespace Drewlabs\Libman\Traits;

use Drewlabs\Libman\Utils\Strings;

trait ArrayableType
{
    /**
     * Creates an instance of the class from array of properties
     * 
     * @param array $attributes 
     * @return static 
     * @throws ReflectionException 
     */
    public static function fromArray(array $attributes)
    {
        $instance = (new \ReflectionClass(__CLASS__))->newInstanceWithoutConstructor();
        foreach ($attributes as $key => $value) {
            if (method_exists($instance, $method = sprintf("set%s", Strings::camelize($key)))) {
                $instance->$method($value);
                continue;
            }
            if (property_exists($instance, $key)) {
                $instance->{$key} = $value;
                continue;
            }
        }
        return $instance;
    }
}