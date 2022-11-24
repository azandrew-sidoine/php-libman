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

use Drewlabs\Libman\Contracts\AuthBasedLibraryConfigInterface;
use Drewlabs\Libman\Contracts\AuthCredentialsInterface;
use Drewlabs\Libman\Contracts\InstallableLibraryConfigInterface;
use Drewlabs\Libman\Contracts\WebServiceLibraryConfigInterface;
use Drewlabs\Libman\Traits\LibraryConfig;
use Drewlabs\Libman\Utils\AuthCredentials;

/**
 * @method static \Drewlabs\Libman\WebserviceLibraryConfig new(string $name, string $type, string $host, string $apikey)
 * @method static \Drewlabs\Libman\WebserviceLibraryConfig new(string $name, string $type, string $host, string $apikey, string $clientid)
 * @method static \Drewlabs\Libman\WebserviceLibraryConfig new(string $name, string $type, string $host, string $apikey, string $clientid, string $package, string $factory)
 */
class WebserviceLibraryConfig implements InstallableLibraryConfigInterface, WebServiceLibraryConfigInterface, AuthBasedLibraryConfigInterface
{
    use LibraryConfig;

    /**
     * Webservice host.
     *
     * @var mixed
     */
    private $host;

    /**
     * @var AuthCredentialsInterface
     */
    private $auth;

    /**
     * @return self
     */
    public function __construct(
        string $name,
        string $type = null,
        string $host = null,
        string $apiKey = null,
        string $client = null,
        string $package = null,
        string $factory = null
    ) {
        $this->name = $name;
        $this->package = $package;
        $this->factory = $factory;
        $this->type = $type;
        $this->activated = true;
        $this->host = $host;
        $this->auth = new AuthCredentials($client, $apiKey);
    }

    public function setAuth($value)
    {
        $value = \is_object($value) ? get_object_vars($value) : $value;
        if (!\is_array($value)) {
            throw new \InvalidArgumentException('Expect array/object as parameter, got '.(null !== $value && \is_object($value) ? \get_class($value) : \gettype($value)));
        }
        $this->auth = AuthCredentials::fromArray($value);
    }

    /**
     * {@inheritDoc}
     */
    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * {@inheritDoc}
     */
    public function getHost()
    {
        return $this->host;
    }
}
