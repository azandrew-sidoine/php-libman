<?php


namespace Drewlabs\Libman\Utils;

use Exception;

class LibaryIDFactory
{
    /**
     * Creates a UUID like library id
     * 
     * @return string 
     * @throws Exception 
     */
    public function create(): string
    {
        return str_replace('-', '', sprintf(
            '%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
            random_int(0, 65535),
            random_int(0, 65535),
            random_int(0, 65535),
            random_int(16384, 20479),
            random_int(32768, 49151),
            random_int(0, 65535),
            random_int(0, 65535),
            random_int(0, 65535)
        ));
    }
}
