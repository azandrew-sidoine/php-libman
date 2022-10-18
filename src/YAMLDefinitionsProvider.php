<?php

namespace Drewlabs\Libman;

use Drewlabs\Libman\Contracts\LibraryDefinitionsProvider;
use Drewlabs\Libman\Exception\ExtensionNotLoadedException;
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
        return $this->values['services'] ?? [];
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
        file_put_contents($this->documentPath, yaml_emit($this->values));
    }


    private static function load(string $path)
    {
        return (array)(yaml_parse(file_get_contents($path)));
    }
}