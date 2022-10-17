<?php

namespace Drewlabs\Libman\Contracts;

interface LibraryFactoryClassInterface
{
    /**
     * Creates a library object
     *
     * @param LibraryConfigInterface $config
     * @return object|\stdClass
     */
    public static function createInstance(LibraryConfigInterface $config);
}
