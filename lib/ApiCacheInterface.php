<?php

namespace Traackr;

// Declare the interface
interface ApiCacheInterface
{
    public function read($key);
    public function write($key, $value);
}

?>