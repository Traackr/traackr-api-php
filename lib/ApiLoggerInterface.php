<?php

namespace Traackr;

// Declare the interface
interface ApiLoggerInterface
{
   public function debug($string);
   public function error($string);
}

?>