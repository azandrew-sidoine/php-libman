<?php

namespace Drewlabs\Libman\Contracts;

interface LibraryInstaller
{
    /**
     * Install the library using the library configuration object
     *
     * @param InstallableLibraryConfigInterface $libraryConfig
     * @return bool|void
     */
    public function install(InstallableLibraryConfigInterface $libraryConfig);
}
