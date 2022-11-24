<?php

namespace Drewlabs\Libman;

use Drewlabs\Libman\Contracts\InstallableLibraryConfigInterface;
use Drewlabs\Libman\Traits\LibraryConfig as TraitsLibraryConfig;

/**
 *
 * @method static \Drewlabs\Libman\LibraryConfig new(string $name, $type)
 * @method static \Drewlabs\Libman\LibraryConfig new(string $name, string $type, ?string $package, ?string $factory)
 *
 * @package Drewlabs\Libman
 */
class LibraryConfig implements InstallableLibraryConfigInterface
{
    use TraitsLibraryConfig;

    /**
     * Creates an instance of {@see LibraryConfig} class
     * 
     * @param string $name 
     * @param null|string $type 
     * @param null|string $package 
     * @param null|string $factory
     * @return self 
     */
    public function __construct(
        string $name,
        string $type = null,
        string $package = null,
        string $factory = null
    ) {

        $this->name = $name;
        $this->package  = $package;
        $this->factory = $factory;
        $this->type = $type;
        $this->activated = true;
    }
}
