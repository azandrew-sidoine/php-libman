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

namespace Drewlabs\Libman\Utils;

use Drewlabs\Libman\Contracts\AuthCredentialsInterface;
use Drewlabs\Libman\Traits\ArrayableType;

class AuthCredentials implements AuthCredentialsInterface
{
    use ArrayableType;

    /**
     * Auth client id.
     *
     * @var string|null
     */
    private $id;

    /**
     * Auth client secret.
     *
     * @var string|null
     */
    private $secret;

    /**
     * Creates an instance of {@see AuthCredentials} class.
     *
     * @return void
     */
    public function __construct(string $id = null, string $secret = null)
    {
        $this->id = $id;
        $this->secret = $secret;
    }

    public function id()
    {
        return $this->id;
    }

    public function secret()
    {
        return $this->secret;
    }
}
