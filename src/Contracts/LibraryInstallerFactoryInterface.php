<?php

namespace Drewlabs\Libman\Contracts;


interface LibraryInstallerFactoryInterface
{
    /**
     * Creates an instance of a library installer
     *
     * @param InstallableLibraryConfigInterface $libraryConfig
     * @return LibraryInstaller
     */
    public static function create(InstallableLibraryConfigInterface $libraryConfig);
}
