<?php

namespace Drewlabs\Libman;

use ArrayIterator;
use Drewlabs\Libman\Contracts\LibraryDefinitionsProvider;
use Drewlabs\Libman\Exceptions\ExtensionNotLoadedException;
use Drewlabs\Libman\Exceptions\FileNotFoundException;
use ReflectionException;

class YAMLDefinitionsProvider implements LibraryDefinitionsProvider
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
     * Creates an instance of {@see YAMLDefinitionsProvider} class
     * 
     * @param array $values 
     * @return self 
     */
    public function __construct(array $values = [])
    {
        $this->values = $values;
    }

    /**
     * Creates an instance of {@see YAMLDefinitionsProvider} class
     * 
     * @param string $path 
     * @param bool $persistable 
     * @return static 
     * @throws ExtensionNotLoadedException 
     * @throws FileNotFoundException 
     * @throws ReflectionException 
     */
    public static function create(string $path, bool $persistable = true)
    {
        if (!function_exists('yaml_parse')) {
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
        return new ArrayIterator($this->values['services'] ?? []);
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
        file_put_contents($this->documentPath, yaml_emit($this->values, YAML_UTF8_ENCODING));
    }


    /**
     * 
     * @param string $path 
     * @return array 
     * @throws FileNotFoundException 
     */
    private static function load(string &$path)
    {
        $path = realpath($path);
        if (@is_dir($path) && @is_file("$path" . DIRECTORY_SEPARATOR . "libman.yml")) {
            $path = "$path" . DIRECTORY_SEPARATOR . "libman.yml";
        } else if (@is_dir($path) && @is_file("$path" . DIRECTORY_SEPARATOR . "libman.yaml")) {
            $path = "$path" . DIRECTORY_SEPARATOR . "libman.yaml";
        }
        if (!is_file($path)) {
            throw new FileNotFoundException($path);
        }
        return (array)(yaml_parse(file_get_contents($path)));
    }
}
