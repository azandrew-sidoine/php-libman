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

interface AuthCredentialsInterface
{
    /**
     * Returns the auth client identifier if required.
     *
     * @return string|null
     */
    public function id();

    /**
     * Returns the auth secret of the web service.
     *
     * @return string
     */
    public function secret();
}
