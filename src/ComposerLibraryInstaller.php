<?php

namespace Drewlabs\Libman;

use Drewlabs\Libman\Contracts\InstallableLibraryConfigInterface;
use Drewlabs\Libman\Contracts\LibraryInstaller;

/**
 * Provides an installation process implementation for PHP package
 * that use composer as package manager for installation
 *
 * @package App
 */
class ComposerLibraryInstaller implements LibraryInstaller
{

    public function install(InstallableLibraryConfigInterface $libraryConfig)
    {
        Composer::install($libraryConfig->getPackage(), $libraryConfig->getVersion());
    }
}
