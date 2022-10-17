<?php

namespace Drewlabs\Libman;

use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;
use Composer\InstalledVersions;
use OutOfBoundsException;
use RuntimeException;

class Composer
{
    /**
     * 
     * @var string
     */
    private static $binaryPath = '/usr/local/bin/composer';

    /**
     * 
     * @var string
     */
    private static $vendorDir = '../../../';

    public static function binary(?string $path = null)
    {
        if (null !== $path) {
            static::$binaryPath = $path;
        }
        if (null === static::$vendorDir) {
            static::$vendorDir = static::findVendorDirectory();
        }
        return static::$binaryPath;
    }


    public static function vendorDirectory(string $directoryPath = null)
    {
        if (null !== $directoryPath) {
            static::$vendorDir = $directoryPath;
        }
        return realpath(static::$vendorDir);
    }

    /**
     * 
     * @param mixed $package 
     * @param string|null $version 
     * @return Generator<int, string, mixed, void> 
     * @throws RuntimeException 
     */
    public static function install($package, string $version = null)
    {
        if (null === static::$vendorDir) {
            throw new \RuntimeException("Vendor directory configuration must not be null.");
        }
        $projectDir = dirname(realpath(static::$vendorDir));
        if (!@is_file(realpath("$projectDir/composer.json"))) {
            throw new \RuntimeException("Missing composer.json file at the root of the project directory. Makes sure the project is a composer based project. by running `composer init`");
        }
        // Build the package with version information if the version  is propvided
        // else return the $package name 
        $package = $version ? "$package@^$version" : "$package";
        if (null === static::$binaryPath) {
            static::$binaryPath = (new ExecutableFinder())->find('composer');
        }
        if (null === static::$binaryPath || !@is_file(static::$binaryPath)) {
            throw new \RuntimeException("No composer installer found at path: ", static::$binaryPath);
        }
        $commands = !@is_executable(static::$binaryPath) ? [static::$binaryPath, 'require', $package, '--optimize-autoloader',  '--no-ansi'] : ['php', static::$binaryPath, 'require', $package, '--optimize-autoloader',  '--no-ansi'];
        $process = new Process($commands, $projectDir);
        $process->start();
        while ($process->isRunning()) {
            // waiting for process to finish
        }
        yield $process->getOutput();
    }

    /**
     * Based on Package name the method resolve absolute class path using relative class path 
     * 
     * @param string $package 
     * @param string $relativeClassPath 
     * @return null 
     * @throws OutOfBoundsException
     * @throws RuntimeException 
     */
    public static function resolveClassPath(string $package, string $relativeClassPath)
    {
        $packagePath = realpath(InstalledVersions::getInstallPath($package));
        $packageComposerJson = "$packagePath" . DIRECTORY_SEPARATOR . 'composer.json';
        if (!@is_file($packageComposerJson)) {
            throw new \RuntimeException("Package $package is not a valid composer package");
        }
        // Parse the composer.json file
        $json = json_decode(file_get_contents($packageComposerJson), true);
        if (!isset($json['autoload']['psr-4']) || !is_array($psr4ClassMap = $json['autoload']['psr-4'])) {
            throw new \RuntimeException("Package $package must be a valid psr4 package");
        }
        $classPath = null;
        foreach ($psr4ClassMap as $namespace => $value) {
            $path = $packagePath . (!empty($value) ? DIRECTORY_SEPARATOR . trim($value, '/') : "") . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClassPath);
            if (@is_file($path) || @is_file("$path.php")) {
                $classPath = trim($namespace, '\\') . '\\' . trim($relativeClassPath);
                break;
            }
        }
        return $classPath;
    }

    /**
     * 
     * @param int $depth 
     * @return string 
     */
    private static function findVendorDirectory(int $depth = 4)
    {
        // Assuming that the vendor directory is a top level directory having autoload.php
        // we start from the directory of the current and loop till the depth is reach
        // to try and locate a folder containing the autoload.php file
        // If the autoload.php is located we stop as the vendor directory is found
        $start = 1;
        $path = __DIR__;
        $vendorDir = '../../../';
        while ($depth > $start) {
            $path = $path . '\/..\/';
            if (is_file(realpath(sprintf("%s/autoload.php", $vendorDir)))) {
                // Check if the content of the file has /composer/autoload_real.php
                $vendorDir = $path;
                break;
            }
        }
        return $vendorDir;
    }
}
