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

use Drewlabs\Libman\Contracts\AuthBasedLibraryConfigInterface;
use Drewlabs\Libman\Contracts\InstallableLibraryConfigInterface;
use Drewlabs\Libman\Contracts\LibraryConfigInterface;
use Drewlabs\Libman\Contracts\LibraryConfigurationsRepositoryInterface;
use Drewlabs\Libman\Contracts\LibraryDefinitionsProvider;
use Drewlabs\Libman\Contracts\WebServiceLibraryConfigInterface;
use Drewlabs\Libman\Exceptions\ExtensionNotLoadedException;

class InMemoryConfigurationRepository implements LibraryConfigurationsRepositoryInterface
{
    /**
     * @var LibraryDefinitionsProvider
     */
    private $provider;

    /**
     * 
     * @throws ExtensionNotLoadedException
     *
     * @return self
     */
    public function __construct(LibraryDefinitionsProvider $provider)
    {
        $this->provider = $provider;
    }

    public function add(LibraryConfigInterface $libraryConfig)
    {
        $attributes = [
            'id' => $libraryConfig->id(),
            'name' => $libraryConfig->getName(),
            'factory' => $libraryConfig->getFactory(),
            'activated' => $libraryConfig->activated(),
            'configuration' => $libraryConfig->getConfiguration()->jsonSerialize()
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

        if (($libraryConfig instanceof AuthBasedLibraryConfigInterface) && (null !== ($auth = $libraryConfig->getAuth()))) {
            $attributes['auth']['id'] = $auth->id();
            $attributes['auth']['secret'] = $auth->secret();
        }
        $this->provider->addDefinition($attributes);
    }

    public function select($id)
    {
        foreach ($this->provider->definitions() as $value) {
            if (!\is_array($value)) {
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
        if (!\is_string($predicate) && \is_callable($predicate)) {
            foreach ($this->provider->definitions() as $value) {
                if ($predicate && $predicate((object) $value)) {
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
     * Check if a key of the array as haystack match user provided $value.
     *
     * @param mixed $value
     *
     * @return bool
     */
    private function propertyIs(array $haystack, string $name, $value)
    {
        return $value === ($haystack[$name] ?? null);
    }

    /**
     * Creates a library configuration instance.
     *
     * @throws ReflectionException
     *
     * @return LibraryConfig
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
