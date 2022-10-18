<?php

namespace Drewlabs\Libman;

use Drewlabs\Libman\Contracts\AuthBasedLibraryConfigInterface;
use Drewlabs\Libman\Contracts\LibraryConfigInterface;
use Drewlabs\Libman\Contracts\InstallableLibraryConfigInterface;
use Drewlabs\Libman\Contracts\LibraryConfigurationsRepositoryInterface;
use Drewlabs\Libman\Contracts\LibraryDefinitionsProvider;
use Drewlabs\Libman\Contracts\WebServiceLibraryConfigInterface;
use Drewlabs\Libman\Exception\ExtensionNotLoadedException;
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
     * @param bool $persist 
     * @return self 
     * @throws ExtensionNotLoadedException 
     */
    public function __construct(LibraryDefinitionsProvider $provider, bool $persist = true)
    {
        $this->provider = $provider;
        $this->persist = $persist;
    }

    public function add(LibraryConfigInterface $libraryConfig)
    {
        $attributes = [
            'name' => $libraryConfig->name(),
            'factory' => $libraryConfig->getFactoryClass(),
            'activated' => $libraryConfig->activated()
        ];

        if ($libraryConfig instanceof InstallableLibraryConfigInterface) {
            $attributes['package'] = $libraryConfig->getPackage();
            $attributes['version'] = $libraryConfig->getPackage();
            $attributes['type'] = $libraryConfig->getType();
        }

        if ($libraryConfig instanceof WebServiceLibraryConfigInterface) {
            $attributes['host'] = $libraryConfig->getHost();
            $attributes['tcp'] = $libraryConfig->getHost();
        }

        if ($libraryConfig instanceof AuthBasedLibraryConfigInterface) {
            $auth = $libraryConfig->getAuthCredentials();
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
        }
    }

    public function selectAll()
    {
        foreach ($this->provider->definitions() as $value) {
            yield $this->createLibraryConfig($value);
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
        $apiType = strtolower($libraryConfig['service-type'] ?? $libraryConfig['service'] ?? 'default');
        switch ($apiType) {
            case 'tcp':
            case 'http':
            case 'rest':
                WebserviceLibraryConfig::create($libraryConfig);
            default:
                return LibraryConfig::create($libraryConfig);
        }
    }
    
}
