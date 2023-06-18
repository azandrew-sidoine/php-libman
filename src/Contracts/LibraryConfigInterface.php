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

namespace Drewlabs\Libman\Contracts;

/**
 * Interface definition of a basic library configuration definition.
 */
interface LibraryConfigInterface
{
    /**
     * return the library name.
     *
     * **Note**
     * Library for unicity should be composer or PHP PSR4 package name (scope/library) compatible
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the library factory class name configured for the library.
     *
     * @return string
     */
    public function getFactory();

    /**
     * Returns the class path of the library.
     *
     * @return LibraryFactoryInterface|string
     */
    public function factoryClass();

    /**
     * Indicates whether the library is enabled or not.
     *
     * @return bool
     */
    public function activated();

    /**
     * returns the library unique identifier. Query for a library
     * instance will be done on library `id` first before falling
     * back to legacy search on name
     * 
     * @return string 
     */
    public function id();

    /**
     * returns the library configuration repository
     * 
     * @return RepositoryInterface 
     */
    public function getConfiguration(): RepositoryInterface;
}
