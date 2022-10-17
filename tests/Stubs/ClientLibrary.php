<?php

namespace Drewlabs\Libman\Tests\Stubs;

class ClientLibrary
{

    public function request(int $post)
    {
        return sprintf("Requesting /GET /comments/post/{$post}");
    }
}