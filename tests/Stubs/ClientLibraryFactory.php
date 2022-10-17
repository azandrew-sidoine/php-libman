<?php

namespace Drewlabs\Libman\Tests\Stubs;

use Drewlabs\Libman\Contracts\LibraryConfigInterface;
use Drewlabs\Libman\Contracts\LibraryFactoryInterface;

class ClientLibraryFactory implements LibraryFactoryInterface
{

    public static function createInstance(LibraryConfigInterface $config)
    {
        return new ClientLibrary;
    }
}
