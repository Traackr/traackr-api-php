<?php

namespace Traackr;

// Declare the interface
interface ApiCacheInterface
{
   public function isCacheable($key);
   public function read($key, $custKey = '');
   public function write($key, $value, $custKey = '');
   public function expire($custKey = '');
}

?>