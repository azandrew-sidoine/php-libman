<?php

namespace Drewlabs\Libman\Contracts;

interface WebServiceLibraryConfigInterface extends LibraryConfigInterface
{
    /**
     * Returns the uri or IP of the host
     *
     * @return string
     */
    public function getHost();
}
