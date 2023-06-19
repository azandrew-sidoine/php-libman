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
        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $data = random_bytes(16);
        assert(16 === strlen($data));

        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0F | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3F | 0x80);

        return str_replace('-', '', vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4)));
    }

}
