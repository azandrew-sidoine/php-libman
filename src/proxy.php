<?php


namespace Drewlabs\Libman\Proxy;

use Drewlabs\Libman\Contracts\LibraryDefinitionsProvider;
use Drewlabs\Libman\Exceptions\ExtensionNotLoadedException;
use Drewlabs\Libman\Exceptions\FileNotFoundException;
use Drewlabs\Libman\InMemoryConfigurationRepository;
use Drewlabs\Libman\JsonDefinitionsProvider;
use Drewlabs\Libman\YAMLDefinitionsProvider;
use ReflectionException;

/**
 * Creates an In-memory library config repository
 * 
 * @param LibraryDefinitionsProvider $provider 
 * @return InMemoryConfigurationRepository 
 */
function CreateInMemoryRepository(LibraryDefinitionsProvider $provider)
{
    return new InMemoryConfigurationRepository($provider);
}


/**
 * Creates YAML based library configuration repository
 * 
 * @param string $path 
 * @param null|bool $persistable 
 * @return InMemoryConfigurationRepository 
 * @throws ExtensionNotLoadedException 
 * @throws FileNotFoundException 
 * @throws ReflectionException 
 */
function CreateYAMLLibraryRepository(string $path, bool $persistable = true) {
    return new InMemoryConfigurationRepository(YAMLDefinitionsProvider::create($path, $persistable));
}

/**
 * Creates JSON based library configuration repository
 * 
 * @param string $path 
 * @param null|bool $persistable 
 * @return InMemoryConfigurationRepository 
 * @throws ExtensionNotLoadedException 
 * @throws FileNotFoundException 
 * @throws ReflectionException 
 */
function CreateJSONLibraryRepository(string $path, bool $persistable = true) {
    return new InMemoryConfigurationRepository(JsonDefinitionsProvider::create($path, $persistable));
}