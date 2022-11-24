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

use Drewlabs\Libman\Contracts\InstallableLibraryConfigInterface;
use Drewlabs\Libman\Contracts\LibraryConfigInterface;
use Drewlabs\Libman\Contracts\LibraryConfigurationsRepositoryInterface;

class LibraryManager
{
    /**
     * @var LibraryConfigurationsRepositoryInterface
     */
    private $repository;

    public function __construct(LibraryConfigurationsRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Resolve or Create a library instance.
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     *
     * @return object
     */
    public function resolveInstance(string $id)
    {
        $library = $this->repository->select($id);
        if ((null === $library) || !($library instanceof LibraryConfigInterface)) {
            throw new \RuntimeException("Library $id is not configured in the provided repository");
        }

        return static::createInstance($library);
    }

    /**
     * Install a library along with it dependencies using the configuration object.
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    public static function install(InstallableLibraryConfigInterface $libraryConfig)
    {
        LibraryInstallerFactory::create($libraryConfig)->install($libraryConfig);
    }

    /**
     * Creates an instance of the library main object using the configuration definition object.
     *
     * @throws \InvalidArgumentException
     *
     * @return object|\stdClass
     */
    public static function createInstance(LibraryConfigInterface $libraryConfig)
    {
        if ((null === $libraryConfig)) {
            throw new \InvalidArgumentException(sprintf('Library %s is does not exists', $libraryConfig->name()));
        }
        if (!$libraryConfig->activated()) {
            throw new \InvalidArgumentException(sprintf('Library %s has been disabled', $libraryConfig->getName()));
        }

        return \call_user_func([$libraryConfig->factoryClass(), 'createInstance'], $libraryConfig);
    }
}
