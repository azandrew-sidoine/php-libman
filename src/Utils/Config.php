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

use Drewlabs\Libman\Contracts\RepositoryInterface;

class Config implements RepositoryInterface
{
    /**
     * @var array<string,mixed>
     */
    private $kvPairs = [];

    /**
     * Creates a configuration repository instance
     * 
     * @param array $configurations 
     */
    public function __construct($configurations = [])
    {
        $this->kvPairs = $configurations;
    }

    /**
     * {@inheritDoc}
     * 
     * @return array<string,mixed>
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Immutable interface for merging repository internal state
     * 
     * @param array $values 
     * @return static 
     */
    public function merge(array $values)
    {
        return new static(array_replace_recursive($this->kvPairs ?? [], $values ?? []));
    }

    /**
     * check if the repository has configuration value
     * 
     * @return bool 
     */
    public function isEmpty()
    {
        return empty($this->kvPairs);
    }

    /**
     * check if configuration value exists for key `$name`
     * 
     * @param string $name 
     * @return bool 
     */
    public function exists(string $name)
    {
        return null !== $this->get($name, null);
    }

    /**
     * PHP array reprentation of the configuration instance
     * 
     * @return array 
     */
    public function toArray()
    {
        return $this->kvPairs ?? [];
    }

    /**
     * resolves a value from the repository
     * 
     * @param string $name 
     * @param mixed $default 
     * @return mixed 
     */
    public function get(string $name, $default = null)
    {
        if (false !== strpos($name, '.')) {
            $keys = explode('.', $name);
            $last = \count($keys);
            $index = 1;
            $result = $this->kvPairs[trim($keys[0])] ?? null;
            while ($index < $last) {
                if (!(($is_object = \is_object($result)) || \is_array($result))) {
                    return null;
                }
                $prop = trim($keys[$index]);
                $result = !$is_object ? $result[$prop] ?? null : $result->{$prop};
                ++$index;
            }

            return $result ?? $default;
        }
        return $this->kvPairs[$name] ?? $default;
    }
}
