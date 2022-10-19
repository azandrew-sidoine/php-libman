<?php

namespace Drewlabs\Libman;

use Drewlabs\Libman\Contracts\InstallableLibraryConfigInterface;
use Drewlabs\Libman\Contracts\LibraryConfigInterface;
use Drewlabs\Libman\Contracts\LibraryConfigurationsRepositoryInterface;
use InvalidArgumentException;
use RuntimeException;

class LibraryManager
{

    /**
     * 
     * @var LibraryConfigurationsRepositoryInterface
     */
    private $repository;

    public function __construct(LibraryConfigurationsRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }


    /**
     * Resolve or Create a library instance
     * 
     * @param string $id 
     * @return object 
     * @throws RuntimeException 
     * @throws InvalidArgumentException 
     */
    public function resolveInstance(string $id)
    {
        $library = $this->repository->select($id);
        if ((null === $library) || !($library instanceof LibraryConfigInterface)) {
            throw new RuntimeException("Library $id is not configured in the provided repository");
        }
        return static::createInstance($library);
    }

    /**
     * Install a library along with it dependencies using the configuration object
     *
     * @param InstallableLibraryConfigInterface $libraryConfig
     * @return void
     * @throws RuntimeException
     */
    public static function install(InstallableLibraryConfigInterface $libraryConfig)
    {
        LibraryInstallerFactory::create($libraryConfig)->install($libraryConfig);
    }


    /**
     * Creates an instance of the library main object using the configuration definition object
     *
     * @param LibraryConfigInterface $libraryConfig
     * @return object|\stdClass
     * @throws InvalidArgumentException
     */
    public static function createInstance(LibraryConfigInterface $libraryConfig)
    {
        if ((null === $libraryConfig)) {
            throw new InvalidArgumentException(sprintf('Library %s is does not exists', $libraryConfig->name()));
        }
        if (!$libraryConfig->activated()) {
            throw new InvalidArgumentException(sprintf('Library %s has been disabled', $libraryConfig->name()));
        }
        return call_user_func([$libraryConfig->getFactoryClass(), 'createInstance'], $libraryConfig);
    }
}
