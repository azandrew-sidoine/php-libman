<?php

use Drewlabs\Libman\Contracts\RepositoryInterface;
use Drewlabs\Libman\Utils\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{

    public function test_config_constructor()
    {
        $config = new Config();
        $this->assertInstanceOf(RepositoryInterface::class, $config);
    }


    public function test_config_get_method()
    {
        $config = new Config([
            'domain' => 'https://test-domain.tg',
            'active' => true,
            'invoicer_id' => 1,
            'client' => [
                'name' => 'TEST INVOICER',
                'scopes' => ['transactions:create']
            ]
        ]);
        $this->assertEquals('TEST INVOICER', $config->get('client.name'));
        $this->assertEquals(['transactions:create'], $config->get('client.scopes'));
        $this->assertEquals('https://test-domain.tg', $config->get('domain'));
    }

    public function test_config_merge_method()
    {
        $config = new Config([
            'domain' => 'https://test-domain.tg',
            'active' => true,
            'invoicer_id' => 1,
            'client' => [
                'name' => 'TEST INVOICER',
                'scopes' => ['transactions:create']
            ]
        ]);

        $config2 = $config->merge([
            'client' => [
                'name' => 'INVOICER',
                'expires_on' => '2022-07-05'
            ]
        ]);

        $this->assertEquals('TEST INVOICER', $config->get('client.name'));
        $this->assertEquals(['transactions:create'], $config->get('client.scopes'));
        $this->assertEquals('https://test-domain.tg', $config->get('domain'));
        $this->assertTrue(null === $config->get('client.expires_on'));

        // config 2
        $this->assertEquals('INVOICER', $config2->get('client.name'));
        $this->assertEquals(['transactions:create'], $config2->get('client.scopes'));
        $this->assertEquals('https://test-domain.tg', $config2->get('domain'));
        $this->assertEquals('2022-07-05', $config2->get('client.expires_on'));
    }

}
