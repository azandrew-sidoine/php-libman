<?php

namespace Drewlabs\Libman\Tests;

use Drewlabs\Libman\CryptAdapter;
use PHPUnit\Framework\TestCase;

class CryptAdapterTest extends TestCase
{

    public function test_crypt_adapter_encrypt()
    {
        $crypt = new CryptAdapter();
        $result  = $crypt->encryptString('MyString');
        $this->assertTrue(0 === strpos($result, 'data:text/plain;base64,'));
    }

    public function test_crypt_adapter_decrypt()
    {
        $crypt = new CryptAdapter();
        $result  = $crypt->encryptString('MyString');
        $this->assertEquals('MyString', $crypt->decryptString($result));
    }

}