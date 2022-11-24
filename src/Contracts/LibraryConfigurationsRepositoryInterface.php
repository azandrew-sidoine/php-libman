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

interface LibraryConfigurationsRepositoryInterface
{
    /**
     * Select a library configuration using developer provided id.
     *
     * @param string|int $id
     *
     * @return LibraryConfigInterface|InstallableLibraryConfigInterface
     */
    public function select($id);

    /**
     * Add a new library to the repostory.
     *
     * @return void
     */
    public function add(LibraryConfigInterface $libraryConfig);

    /**
     * Returns the list of available library configuration.
     *
     * @param \Closure<object|array,bool>|null $predicate
     *
     * @return \Traversable
     */
    public function selectAll(\Closure $predicate = null);
}
