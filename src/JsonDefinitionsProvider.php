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
use Drewlabs\Libman\Exceptions\FileNotFoundException;
use ReflectionException;
use Drewlabs\Libman\Contracts\CryptInterface;

class JsonDefinitionsProvider implements LibraryDefinitionsProvider
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
     * @var CryptInterface
     */
    private $crypt;

    /**
     * Creates an instance of {@see JsonDefinitionsProvider} class.
     *
     * @return self
     */
    public function __construct(array $values = [])
    {
        $this->values = $values;
    }

    public static function create(string $path, bool $persistable = true, CryptInterface $crypt = null)
    {
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

    public function addDefinition(array $value)
    {
        $this->values['services'][] = $value;
        if ($this->persistable) {
            $this->persist();
        }
    }

    public function definitions()
    {
        return new \ArrayIterator($this->values['services'] ?? $this->values ?? []);
    }

    public function persist()
    {
        if (!@is_file($this->documentPath)) {
            return;
        }
        if (!@is_writable($this->documentPath)) {
            return;
        }
        file_put_contents($this->documentPath, $this->crypt->encryptString(str_replace('\/', '/', json_encode($this->values, \JSON_PRETTY_PRINT))));
    }

    private static function load(CryptInterface $crypt, string &$path)
    {
        $path = realpath($path);
        if (@is_dir($path) && @is_file("$path" . \DIRECTORY_SEPARATOR . 'libman.json')) {
            $path = "$path" . \DIRECTORY_SEPARATOR . 'libman.json';
        }
        if (!is_file($path)) {
            throw new FileNotFoundException($path);
        }

        return json_decode($crypt->decryptString(file_get_contents($path)), true);
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
