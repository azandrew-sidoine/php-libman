<?php

namespace Drewlabs\Libman\Utils;

use Drewlabs\Libman\Contracts\AuthCredentialsInterface;
use Drewlabs\Libman\Traits\ArrayableType;

class AuthCredentials implements AuthCredentialsInterface
{
    use ArrayableType;

    /**
     * Auth client id
     * 
     * @var null|string
     */
    private $id;

    /**
     * Auth client secret
     * 
     * @var null|string
     */
    private $secret;

    /**
     * Creates an instance of {@see AuthCredentials} class
     * 
     * @param null|string $id 
     * @param null|string $secret 
     * @return void 
     */
    public function __construct(?string $id = null, ?string $secret = null)
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
