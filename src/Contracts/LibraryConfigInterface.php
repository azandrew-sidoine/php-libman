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
     * Returns the library name.
     *
     * **Note**
     * Libraries are composer or PHP PSR4 packages (scope/library) that ca
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
}
