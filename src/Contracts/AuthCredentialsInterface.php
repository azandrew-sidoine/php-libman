<?php

namespace Drewlabs\Libman\Contracts;

interface AuthCredentialsInterface
{

    /**
     * Returns the auth client identifier if required
     *
     * @return string|null
     */
    public function id();

    /**
     * Returns the auth secret of the web service
     *
     * @return string
     */
    public function secret();
}
