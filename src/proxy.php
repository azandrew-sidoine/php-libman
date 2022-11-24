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

namespace Drewlabs\Libman\Proxy;

use Drewlabs\Libman\Contracts\LibraryDefinitionsProvider;
use Drewlabs\Libman\Exceptions\ExtensionNotLoadedException;
use Drewlabs\Libman\Exceptions\FileNotFoundException;
use Drewlabs\Libman\InMemoryConfigurationRepository;
use Drewlabs\Libman\JsonDefinitionsProvider;
use Drewlabs\Libman\YAMLDefinitionsProvider;

/**
 * Creates an In-memory library config repository.
 *
 * @return InMemoryConfigurationRepository
 */
function CreateInMemoryRepository(LibraryDefinitionsProvider $provider)
{
    return new InMemoryConfigurationRepository($provider);
}

/**
 * Creates YAML based library configuration repository.
 *
 * @param bool|null $persistable
 *
 * @throws ExtensionNotLoadedException
 * @throws FileNotFoundException
 * @throws \ReflectionException
 *
 * @return InMemoryConfigurationRepository
 */
function CreateYAMLLibraryRepository(string $path, bool $persistable = true)
{
    return new InMemoryConfigurationRepository(YAMLDefinitionsProvider::create($path, $persistable));
}

/**
 * Creates JSON based library configuration repository.
 *
 * @param bool|null $persistable
 *
 * @throws ExtensionNotLoadedException
 * @throws FileNotFoundException
 * @throws \ReflectionException
 *
 * @return InMemoryConfigurationRepository
 */
function CreateJSONLibraryRepository(string $path, bool $persistable = true)
{
    return new InMemoryConfigurationRepository(JsonDefinitionsProvider::create($path, $persistable));
}
