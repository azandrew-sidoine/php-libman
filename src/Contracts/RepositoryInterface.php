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

use JsonSerializable;

interface RepositoryInterface extends JsonSerializable
{

    /**
     * Query/Get value for a matching `$name` in the repository
     * 
     * @param string $name 
     * @param mixed $default
     * 
     * @return mixed 
     */
    public function get(string $name, $default = null);
}
