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

namespace Drewlabs\Libman\Tests\Stubs;

use Drewlabs\Libman\Contracts\LibraryConfigInterface;
use Drewlabs\Libman\Contracts\LibraryFactoryInterface;

class ClientLibraryFactory implements LibraryFactoryInterface
{
    public static function createInstance(LibraryConfigInterface $config)
    {
        return new ClientLibrary();
    }
}
