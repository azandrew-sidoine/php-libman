<?php

use Drewlabs\Libman\ComposerLibraryInstaller;
use Drewlabs\Libman\LibraryConfig;
use Drewlabs\Libman\LibraryInstallerFactory;
use PHPUnit\Framework\TestCase;

class LibraryInstallerFactoryTest extends TestCase
{


    public function test_create_Library_installer()
    {
        $installer = LibraryInstallerFactory::create(LibraryConfig::new('ClientLibrary', 'composer', 'client/library', ClientLibraryFactory::class));
        $this->assertInstanceOf(ComposerLibraryInstaller::class, $installer);
    }

    public function test_create_Library_installer_throws_exception_for_missing_installer()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No installer provided for npm');
        $installer = LibraryInstallerFactory::create(LibraryConfig::new('ClientLibrary', 'npm', 'client/library'));
        $this->assertInstanceOf(ComposerLibraryInstaller::class, $installer);
    }
}