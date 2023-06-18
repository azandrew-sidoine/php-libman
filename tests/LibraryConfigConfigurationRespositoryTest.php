<?php

use Drewlabs\Libman\LibraryConfig;
use Drewlabs\Libman\Utils\LibaryIDFactory;
use PHPUnit\Framework\TestCase;

class LibraryConfigConfigurationRespositoryTest extends TestCase
{

    public function test_create_library_without_configuration_keyword_creates_an_empty_repository()
    {
        $library = LibraryConfig::create([
            'name' => 'drewlabs/crypt',
            'type' => 'composer',
            'package' => 'drewlabs/crypt',
            'version' => '0.2.0'
        ]);
        $this->assertTrue($library->getConfiguration()->isEmpty());
        $this->assertTrue(is_string($library->id()));
    }

    public function test_create_library_with_configuration()
    {
        $id = (new LibaryIDFactory)->create();
        $library = LibraryConfig::create([
            'name' => 'drewlabs/crypt',
            'type' => 'composer',
            'package' => 'drewlabs/crypt',
            'version' => '0.2.0',
            'id' => $id,
            'configuration' => [
                'domain' => 'https://test-domain.tg',
            ]
        ]);
        $this->assertTrue('https://test-domain.tg' === $library->getConfiguration()->get('domain'));
        $this->assertEquals($id, $library->id());
    }
}