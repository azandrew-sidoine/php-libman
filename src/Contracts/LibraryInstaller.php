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

namespace Drewlabs\Libman\Contracts;

interface LibraryInstaller
{
    /**
     * Install the library using the library configuration object.
     *
     * @param \Closure $errorCallback Closure to execute when an error occurs during installation
     *
     * @return bool|void
     */
    public function install(InstallableLibraryConfigInterface $libraryConfig, ?\Closure $errorCallback = null);
}
