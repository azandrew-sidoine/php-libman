<?php

namespace Drewlabs\Libman\Contracts;

interface AuthBasedLibraryConfigInterface
{
    /**
     * Returns the authorization credentials to the web service
     *
     * @return AuthCredentialsInterface
     */
    public function getAuth();
}