<?php

use Drewlabs\Libman\InMemoryConfigurationRepository;
use Drewlabs\Libman\LibraryManager;
use Drewlabs\Libman\Tests\Stubs\ClientLibrary;
use Drewlabs\Libman\Tests\Stubs\ClientLibraryFactory;
use Drewlabs\Libman\WebserviceLibraryConfig;
use Drewlabs\Libman\YAMLDefinitionsProvider;
use PHPUnit\Framework\TestCase;

class LibraryManagerTest extends TestCase
{

    private function createYMLBasedRepository(bool $persitable = true)
    {
        return new InMemoryConfigurationRepository(YAMLDefinitionsProvider::create(realpath(__DIR__ . '/Stubs'), $persitable));
    }

    public function test_library_manager_create_instance_method()
    {
        $instance = WebserviceLibraryConfig::new('ClientLibrary', 'composer', null, 'ApiSecret', 'ApiClientId', 'client/library', ClientLibraryFactory::class);
        $library = LibraryManager::createInstance($instance);
        $this->assertInstanceOf(ClientLibrary::class, $library);
    }


    public function test_library_manager_resolve_instance_method()
    {
        $repository = $this->createYMLBasedRepository(false);
        $libManager = new LibraryManager($repository);
        $instance = $libManager->resolveInstance('c9c8dba2-068c-454e-a1f5-fa711bddde41');
        $this->assertInstanceOf(ClientLibrary::class, $instance);
    }
}