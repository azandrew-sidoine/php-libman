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

interface LibraryDefinitionsProvider
{
    /**
     * Add a library definition to the provider.
     *
     * @return void
     */
    public function addDefinition(array $value);

    /**
     * Return the an iterable of library definitions.
     *
     * @return array|\Traversable
     */
    public function definitions();

    /**
     * Persist the definition to the disk.
     *
     * @return void
     */
    public function persist();
}
