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

use Drewlabs\Libman\Contracts\CryptInterface;
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
     * 
     * @var CryptInterface
     */
    private $crypt;


    /**
     * Creates an instance of {@see YAMLDefinitionsProvider} class.
     *
     * @return self
     */
    public function __construct(array $values = [])
    {
        $this->values = $values;
        $this->crypt = new CryptAdapter();
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
    public static function create(string $path, bool $persistable = true, CryptInterface $crypt = null)
    {
        if (!\function_exists('yaml_parse')) {
            throw new ExtensionNotLoadedException('yaml');
        }
        $crypt = $crypt ?? new CryptAdapter();
        $instance = static::newInstanceWithoutConstructor();
        $instance->values = static::load($crypt, $path);
        $instance->documentPath = $path;
        $instance->persistable = $persistable;
        $instance->setCrypt($crypt);
        return $instance;
    }

    /**
     * set crypt instance
     * 
     * @param CryptInterface $crypt 
     * @return static 
     */
    public function setCrypt(CryptInterface $crypt)
    {
        $this->crypt = $crypt;
        return $this;
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
        file_put_contents($this->documentPath, $this->crypt->encryptString(yaml_emit($this->values, \YAML_UTF8_ENCODING)));
    }

    /**
     * @throws FileNotFoundException
     *
     * @return array
     */
    private static function load(CryptInterface $crypt, string &$path)
    {
        $path = realpath($path);
        if (@is_dir($path) && @is_file("$path".\DIRECTORY_SEPARATOR.'libman.yml')) {
            $path = "$path".\DIRECTORY_SEPARATOR.'libman.yml';
        } elseif (@is_dir($path) && @is_file("$path".\DIRECTORY_SEPARATOR.'libman.yaml')) {
            $path = "$path".\DIRECTORY_SEPARATOR.'libman.yaml';
        } else if (@is_dir($path) && @is_file("$path" . \DIRECTORY_SEPARATOR . 'libman')) {
            // search path for libman wihtout .json extension
            $path = "$path" . \DIRECTORY_SEPARATOR . 'libman';
        }

        // case $path variable is still not a file path, we throw an exception
        if (!is_file($path)) {
            throw new FileNotFoundException($path);
        }
        return (array) (yaml_parse($crypt->decryptString(file_get_contents($path))));
    }

    /**
     * Creates new class without calling constructor
     * 
     * @return static 
     * @throws ReflectionException 
     */
    private static function newInstanceWithoutConstructor()
    {
        return (new \ReflectionClass(__CLASS__))->newInstanceWithoutConstructor();
    }
}
