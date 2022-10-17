<?php

namespace Drewlabs\Libman;

use Drewlabs\Libman\Contracts\AuthBasedLibraryConfigInterface;
use Drewlabs\Libman\Contracts\AuthCredentialsInterface;
use Drewlabs\Libman\Contracts\InstallableLibraryConfigInterface;
use Drewlabs\Libman\Contracts\WebServiceLibraryConfigInterface;
use Drewlabs\Libman\Traits\LibraryConfig;

/**
 *
 * @method \Drewlabs\Libman\WebserviceLibraryConfig static new(string $name, string $type, string $host, string $apikey)
 * @method \Drewlabs\Libman\WebserviceLibraryConfig static new(string $name, string $type, string $host, string $apikey, string $clientid)
 * @method \Drewlabs\Libman\WebserviceLibraryConfig static new(string $name, string $type, string $host, string $apikey, string $clientid, string $package, string $factory)
 *
 * @package Drewlabs\Libman
 */
class WebserviceLibraryConfig implements
    InstallableLibraryConfigInterface,
    WebServiceLibraryConfigInterface,
    AuthBasedLibraryConfigInterface
{
    use LibraryConfig;

    /**
     *
     * @var string
     */
    private $apiKey;

    /**
     *
     * @var string
     */
    private $client;

    /**
     * Webservice host
     *
     * @var mixed
     */
    private $host;

    /**
     * 
     * @param string $name 
     * @param null|string $type 
     * @param null|string $host 
     * @param null|string $apiKey 
     * @param null|string $client 
     * @param null|string $package 
     * @param null|string $factory 
     * @return self 
     */
    public function __construct(
        string $name,
        ?string $type = null,
        ?string $host = null,
        ?string $apiKey = null,
        ?string $client = null,
        ?string $package = null,
        ?string $factory = null
    ) {

        $this->name = $name;
        $this->package  = $package;
        $this->factory = $factory;
        $this->type = $type;
        $this->activated = true;
        $this->host = $host;
        $this->apiKey = $apiKey;
        $this->client = $client;
    }

    /**
     * {@inheritDoc}
     */
    public function getAuthCredentials()
    {
        return new class($this->client, $this->apiKey) implements AuthCredentialsInterface
        {
            // Properties defintions
            /**
             * 
             * @var null|string
             */
            private $id;

            /**
             * 
             * @var null|string
             */
            private $secret;

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
        };
    }

    /**
     * {@inheritDoc}
     */
    public function getHost()
    {
        return $this->host;
    }
}
