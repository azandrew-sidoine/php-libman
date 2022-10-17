<?php

use Drewlabs\Libman\LibraryManager;
use Drewlabs\Libman\Tests\Stubs\ClientLibrary;
use Drewlabs\Libman\Tests\Stubs\ClientLibraryFactory;
use Drewlabs\Libman\WebserviceLibraryConfig;
use PHPUnit\Framework\TestCase;

class LibraryManagerTest extends TestCase
{

    public function test_library_manager_create_instance_method()
    {
        $instance = WebserviceLibraryConfig::new('ClientLibrary', 'composer', null, 'ApiSecret', 'ApiClientId', 'client/library', ClientLibraryFactory::class);
        $library = LibraryManager::createInstance($instance);
        $this->assertInstanceOf(ClientLibrary::class, $library);
    }
}