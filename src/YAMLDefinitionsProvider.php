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

namespace Drewlabs\Libman;

use Drewlabs\Libman\Contracts\LibraryDefinitionsProvider;
use Drewlabs\Libman\Exceptions\ExtensionNotLoadedException;
use Drewlabs\Libman\Exceptions\FileNotFoundException;

class YAMLDefinitionsProvider implements LibraryDefinitionsProvider
{
    /**
     * @var array|\Traversable
     */
    private $values;

    /**
     * @var string
     */
    private $documentPath;

    /**
     * @var bool
     */
    private $persistable;

    /**
     * Creates an instance of {@see YAMLDefinitionsProvider} class.
     *
     * @return self
     */
    public function __construct(array $values = [])
    {
        $this->values = $values;
    }

    /**
     * Creates an instance of {@see YAMLDefinitionsProvider} class.
     *
     * @throws ExtensionNotLoadedException
     * @throws FileNotFoundException
     * @throws \ReflectionException
     *
     * @return static
     */
    public static function create(string $path, bool $persistable = true)
    {
        if (!\function_exists('yaml_parse')) {
            throw new ExtensionNotLoadedException('yaml');
        }
        /**
         * @var static
         */
        $instance = (new \ReflectionClass(__CLASS__))->newInstanceWithoutConstructor();
        $instance->values = static::load($path);
        $instance->documentPath = $path;
        $instance->persistable = $persistable;

        return $instance;
    }

    public function definitions()
    {
        return new \ArrayIterator($this->values['services'] ?? []);
    }

    public function addDefinition(array $value)
    {
        $this->values['services'][] = $value;
        if ($this->persistable) {
            $this->persist();
        }
    }

    public function persist()
    {
        if (!@is_file($this->documentPath)) {
            return;
        }
        if (!@is_writable($this->documentPath)) {
            return;
        }
        file_put_contents($this->documentPath, yaml_emit($this->values, \YAML_UTF8_ENCODING));
    }

    /**
     * @throws FileNotFoundException
     *
     * @return array
     */
    private static function load(string &$path)
    {
        $path = realpath($path);
        if (@is_dir($path) && @is_file("$path".\DIRECTORY_SEPARATOR.'libman.yml')) {
            $path = "$path".\DIRECTORY_SEPARATOR.'libman.yml';
        } elseif (@is_dir($path) && @is_file("$path".\DIRECTORY_SEPARATOR.'libman.yaml')) {
            $path = "$path".\DIRECTORY_SEPARATOR.'libman.yaml';
        }
        if (!is_file($path)) {
            throw new FileNotFoundException($path);
        }

        return (array) (yaml_parse(file_get_contents($path)));
    }
}
