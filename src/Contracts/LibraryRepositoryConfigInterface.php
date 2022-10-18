<?php

namespace Drewlabs\Libman\Contracts;

interface LibraryRepositoryConfigInterface
{
    /**
     * Returns the repository type
     * 
     * @return string 
     */
    public function getType();

    /**
     * Returns the URL to the source code respository
     * 
     * @return string 
     */
    public function getURL();
}