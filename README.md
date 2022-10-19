# Libman

Libman package is a small PHP utility library used in installing, creating business specific class instance using configuration based approach. The package comes YAML and JSON library definitions provider, used by the runtime instance builder.

## Concept

- What is a Library ?

A library in the `libman` package implementation is a pure abstraction arround a PHP object that performs a specific task, or a set of tasks. A PHP object having business domain specific implementation that that must be required by another PHP application.

- Why `Libman`

Libman tries to provides an API for building instances of a library (class) at runtime without developper programmatically creating instances. Developper provides configuration values about which type of package manager to use when adding library to the project, the factory class that is used to create an instance of the library.

Using developper configuration, written in YAML/JSON, or any library definition provider, `Libman` will be capable of create library instance at runtime when required in application code.

## Installation

To add the library to your PHP application using composer manager:

> composer require drewlabs/libman

## Usage

- Create a composer based library

Composer based libraries are PHP library installable using `PHP` composer package manager. The library manager assume by default that the library factory class is name `Factory`.

```php
use Drewlabs\Libman\LibraryConfig;
use Drewlabs\Libman\LibraryInstallerFactory;

// Creates an instance of the library to install
// API: LibraryConfig::new(string name, string type, string package)
$library = LibraryConfig::new('ClientLibrary', 'composer', 'client/library');
```

- Create a library configuration using a PHP factory class

A factory class based library configuration is a library configuration, that will use a PHP factory class to create an instance of library.

```php
use Drewlabs\Libman\LibraryConfig;
use Drewlabs\Libman\LibraryInstallerFactory;

// Creates an instance of the library to install
// API : LibraryConfig::new(string name, ?string type, ?string package, string $factoryClass)
$library = LibraryConfig::new('ClientLibrary', null, null, ClientLibraryFactory::class);
```

- Create a library instance using the `LibraryManager`

`LibraryManager` is a PHP class provided by the `libman` for creating libary instance based on configuration files or database configurations.

The code below use an `InMemoryConfigurationRepository` class that will read library configuration from a yaml definition file.

```php
use Drewlabs\Libman\LibraryManager;
use Drewlabs\Libma\InMemoryConfigurationRepository;
use Drewlabs\Libman\YAMLDefinitionsProvider;

$repository = new InMemoryConfigurationRepository(YAMLDefinitionsProvider::create('./path/to/libman.yml'));
$libManager =  new LibraryManager($repository);

// Create an instance a composer based library named 'drewlabs/crypt'
$libManager->resolveInstance('drewlabs/crypt');
```

The `LibraryManager` class comes with static method `createInstance` that takes a library configuration instance:

```php
use Drewlabs\Libman\LibraryManager;
use Drewlabs\Libman\LibraryConfig;

// Create an Instance of the library 
$instance = LibraryManager::createInstance(LibraryConfig::new('ClientLibrary));
```
