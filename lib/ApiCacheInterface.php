<?php

namespace Traackr;

/**
 * Allows integration with the caller's caching engine
 */
interface ApiCacheInterface
{
   /**
    * Sample implementation:
    *
    * public function isCacheable($key, $prefix = '') {
    *    if (strpos($key, '/posts/search') !== false
    *          || strpos($key, '/posts/lookup') !== false) {
    *       return true;
    *    }
    * 
    *    return false;
    * }
    */
   public function isCacheable($key, $prefix = '');

   /**
    * Sample implementation:
    *
    * public function read($key, $prefix = '') {
    *    return Cache::read(CACHE_KEY_API_DATA . ':' . (empty($prefix) ? '' : $prefix . ':') . $key, CACHE_API_DATA);
    * }
    */
   public function read($key, $prefix = '');

   /**
    * Sample implementation:
    *
    * public function write($key, $value, $prefix = '') {
    *    Cache::write(CACHE_KEY_API_DATA . ':' . (empty($prefix) ? '' : $prefix . ':') . $key, $value, CACHE_API_DATA);
    * }
    */
   public function write($key, $value, $prefix = '');

   /**
    * Sample implementation:
    *
    * public function expire($prefix = '') {
    *    Cache::delete(CACHE_KEY_API_DATA . ':' . (empty($prefix) ? '' : $prefix . ':') . '*', CACHE_API_DATA);
    * }
    */
   public function expire($prefix = '');
}

?>