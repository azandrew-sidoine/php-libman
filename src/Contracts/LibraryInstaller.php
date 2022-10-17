<?php

namespace Drewlabs\Libman\Contracts;

interface LibraryInstaller
{
    /**
     * Install the library using the library configuration object
     *
     * @param InstallableLibraryConfigInterface $libraryConfig
     * @param \Closure $errorCallback Closure to execute when an error occurs during installation
     * @return bool|void
     */
    public function install(InstallableLibraryConfigInterface $libraryConfig, \Closure $errorCallback = null);
}
