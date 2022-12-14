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
use Drewlabs\Libman\Contracts\LibraryInstaller;

/**
 * Provides an installation process implementation for PHP package
 * that use composer as package manager for installation.
 */
class ComposerLibraryInstaller implements LibraryInstaller
{
    /**
     * Event listenerrs during installation process.
     *
     * @var array
     */
    private $listeners = [
        'installing' => [],
        'complete' => [],
    ];

    public function addListener(string $event, callable $callback)
    {
        if (!\in_array($event, ['installing', 'complete'], true)) {
            return $this;
        }
        if (isset($this->listeners[$event])) {
            $this->listeners[$event][] = $callback;

            return $this;
        }
        $this->listeners[$event] = [$callback];

        return $this;
    }

    public function install(InstallableLibraryConfigInterface $libraryConfig, \Closure $errorCallback = null)
    {
        return Composer::install($libraryConfig, function () {
            $this->processListenerFor('installing');
        }, function ($value) {
            $this->processListenerFor('complete', $value);
        }, $errorCallback);
    }

    private function processListenerFor(string $event, ...$params)
    {
        if (isset($this->listeners[$event]) && !empty($this->listeners[$event])) {
            foreach ($this->listeners[$event] as $value) {
                if (null !== $value) {
                    $value(...$params);
                    continue;
                }
            }
        }
    }
}
