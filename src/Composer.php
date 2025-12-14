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

use Composer\InstalledVersions;
use Drewlabs\Libman\Contracts\InstallableLibraryConfigInterface;
use Drewlabs\Libman\Contracts\LibraryRepositoryConfigInterface;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

class Composer
{
    /**@var string */
    private static $binaryPath;

    /**
     * @var string
     */
    private static $vendorDir = '../../../';

    /** @deprecated */
    public static function binary(?string $path = null)
    {
        if (!is_null($path)) {
            static::$binaryPath = $path;
        }
        if (is_null(static::$vendorDir) && !is_null($vendor = static::findVendorDirectory())) {
            static::$vendorDir =  $vendor;
        }

        return static::$binaryPath;
    }

    /**
     * Initialize composer executable path and vendor directory
     * 
     * @param string|null $path 
     * @param string|null $vendorDir 
     * @return void 
     */
    public static function init(?string $path = null, ?string $vendorDir = null)
    {
        if (!is_null($path)) {
            static::$binaryPath = $path;
        }

        $vendorDir = is_null($vendorDir) ? static::findVendorDirectory() : $vendorDir;
        if (!is_null($vendorDir)) {
            static::$vendorDir =  $vendorDir;
        }

    }

    public static function vendorDirectory(?string $path = null)
    {
        if (!is_null($path)) {
            static::$vendorDir = $path;
        }

        return realpath(static::$vendorDir);
    }

    /**
     * @throws \RuntimeException
     *
     * @return mixed
     */
    public static function install(
        InstallableLibraryConfigInterface $library,
        ?\Closure $beforeCallBack = null,
        ?\Closure $completeCallback = null,
        ?\Closure $errorCallback = null
    ) {
        if (null === static::$vendorDir) {
            throw new \RuntimeException('Vendor directory configuration must not be null.');
        }
        $projectDir = \dirname(realpath(static::$vendorDir ?? static::findVendorDirectory() ?? '../../../'));
        if (!@is_file($composerJsonPath = realpath("$projectDir/composer.json"))) {
            throw new \RuntimeException("Missing composer.json file at the root \"$projectDir\" of the project directory. Makes sure the project is a composer based project. by running `composer init`");
        }
        if (!empty($respositories = $library->getRepository()) && $library->isPrivate()) {
            static::updateComposerJson($composerJsonPath, ['repositories' => static function () use ($respositories) {
                return array_map(static function (LibraryRepositoryConfigInterface $respository) {
                    return ['type' => $respository->getType(), 'url' => $respository->getURL()];
                }, array_filter(\is_array($respositories) ? $respositories : [$respositories]));
            }]);
        }
        // Build the package with version information if the version  is propvided
        // else return the $package name
        $version = $library->getVersion();
        $version = $version && (false === preg_match('/\d/', $version[0])) ? substr((string)$version, 1) : $version;
        $package = $library->getPackage();
        $package = $version && !$library->isPrivate() ? "$package@^$version" : "$package";
        $composerPath = is_null(static::$binaryPath) ?  (new ExecutableFinder())->find('composer') : static::$binaryPath;
        $composerPath = $composerPath ?? '/usr/local/bin/composer'; // Use default path if none is found
        if (!@is_file($composerPath)) {
            throw new \RuntimeException('No composer installer found at path: ' . $composerPath);
        }
        $commands = !@is_executable($composerPath) ? [$composerPath, 'require', $package, '--optimize-autoloader', '--update-no-dev',  '--no-ansi'] : ['php', $composerPath, 'require', $package, '--optimize-autoloader', '--update-no-dev',  '--no-ansi'];
        $process = new Process($commands, $projectDir);
        $process->start();
        if ($beforeCallBack) {
            $beforeCallBack();
        }
        while ($process->isRunning()) {
            // waiting for process to finish
        }
        if ($process->isSuccessful() && $completeCallback) {
            return $completeCallback($process->getOutput());
        }

        if (!$process->isSuccessful() && $errorCallback) {
            return $errorCallback($process->getErrorOutput());
        }

        return $process->getOutput();
    }

    /**
     * Based on Package name the method resolve absolute class path using relative class path.
     *
     * @throws \RuntimeException
     *
     * @return string|null
     */
    public static function resolveClassPath(string $package, string $relativeClassPath)
    {
        try {
            $packagePath = realpath(InstalledVersions::getInstallPath($package));
        } catch (\OutOfBoundsException $e) {
            return null;
        }
        $packageComposerJson = "$packagePath".\DIRECTORY_SEPARATOR.'composer.json';
        if (!@is_file($packageComposerJson)) {
            throw new \RuntimeException("Package $package is not a valid composer package");
        }
        // Parse the composer.json file
        $json = json_decode(file_get_contents($packageComposerJson), true);
        if (!isset($json['autoload']['psr-4']) || !\is_array($psr4ClassMap = $json['autoload']['psr-4'])) {
            throw new \RuntimeException("Package $package must be a valid psr4 package");
        }
        $classPath = null;
        foreach ($psr4ClassMap as $namespace => $value) {
            $path = $packagePath.(!empty($value) ? \DIRECTORY_SEPARATOR.trim($value, '/') : '').\DIRECTORY_SEPARATOR.str_replace('\\', \DIRECTORY_SEPARATOR, $relativeClassPath);
            if (@is_file($path) || @is_file("$path.php")) {
                $classPath = trim($namespace, '\\').'\\'.trim($relativeClassPath);
                break;
            }
        }

        return $classPath;
    }

    /**
     * Update some property of the composer json.
     * @return void
     */
    public static function updateComposerJson(string $path, array $values)
    {
        if (!@is_file($path)) {
            return;
        }
        $composerJson = json_decode(file_get_contents($path), true);
        if ($composerJson) {
            foreach ($values as $key => $value) {
                $value = !\is_string($value) && \is_callable($value) ? $value() : $value;
                if (!\is_string($key)) {
                    continue;
                }
                if (isset($composerJson[$key]) && \is_array($composerJson[$key]) && \is_array($value)) {
                    $composerJson[$key] = array_unique(array_merge($composerJson[$key], $value), \SORT_REGULAR);
                    continue;
                }
                $composerJson[$key] = $value;
            }
            // Write the updated composer json back to file
            file_put_contents($path, str_replace('\/', '/', json_encode($composerJson, \JSON_PRETTY_PRINT)));
        }
    }

    /** @return string|null */
    private static function findVendorDirectory(int $depth = 4)
    {
        // Assuming that the vendor directory is a top level directory having autoload.php
        // we start from the directory of the current and loop till the depth is reach
        // to try and locate a folder containing the autoload.php file
        // If the autoload.php is located we stop as the vendor directory is found
        $start = 1;
        $path = __DIR__;
        $vendorDir = null;
        while ($depth > $start) {
            $path = $path.'\/..\/';
            if (is_file(realpath(sprintf('%s/autoload.php', $path)))) {
                // Check if the content of the file has /composer/autoload_real.php
                $vendorDir = $path;
                break;
            }
        }

        return $vendorDir;
    }
}
