<?php

namespace Drewlabs\Libman;

use Drewlabs\Libman\Contracts\InstallableLibraryConfigInterface;
use Drewlabs\Libman\Contracts\LibraryInstaller;
use Drewlabs\Libman\Contracts\LibraryInstallerFactoryInterface;
use RuntimeException;

class LibraryInstallerFactory implements LibraryInstallerFactoryInterface
{
    /**
     * 
     * @param InstallableLibraryConfigInterface $libraryConfig 
     * @return LibraryInstaller 
     * @throws RuntimeException 
     */
    public static function create(InstallableLibraryConfigInterface $libraryConfig)
    {
        switch (strtolower($libraryConfig->getType())) {
            case 'composer':
                return new ComposerLibraryInstaller;
            default:
                throw new RuntimeException(sprintf('No installer provided for %s', $libraryConfig->getType()));
        }
    }
}
