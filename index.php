<?php

use Composer\InstalledVersions;

require_once __DIR__ . '/vendor/autoload.php';

$package = 'symfony/process';
$pipesClass = 'Pipes\\PipesInterface';
$packagePath = realpath(InstalledVersions::getInstallPath($package));
$packageComposerJson = "$packagePath" . DIRECTORY_SEPARATOR . 'composer.json';
if (!@is_file($packageComposerJson)) {
    throw new RuntimeException("Package $package is not a valid composer package");
}
// Parse the composer.json file
$json = json_decode(file_get_contents($packageComposerJson), true);

if (!isset($json['autoload']['psr-4']) || !is_array($psr4ClassMap = $json['autoload']['psr-4'])) {
    throw new RuntimeException("Package $package must be a valid psr4 package");
}

$class = null;
foreach ($psr4ClassMap as $namespace => $value) {
    $classPath = $packagePath . (!empty($value) ? DIRECTORY_SEPARATOR . trim($value, '/') : "") . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $pipesClass);
    if (@is_file($classPath) || @is_file("$classPath.php")) {
        $class = trim($namespace, '\\') . '\\' . trim($pipesClass);
        break;
    }
}

print_r($class);