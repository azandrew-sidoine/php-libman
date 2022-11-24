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

namespace Drewlabs\Libman\Exceptions;

class ExtensionNotLoadedException extends \Exception
{
    /**
     * @return self
     */
    public function __construct(string $extension)
    {
        $message = "Extension $extension is not loaded by the PHP interpreter. Consider installing $extension extension and adding to your php.ini file";
        parent::__construct($message);
    }
}
