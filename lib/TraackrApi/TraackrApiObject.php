<?php

abstract class TraackrApiObject {

   public static $connectionTimeout = 10;
   public static $timeout = 10;

   private $curl;

   public function __construct() {

      // init cURL
      $this->curl = curl_init();
      // return value as a string
      curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
      // Set timeouts
      curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, self::$connectionTimeout);
      curl_setopt($this->curl, CURLOPT_TIMEOUT, self::$timeout);

      // Adding some headers to force no caching.
      $curl_headers = array(
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            //some proxies throw a "417" error for CURL calls; CURL is supposed
            //to retry the call, but doesn't, so just set "Expect" to nothing to
            //avoid this (this ensures that CURL doesn't set it to an unrecognized
            //value under the covers)
            "Expect:"
        );
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $curl_headers);

   } // End constructor


   protected function addCustomerKey(&$params) {

      $key = TraackrApi::getCustomerKey();
      if ( !empty($key) ) {
         $params[PARAM_CUSTOMER_KEY] = $key;
      }
      return $params;

   } // End function addCustomerKey()

   private function call($decode) {

      // Sets request headers. This are important to be UTF-8 compliant
      //
      // To ensure that POST parameters (passed in the body) are UTF-8 encoded:
      // "Content-Type: application/x-www-form-urlencoded ; charset=UTF-8"
      //
      // To Ensure the server sends back UTF-8 text
      // "Accept-Charset: utf-8",
      // "Accept: text/plain",
      curl_setopt($this->curl, CURLOPT_HTTPHEADER, array (
         // "Content-Type: application/x-www-form-urlencoded ; charset=utf-8",
         "Accept-Charset: utf-8",
         "Accept: text/plain"
      ));
      $curl_exec = curl_exec($this->curl);
      if($curl_exec === false) {
         // $this->log('cUrl error: '.curl_error($this->ch), LOG_WARNING);
         throw new TraackrApiException();
      }
      if ( is_null($curl_exec) ) {
         // $this->log('cUrl error: Return was null', LOG_WARNING);
         throw new TraackrApiException();
      }
      $httpcode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
      if( $httpcode != "200" ) {
         // $this->log('cUrl HTTP error: '.$httpcode, LOG_WARNING);
         if ( $httpcode == "404" ) {
            throw new NotFoundException();
         }
         else {
            throw new TraackrApiException('API Error returned: '.$httpcode);
         }
         return false;
      }

      // API MUST return UTF8
      if ( $decode ) {
         $rez = json_decode($curl_exec, true);
      }
      else {
         $rez = $curl_exec;
      }

      // json_decode() might return null on error
      return is_null($rez)? false : $rez;

   } // End function call()

   public function get($url, $params = array()) {

      // Ensure we do a GET call - W/o a set to 0 a CURL might be set for a POST
      // call from a previous request
      curl_setopt($this->curl, CURLOPT_POST, 0);
      // Add API key parameter if not present
      if ( !isset($params[PARAM_API_KEY]) ) {
         $params[PARAM_API_KEY] = TraackrApi::getApiKey();
      }
      // Add params if needed
      if ( !empty($params) ) {
         $url .= "?".http_build_query($params);
      }
      // Sets URL
      curl_setopt($this->curl, CURLOPT_URL, $url);
      // Make call
      return $this->call(!TraackrAPI::isJsonOutput());

   } // End function doGet()

   public function post($url, $params = array()) {

      // POST call
      curl_setopt($this->curl, CURLOPT_POST, 1);
      // Sets URL
      curl_setopt($this->curl, CURLOPT_URL, $url);
      // Build Parameters
      // Add API key parameter if not present
      if ( !isset($params[PARAM_API_KEY]) ) {
         $params[PARAM_API_KEY] = TraackrApi::getApiKey();
      }
      $http_param_query = http_build_query($params);
      curl_setopt($this->curl, CURLOPT_POSTFIELDS, $http_param_query);
      // Make call
      // $this->log(sprintf('Calling (POST): %s [%s]', $url, $http_param_query), LOG_DEBUG);
      sprintf('Calling (POST): %s [%s]', $url, $http_param_query);
      return $this->call(!TraackrAPI::isJsonOutput());

   } // End functuion doPost()


} // End class TraackrAPI