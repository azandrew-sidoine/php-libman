<?php

namespace Drewlabs\Libman;

use ArrayIterator;
use Drewlabs\Libman\Contracts\LibraryDefinitionsProvider;
use Drewlabs\Libman\Exceptions\FileNotFoundException;

class JSONDefinitionsProvider implements LibraryDefinitionsProvider
{
    /**
     * 
     * @var array|\Traversable
     */
    private $values;

    /**
     * 
     * @var string
     */
    private $documentPath;

    /**
     * 
     * @var bool
     */
    private $persistable;

    /**
     * Creates an instance of {@see JsonDefinitionsProvider} class
     * 
     * @param array $values 
     * @return self 
     */
    public function __construct(array $values = [])
    {
        $this->values = $values;
    }

    public static function create(string $path, bool $persistable = true)
    {
        $instance = (new \ReflectionClass(__CLASS__))->newInstanceWithoutConstructor();
        $instance->values = static::load($path);
        $instance->documentPath = $path;
        $instance->persistable = $persistable;
        return $instance;
    }

    private static function load(string &$path)
    {
        $path = realpath($path);
        if (@is_dir($path) && @is_file("$path" . DIRECTORY_SEPARATOR . "libman.json")) {
            $path = "$path" . DIRECTORY_SEPARATOR . "libman.json";
        }
        if (!is_file($path)) {
            throw new FileNotFoundException($path);
        }
        return json_decode(file_get_contents($path), true);
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
        return new ArrayIterator($this->values['services'] ?? $this->values ?? []);
    }

    public function persist()
    {
        if (!@is_file($this->documentPath)) {
            return;
        }
        if (!@is_writable($this->documentPath)) {
            return;
        }
        file_put_contents($this->documentPath, json_encode($this->values, JSON_PRETTY_PRINT));
    }
}