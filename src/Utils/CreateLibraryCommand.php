<?php

namespace Drewlabs\Libman\Utils;

use Drewlabs\Libman\Contracts\LibraryConfigurationsRepositoryInterface;
use Drewlabs\Libman\Contracts\WebServiceLibraryConfigInterface;
use Drewlabs\Libman\LibraryConfig;
use Drewlabs\Libman\LibraryManager;
use Drewlabs\Libman\WebserviceLibraryConfig;
use ReflectionException;
use RuntimeException;
use UnexpectedValueException;

class CreateLibraryCommand
{
    /**
     * 
     * @var array<string,mixed>
     */
    private $arguments = [];

    /**
     * 
     * @var array<string,mixed>
     */
    private $options = [];

    /**
     * 
     * @var callable
     */
    private $errorCallback;

    /**
     * 
     * @var callback
     */
    private $infoCallback;

    /**
     * 
     * @var callable
     */
    private $promptCallback;

    /**
     * 
     * @var callback
     */
    private $choiceCallback;

    /**
     * 
     * @var callback
     */
    private $confirmCallback;

    /**
     * 
     * @var LibraryConfigurationsRepositoryInterface
     */
    private $repository;

    /**
     * Creates an instance of the command
     * 
     * @param LibraryConfigurationsRepositoryInterface $repository 
     */
    public function __construct(LibraryConfigurationsRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Add a list of argument to the command arguments property
     * 
     * @param array $arguments 
     * @return void 
     * @throws UnexpectedValueException 
     */
    public function setArguments(array $arguments = [])
    {
        foreach ($arguments as $key => $value) {
            if (!is_string($key)) {
                throw new UnexpectedValueException("Array keys must be PHP string");
            }
            $this->arguments[$key] = $value;
        }
    }


    /**
     * Push a new argument to the argument stack
     * 
     * @param string $name 
     * @param mixed $value 
     * 
     * @return self 
     */
    public function setArgument(string $name, $value)
    {
        $this->arguments[$name] = $value;
        return $this;
    }

    /**
     * Add a list of options to the options bag
     * 
     * @param array $options 
     * @return void 
     * @throws UnexpectedValueException 
     */
    public function setOptions(array $options = [])
    {
        foreach ($options as $key => $value) {
            if (!is_string($key)) {
                throw new UnexpectedValueException("Array keys must be PHP string");
            }
            if (substr($key, 0, 2) === '--') {
                $key = substr($key, 2);
            }
            $this->options[$key] = $value;
        }
    }

    /**
     * Add a new option to the options bag
     * 
     * @param string $name 
     * @param mixed $value 
     * 
     * @return self 
     */
    public function setOption(string $name, $value)
    {

        $this->options[$name] = $value;
        return $this;
    }

    /**
     * The the callback to invoke when command failed with an error message.
     * The callback takes a parameter the erro string to be displayed
     * 
     * @param callable $handler 
     * @return self 
     */
    public function onPrintError(callable $handler)
    {
        $this->errorCallback = $handler;
        return $this;
    }

    /**
     * The the callback to invoketo display information to the user
     * The callback takes a parameter the information string to be displayed
     * 
     * @param callable $handler
     * 
     * @return self 
     */
    public function onPrint(callable $handler)
    {
        $this->infoCallback = $handler;
        return $this;
    }

    /**
     * Set the callback function use to get input from user
     * 
     * @param callable $prompt 
     * @return self 
     */
    public function onPrompt(callable $prompt)
    {
        $this->promptCallback = $prompt;
        return $this;
    }

    /**
     * Set the callback function use to get input from user providing user
     * with multiple choice
     * 
     * @param callable $prompt 
     * @return self 
     */
    public function onChoice(callable $fn)
    {
        $this->choiceCallback = $fn;
        return $this;
    }

    /**
     * Set a confirmation callback for the command
     * 
     * @param callable $fn 
     * @return self 
     */
    public function onConfirm(callable $fn)
    {
        $this->confirmCallback = $fn;
        return $this;
    }

    /**
     * Private argument getter
     * 
     * @param string $name 
     * @return mixed 
     */
    private function argument(string $name)
    {
        return $this->arguments[$name] ?? null;
    }

    /**
     * Private option getter
     * 
     * @param string $name 
     * @return mixed 
     */
    private function option(string $name)
    {
        return $this->options[$name] ?? null;
    }

    /**
     * Actually executes the configured command
     * 
     * @return mixed 
     * @throws RuntimeException 
     * @throws ReflectionException 
     */
    public function execute()
    {
        $this->assertCallbacks();
        //#region Variables initialization
        $name = $this->argument('name');
        $factory = $this->option('factory');
        $repositories = [];
        $version = null;
        $private = false;
        $package = null;
        $id = null;
        $secret = null;
        //#region Variables initialization

        $type = ($this->choiceCallback)('What is the library type ?', ['composer', 'default'], 1);

        //# Choice, Confirm prompts
        if (strtolower($type) === 'composer') {
            $package = ($this->promptCallback)('What is the composer package name ?');
            if (empty($package)) {
                return ($this->errorCallback)('Composer based library require the package name');
            }
            $version = ($this->promptCallback)('What is the package version to install (1.x, 2.x.x, etc..)?');
            $private = ($this->confirmCallback)('Is the package a private package ?');
            if (true === $private) {
                $repositories[] = $this->getRepositoryConfig();
                $continue = ($this->confirmCallback)('Do you wish to add another repository ?');
                while ($continue) {
                    $repositories[] = $this->getRepositoryConfig();
                    $continue = ($this->confirmCallback)('Do you wish to add another repository ?');
                }
            }
        }
        $service = ($this->choiceCallback)('What is the type of the API', ['basic', 'http']);
        if (strtolower($service) === 'http') {
            $host = ($this->promptCallback)('Please specify the API host url');
        }

        if (($this->confirmCallback)('Does library require authentication')) {
            $id = ($this->promptCallback)('Please specify the API host api key. Press enter for blank', null);
            $secret = ($this->promptCallback)('Please specify the API api secret. Press enter for blank', null);
        }

        $config = $this->getLibraryExtaConfigurations();

        $libConfig = strtolower($service) === 'http' ?
            WebserviceLibraryConfig::create([
                'name' => $name,
                'type' => $type,
                'host' => $host,
                'secret' => $secret,
                'id' => $id,
                'package' => $package,
                'factory' => $factory,
                'version' => $version,
                'host' => $host,
                'auth' => [
                    'id' => $id,
                    'secret' => $secret
                ],
                'configuration' => $config ?? []
            ]) :
            LibraryConfig::create([
                'name' => $name,
                'type' => $type,
                'package' => $package,
                'factory' => $factory,
                'version' => $version,
                'auth' => [
                    'id' => $id,
                    'secret' => $secret
                ],
                'configuration' => $config ?? []
            ]);

        // Add the library to the libraries database
        $this->repository->add($libConfig);

        ($this->infoCallback)('Library added successfully!');

        ($this->infoCallback)("Name: " . $libConfig->getName());
        ($this->infoCallback)("Type: " . $libConfig->getType());

        if ($package = $libConfig->getPackage()) {
            ($this->infoCallback)("Package: " . $package);
        }

        if ($factory = $libConfig->getFactory()) {
            ($this->infoCallback)("Factory: " . $factory);
        }

        if (($libConfig instanceof WebServiceLibraryConfigInterface) && ($host = $libConfig->getHost())) {
            ($this->infoCallback)("HTTP Host: " . $host);
        }

        if ($type === 'composer') {
            $install = ($this->confirmCallback)('Do you wish to install the library using the project composer binary ?');
            if (true === $install) {
                ($this->infoCallback)('Installing composer library, please wait...');
                LibraryManager::install($libConfig);
                ($this->infoCallback)('Library source code installed successfully!');
            }
        }
        //# Choice, Confirm prompts
        return;
    }

    /**
     * Assert that all callback are set before invoking the execute() method
     * 
     * @return void 
     * @throws RuntimeException 
     */
    private function assertCallbacks()
    {
        $callbacks = [
            'errorCallback' => $this->errorCallback,
            'infoCallback' => $this->infoCallback,
            'promptCallback' => $this->promptCallback,
            'choiceCallback' => $this->choiceCallback,
            'confirmCallback' => $this->confirmCallback,
        ];
        foreach ($callbacks as $key => $value) {
            if (!is_callable($value)) {
                throw new RuntimeException('Before executing command, make sure you call onPrintError(), onPrint(), onPrompt(), onChoice(), onConfirm() methods to bind the callback for each cases');
            }
        }
    }

    /**
     * Creates a configuration array for the library
     * 
     * @return array 
     */
    private function getLibraryExtaConfigurations()
    {
        $config = [];
        $input = boolval(($this->confirmCallback)('Does the library depends on extras configuration ?'));
        while ($input) {
            $name = ($this->promptCallback)('Please configuration name: ');
            $value = ($this->promptCallback)('Please enter the configuration value: ');
            if (null === $value || (null === $name)) {
                break;
            }
            // Set value for an array.
            // Case the name contains `.`, the algorithm recursively set the nested properties
            $this->setValue($config, $name, $value);
            $input = boolval(($this->confirmCallback)('Do you wish to add another configuration value ?'));
        }
        return $config;
    }

    /**
     * Set property value for an array
     * 
     * @param mixed $array 
     * @param string $key 
     * @param mixed $value 
     * @return mixed 
     */
    private function setValue(&$array, string $key, $value)
    {
        if (null === $key) {
            return $array = $value;
        }

        $keys = explode('.', (string) $key);

        while (\count($keys) > 1) {
            $key = array_shift($keys);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (!isset($array[$key]) || !\is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }

    private function getRepositoryConfig()
    {
        $type = ($this->promptCallback)('What is the repository type ?');
        $url = ($this->promptCallback)('Please enter the private repository URL: ');
        return ['type' => $type, 'url' => $url];
    }
}
