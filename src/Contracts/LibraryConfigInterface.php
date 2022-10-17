<?php

namespace Drewlabs\Libman\Contracts;

/**
 * Interface definition of a basic library configuration definition
 *
 * @package Drewlabs\Libman\Contracts
 */
interface LibraryConfigInterface
{

    /**
     * Returns the library name
     *
     * **Note**
     * Libraries are composer or PHP PSR4 packages (scope/library) that ca
     *
     * @return string
     */
    public function name();

    /**
     * Returns the class path of the library
     *
     * @return LibraryFactoryClassInterface|string
     */
    public function getFactoryClass();

    /**
     * Indicates whether the library is enabled or not
     *
     * @return bool
     */
    public function activated();
}
