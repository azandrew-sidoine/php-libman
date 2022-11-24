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

interface LibraryRepositoryConfigInterface
{
    /**
     * Returns the repository type.
     *
     * @return string
     */
    public function getType();

    /**
     * Returns the URL to the source code respository.
     *
     * @return string
     */
    public function getURL();
}
