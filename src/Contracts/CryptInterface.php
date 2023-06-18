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

namespace Drewlabs\Libman\Contracts;

interface CryptInterface
{
    /**
     * Encrypt a given string and return the encrypted string.
     *
     * @throws EncryptionException
     *
     * @return string
     */
    public function encryptString(string $value): string;

    /**
     * Decrypt an encrypted string and return the raw string.
     *
     * @throws \Exception
     *
     * @return string
     */
    public function decryptString(string $encrypted): string;
}
