<?php

namespace Drewlabs\Libman\Exception;

use Exception;

class ExtensionNotLoadedException extends Exception
{
    /**
     * 
     * @param string $extension 
     * @return self 
     */
    public function __construct(string $extension)
    {
        $message = "Extension $extension is not loaded by the PHP interpreter. Consider installing $extension extension and adding to your php.ini file";
        parent::__construct($message);
    }
}