<?php

namespace Drewlabs\Libman;

use Drewlabs\Libman\Contracts\AuthBasedLibraryConfigInterface;
use Drewlabs\Libman\Contracts\LibraryConfigInterface;
use Drewlabs\Libman\Contracts\InstallableLibraryConfigInterface;
use Drewlabs\Libman\Contracts\LibraryConfigurationsRepositoryInterface;
use Drewlabs\Libman\Contracts\LibraryDefinitionsProvider;
use Drewlabs\Libman\Contracts\WebServiceLibraryConfigInterface;
use Drewlabs\Libman\Exceptions\ExtensionNotLoadedException;
use Drewlabs\Libman\LibraryConfig;
use Drewlabs\Libman\WebserviceLibraryConfig;

class InMemoryConfigurationRepository implements LibraryConfigurationsRepositoryInterface
{
    /**
     * 
     * @var LibraryDefinitionsProvider
     */
    private $provider;

    /**
     * 
     * @param LibraryDefinitionsProvider $path 
     * @return self 
     * @throws ExtensionNotLoadedException 
     */
    public function __construct(LibraryDefinitionsProvider $provider)
    {
        $this->provider = $provider;
    }

    public function add(LibraryConfigInterface $libraryConfig)
    {
        $attributes = [
            'name' => $libraryConfig->getName(),
            'factory' => $libraryConfig->getFactory(),
            'activated' => $libraryConfig->activated()
        ];

        if ($libraryConfig instanceof InstallableLibraryConfigInterface) {
            $attributes['package'] = $libraryConfig->getPackage();
            $attributes['version'] = $libraryConfig->getVersion();
            $attributes['type'] = $libraryConfig->getType();
        }

        if ($libraryConfig instanceof WebServiceLibraryConfigInterface) {
            $attributes['host'] = $libraryConfig->getHost();
            $attributes['service'] = 'tcp';
        }

        if ($libraryConfig instanceof AuthBasedLibraryConfigInterface) {
            $auth = $libraryConfig->getAuth();
            $attributes['auth']['id'] = $auth->id();
            $attributes['auth']['secret'] = $auth->secret();
        }
        $this->provider->addDefinition($attributes);
    }

    public function select($id)
    {
        foreach ($this->provider->definitions() as $value) {
            if (!is_array($value)) {
                continue;
            }
            if ($this->propertyIs($value, 'id', $id) || $this->propertyIs($value, 'package', $id)) {
                return $this->createLibraryConfig($value);
            }
            if ($this->propertyIs($value, 'name', $id)) {
                return $this->createLibraryConfig($value);
            }
        }
        return null;
    }

    public function selectAll(\Closure $predicate = null)
    {
        if (!is_string($predicate) && is_callable($predicate)) {
            foreach ($this->provider->definitions() as $value) {
                if ($predicate && $predicate((object)$value)) {
                    yield $this->createLibraryConfig($value);
                }
            }
        } else {
            foreach ($this->provider->definitions() as $value) {
                yield $this->createLibraryConfig($value);
            }
        }
    }

    /**
     * Check if a key of the array as haystack match user provided $value
     * 
     * @param array $haystack 
     * @param string $name 
     * @param mixed $value 
     * @return bool 
     */
    private function propertyIs(array $haystack, string $name, $value)
    {
        return ($value === ($haystack[$name] ?? null));
    }

    /**
     * Creates a library configuration instance
     * 
     * @param array $libraryConfig 
     * @return LibraryConfig 
     * @throws ReflectionException 
     */
    private function createLibraryConfig(array $libraryConfig)
    {
        $apiType = strtolower($libraryConfig['service'] ?? $libraryConfig['api'] ?? 'basic');
        switch ($apiType) {
            case 'tcp':
            case 'http':
            case 'rest':
                return WebserviceLibraryConfig::create($libraryConfig);
            case 'basic':
                return LibraryConfig::create($libraryConfig);
            default:
                return LibraryConfig::create($libraryConfig);
        }
    }
}
