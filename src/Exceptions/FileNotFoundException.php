<?php

namespace Drewlabs\Libman\Exceptions;

use Exception;

class FileNotFoundException extends Exception
{
    public function __construct(string $path)
    {
        $message = "Missing file at path $path.";
        parent::__construct($message);
    }
}