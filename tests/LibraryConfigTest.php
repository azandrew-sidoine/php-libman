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

namespace Drewlabs\Libman\Tests;

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
        $this->assertSame($instance->getName(), 'ClientLibrary');
        $this->assertSame($instance->getType(), 'composer');
        $this->assertSame($instance->getPackage(), 'client/library');
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
        $instance->addDeactivateListener(static function () use (&$times) {
            ++$times;
        });
        $instance->deactivate();
        $instance->deactivate();
        $this->assertSame($times, 2, 'Expect the onDeactive callaback to be called twice');
    }
}
