<?php

namespace Drewlabs\Libman\Contracts;

interface LibraryFactoryInterface
{
    /**
     * Creates a library object
     *
     * @param LibraryConfigInterface $config
     * @return object|\stdClass
     */
    public static function createInstance(LibraryConfigInterface $config);
}
