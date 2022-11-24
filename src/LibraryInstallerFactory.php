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
use Drewlabs\Libman\Contracts\LibraryInstaller;
use Drewlabs\Libman\Contracts\LibraryInstallerFactoryInterface;

class LibraryInstallerFactory implements LibraryInstallerFactoryInterface
{
    /**
     * @throws \RuntimeException
     *
     * @return LibraryInstaller
     */
    public static function create(InstallableLibraryConfigInterface $libraryConfig)
    {
        switch (strtolower($libraryConfig->getType())) {
            case 'composer':
                return new ComposerLibraryInstaller();
            default:
                throw new \RuntimeException(sprintf('No installer provided for %s', $libraryConfig->getType()));
        }
    }
}
