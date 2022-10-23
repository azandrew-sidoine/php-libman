<?php

use Drewlabs\Libman\Contracts\InstallableLibraryConfigInterface;
use Drewlabs\Libman\Contracts\LibraryFactoryInterface;
use Drewlabs\Libman\LibraryConfig;
use Drewlabs\Libman\Tests\Stubs\ClientLibraryFactory;
use PHPUnit\Framework\TestCase;

class LibraryConfigTest extends TestCase
{

    public function test_create_library_config_instance()
    {
        $instance = LibraryConfig::new('ClientLibrary', 'composer', 'client/library');
        $this->assertInstanceOf(InstallableLibraryConfigInterface::class, $instance);
        $this->assertEquals($instance->getName(), 'ClientLibrary');
        $this->assertEquals($instance->getType(), 'composer');
        $this->assertEquals($instance->getPackage(), 'client/library');

    }

    public function test_library_config_instance_get_factory_class_throws_exception_for_missing_factory_class()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Expected instance of Drewlabs\Libman\Contracts\LibraryFactoryInterface got \App\FT');
        $instance = LibraryConfig::new('FT', 'composer', 'client/library');
        $instance->factoryClass();
        $this->assertTrue(true);
    }

    public function test_library_config_instance_get_factory_class_return_instance_of_factory_class_interface_or_name_of_class_implenting_factory_class_interface()
    {
        $factoryClass = LibraryConfig::new('ClientLibrary', 'composer', 'client/library', ClientLibraryFactory::class)->factoryClass();
        $this->assertTrue(is_a($factoryClass, LibraryFactoryInterface::class, true));
    }

    public function test_library_config_on_deactivate_method_implementation()
    {
        /**
         * @var LibraryConfig
         */
        $instance = LibraryConfig::new('ClientLibrary', 'composer', 'client/library', ClientLibraryFactory::class);
        $times = 0;
        $instance->addDeactivateListener(function() use (&$times) {
            $times += 1;
        });
        $instance->deactivate();
        $instance->deactivate();
        $this->assertEquals($times, 2, 'Expect the onDeactive callaback to be called twice');
    }
}
