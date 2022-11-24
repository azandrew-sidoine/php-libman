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

namespace Drewlabs\Libman\Utils;

use Drewlabs\Libman\Contracts\LibraryRepositoryConfigInterface;
use Drewlabs\Libman\Traits\ArrayableType;

class LibraryRepositoryConfig implements LibraryRepositoryConfigInterface
{
    use ArrayableType;

    /**
     * Type of the repository.
     *
     * @var string
     */
    private $type;

    /**
     * HTTP URL to the repository.
     *
     * @var string
     */
    private $url;

    /**
     * Creates an instance of {@see LibraryRepositoryConfig} class.
     *
     * @return void
     */
    public function __construct(string $type, string $url)
    {
        $this->type = $type;
        $this->url = $url;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getURL()
    {
        return $this->url;
    }
}
