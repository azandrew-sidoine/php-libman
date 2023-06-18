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

namespace Drewlabs\Libman;

use Drewlabs\Crypt\Encrypter\Crypt;
use Drewlabs\Crypt\Encrypter\Key;
use Drewlabs\Libman\Contracts\CryptInterface;

/**
 * Provides an adapter arround drewlabs/crypt library implementation.
 * 
 * The implementation will fallback to base64 encoding if the crypt library is not present
 * 
 * @package Drewlabs\Libman
 */
class CryptAdapter implements CryptInterface
{
    /**
     * 
     * @var CryptInterface|Crypt
     */
    private $instance;

    /**
     * Creates new class instance
     * 
     */
    public function __construct()
    {
        if (class_exists(Crypt::class)) {
            $this->instance = new class implements CryptInterface
            {

                public function encryptString(string $value): string
                {
                    $key = Key::make();
                    $scheme = sprintf("%s[%s,%s]", 'crypt', $key->__toString(), $key->cipher());
                    return sprintf("%s%s", $scheme, Crypt::new($key->__toString(), $key->cipher())->encryptString($value));
                }

                public function decryptString(string $encrypted): string
                {
                    $position = strpos($encrypted, ']');
                    if (false === $position) {
                        return $encrypted;
                    }
                    $scheme = substr($encrypted, 0, $position + 1);
                    $encoding = substr($scheme, strlen('crypt'));
                    list($key, $cipher) = explode(',', rtrim(ltrim($encoding, '['), ']'));
                    return Crypt::new($key, $cipher)->decryptString(substr($encrypted, strlen($scheme)));
                }
            };
        } else {
            $this->instance = new class implements CryptInterface
            {
                /**
                 * URI scheme prefix
                 * 
                 * @var string
                 */
                private $prefix = 'data:text/plain;base64';

                public function encryptString(string $value): string
                {
                    $scheme = sprintf("%s,", $this->prefix);
                    return sprintf("%s%s", $scheme, base64_encode($value));
                }

                public function decryptString(string $encrypted): string
                {
                    $scheme = sprintf("%s,", $this->prefix);
                    if (0 !== strpos($encrypted, $scheme)) {
                        return $encrypted;
                    }
                    return base64_decode(substr($encrypted, strlen($scheme)));
                }
            };
        }
    }

    public function encryptString(string $value): string
    {
        try {
            return $this->instance->encryptString($value);
        } catch (\Throwable $e) {
            // case the operation fails, we fallback to origin string value
            return $value;
        }
    }

    public function decryptString(string $encrypted): string
    {
        try {
            return $this->instance->decryptString($encrypted);
        } catch (\Throwable $e) {
            // Case the decrypt action fails, we fallback to original string
            // as maybe the input was not encrypted using the current interface
            return $encrypted;
        }
    }
}
