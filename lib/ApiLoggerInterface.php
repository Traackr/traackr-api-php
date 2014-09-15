<?php

namespace Traackr;

/**
 * Allows integration between our debug/error messages and the caller's logs
 */
interface ApiLoggerInterface
{
   /**
    * Sample implementation:
    *
    * public function debug($string) {
    *    $this->log($string, LOG_DEBUG);
    * }
    */
   public function debug($string);

   /**
    * Sample implementation:
    *
    * public function error($string) {
    *    $this->log($string, LOG_WARNING);
    * }
    */
    public function error($string);
}

?>