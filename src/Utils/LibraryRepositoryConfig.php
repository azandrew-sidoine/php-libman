<?php

namespace Drewlabs\Libman\Utils;

use Drewlabs\Libman\Contracts\LibraryRepositoryConfigInterface;
use Drewlabs\Libman\Traits\ArrayableType;

class LibraryRepositoryConfig implements LibraryRepositoryConfigInterface
{
    use ArrayableType;

    /**
     * Type of the repository
     * 
     * @var string
     */
    private $type;

    /**
     * HTTP URL to the repository
     * 
     * @var string
     */
    private $url;

    /**
     * Creates an instance of {@see LibraryRepositoryConfig} class
     * 
     * @param string $type 
     * @param string $url 
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
