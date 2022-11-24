<?php

namespace Drewlabs\Libman\Traits;

use Drewlabs\Libman\Composer;
use Drewlabs\Libman\Contracts\LibraryFactoryInterface;
use ReflectionException;
use RuntimeException;
use Drewlabs\Libman\Contracts\LibraryRepositoryConfigInterface;
use Drewlabs\Libman\Utils\LibraryRepositoryConfig;
use Drewlabs\Libman\Utils\Strings;

trait LibraryConfig
{

    use ArrayableType;

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
     * Repository configuration of the library
     * 
     * @var LibraryRepositoryConfigInterface
     */
    private $repository;

    /**
     * Indicates whether the repository is private or public
     * 
     * @var bool
     */
    private $private = false;

    /**
     * Indicated if the library is activated or not
     * 
     * @var bool
     */
    private $activated = true;

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
    public static function create(array $attributes)
    {
        return static::fromArray($attributes);
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
    public function getName()
    {
        return $this->name;
    }

    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * {@inheritDoc}
     */
    public function factoryClass()
    {
        if ((null === ($factory = $this->resolveFactoryClass())) || !class_exists($factory)) {
            throw new RuntimeException('Expected instance of ' . LibraryFactoryInterface::class . ' got ' . $factory);
        }
        if (!(is_a($factory, LibraryFactoryInterface::class, true))) {
            throw new RuntimeException('Expected instance of ' . LibraryFactoryInterface::class . ' got ' . $factory);
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
    public function defaultNamespace(string $namespace = null)
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
     * Set the repository property on the current instance
     * 
     * @param object|array $value 
     * @return void 
     */
    public function setRepository($value)
    {
        $value = is_object($value) ? get_object_vars($value) : $value;
        if (!is_array($value)) {
            throw new \InvalidArgumentException("Expect array/object as parameter, got " . (null !== $value && is_object($value) ? get_class($value) : gettype($value)));
        }
        // we sure the repository is always an array
        $isList = array_filter($value, 'is_array') === $value;
        $this->repository = $isList ? array_map(function ($item) {
            return LibraryRepositoryConfig::fromArray($item);
        }, $value) : [LibraryRepositoryConfig::fromArray($value)];
    }

    /**
     * 
     * {@inheritDoc}
     * 
     * @return LibraryRepositoryConfigInterface[] 
     */
    public function getRepository()
    {
        return $this->repository;
    }

    public function isPrivate()
    {
        return $this->private;
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
     * Resolve factory class of the given library
     * 
     * @return string 
     */
    private function resolveFactoryClass()
    {
        if (($factory = $this->getFactory()) && class_exists($factory)) {
            return $factory;
        }
        $factoryClass = null;
        if ((strtolower($this->getType()) === 'composer') && (null !== $this->getPackage())) {
            // We working with composer based library configuration, we assume by default `Factory.php` is the
            // class used to create the library instance if no factory class is provided
            $factoryClass =  Composer::resolveClassPath($this->getPackage(), $this->getFactory() ?? 'Factory');
        }
        return $factoryClass ?? sprintf("%s\\%s", $this->defaultNamespace(), Strings::camelize($this->getName()));
    }
}
