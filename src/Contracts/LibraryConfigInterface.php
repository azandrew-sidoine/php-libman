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
    public function getName();

    /**
     * Returns the library factory class name configured for the library
     * 
     * @return string 
     */
    public function getFactory();

    /**
     * Returns the class path of the library
     *
     * @return LibraryFactoryInterface|string
     */
    public function factoryClass();

    /**
     * Indicates whether the library is enabled or not
     *
     * @return bool
     */
    public function activated();
}
