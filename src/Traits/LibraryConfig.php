<?php

namespace Drewlabs\Libman\Traits;

use Drewlabs\Libman\Composer;
use Drewlabs\Libman\Contracts\LibraryFactoryClassInterface;
use ReflectionException;
use RuntimeException;

trait LibraryConfig
{
    /**
     * Library name or label
     *
     * @var string
     */
    private $name;

    /**
     * Library package name
     *
     * @var string
     */
    private $package;

    /**
     * Library package version
     * 
     * @var int|string
     */
    private $version;

    /**
     * Library factory class patch
     *
     * @var string
     */
    private $factory;

    /**
     * Library type definition
     *
     * @var string
     */
    private $type;

    /**
     * Dictionary of dynamically invokable method defines on the current class
     *
     * @var array<string,\Closure>
     */
    private $callbacks = [];

    /**
     * The default namespace from which the library factory class must be resolved
     *
     * @var string
     */
    private $defaultNamespace = '\\App';

    /**
     * Creates a new instance of the library config
     *
     * @param mixed $args
     * @return \Drewlabs\Libman\Contracts\InstallableLibraryConfigInterface
     */
    public static function new(...$args)
    {
        return new self(...$args);
    }

    /**
     * Creates a library config instance from array of properties
     * 
     * ```php
     *  // Creates a new library instance
     *  $library = LibraryConfig::create([
     *      'name' => 'Process',
     *      'package' => 'symfony/process',
     *      'version' => '6.0',
     *      'type' => 'composer'
     * ]);
     * ```
     * 
     * @param array $attributes 
     * @return static 
     * @throws ReflectionException 
     */
    public static function create(array $attributes) {
        $instance = (new \ReflectionClass(__CLASS__))->newInstanceWithoutConstructor();
        foreach ($attributes as $key => $value) {
            if (property_exists($instance, $key)) {
                $instance->{$key} = $value;
            }
        }
        return $instance;
    }

    /**
     * {@inheritDoc}
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * {@inheritDoc}
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritDoc}
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getFactoryClass()
    {
        if ((null === ($factory = $this->resolveFactoryClass())) || !class_exists($factory)) {
            throw new RuntimeException('Expected instance of ' . LibraryFactoryClassInterface::class . ' got ' . $factory);
        }
        if (!(is_a($factory, LibraryFactoryClassInterface::class, true))) {
            throw new RuntimeException('Expected instance of ' . LibraryFactoryClassInterface::class . ' got ' . $factory);
        }
        return $factory;
    }

    /**
     * {@inheritDoc}
     */
    public function activated()
    {
        return $this->activated;
    }


    /**
     * Default namespace property getter and setter.
     *
     * @param string|null $namespace
     * @return string
     */
    public function defaultNamespace(?string $namespace = null)
    {
        if (null !== $namespace) {
            $this->defaultNamespace = $namespace;
        }
        return $this->defaultNamespace;
    }

    /**
     *  Register event listener callback that is executed when the library is deactived
     * 
     * @param Closure $callback
     * @return void
     */
    public function addDeactivateListener(\Closure $callback)
    {
        if (null === $callback) {
            return;
        }
        $this->registerMacro('onDeactivate', is_callable($callback) ? \Closure::fromCallable($callback) : $callback);
    }

    /**
     * Deactivate the library instance
     *
     * @return self
     */
    public function deactivate()
    {
        $this->activated = false;
        // We simply invoke the onDeactivate macro passing the current
        // instance as parameter to the caller
        foreach ($this->getMacros('onDeactivate') as $callback) {
            if (null === $callback) {
                continue;
            }
            \Closure::bind($callback, $this)->__invoke($this);
        }
        return $this;
    }

    /**
     * Register a macro or a callable function with bindings to a given name
     *
     * @param mixed $name
     * @param Closure $macro
     * @return void
     */
    private function registerMacro($name, \Closure $macro)
    {
        $this->callbacks[$name] = array_merge($this->callbacks[$name] ?? [], [$macro]);
    }

    /**
     * Returns a list of callable function that we register for the given object
     *
     * @param string $name
     * @return array
     */
    private function getMacros(string $name)
    {
        return is_array($value = $this->callbacks[$name] ?? []) ? $value : [$value];
    }

    /**
     * 
     * @param string $haystack 
     * @param bool $firstcapital 
     * @return mixed 
     */
    private function camelize(string $haystack, $firstcapital = true)
    {
        $replacePipe = function (string $haystack) {
            $delimiter = '_';
            return str_replace($delimiter, '', ucwords($haystack, $delimiter));
        };
        $capitalizePipe = function ($string) use ($firstcapital) {
            return !$firstcapital ? lcfirst($string) : $string;
        };
        return $capitalizePipe($replacePipe($haystack));
    }

    /**
     * Resolve factory class of the given library
     * 
     * @return string 
     */
    private function resolveFactoryClass()
    {
        if ($this->factory && class_exists($this->factory)) {
            return $this->factory;
        }
        if ((strtolower($this->type()) === 'composer') && (null !== $this->getPackage())) {
            return Composer::resolveClassPath($this->getPackage(), $this->factory ?? $this->camelize($this->name()));
        }
        return sprintf("%s\\%s", $this->defaultNamespace(), $this->camelize($this->name()));
    }
}
