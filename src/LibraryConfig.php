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

namespace Drewlabs\Libman;

use Drewlabs\Libman\Contracts\InstallableLibraryConfigInterface;
use Drewlabs\Libman\Traits\LibraryConfig as TraitsLibraryConfig;
use Drewlabs\Libman\Utils\Config;
use Drewlabs\Libman\Utils\LibaryIDFactory;

/**
 * @method static \Drewlabs\Libman\LibraryConfig new(string $name, $type)
 * @method static \Drewlabs\Libman\LibraryConfig new(string $name, string $type, ?string $package, ?string $factory)
 */
class LibraryConfig implements InstallableLibraryConfigInterface
{
    use TraitsLibraryConfig;

    /**
     * Creates an instance of {@see LibraryConfig} class.
     *
     * @return self
     */
    public function __construct(
        string $name,
        ?string $type = null,
        ?string $package = null,
        ?string $factory = null,
        array $config = []
    ) {
        $this->name = $name;
        $this->package = $package;
        $this->factory = $factory;
        $this->type = $type;
        $this->activated = true;

        //#region Added library id and configuration to extends previous implementation
        $this->id = (new LibaryIDFactory)->create();
        $this->configuration =  new Config($config);
        //#region Added library id and configuration to extends previous implementation
    }
}
