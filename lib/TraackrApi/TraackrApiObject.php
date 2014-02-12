<?php

namespace Traackr;

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

      $curl_headers = array(
         // Adding some headers to force no caching.
         "Cache-Control: no-cache",
         "Pragma: no-cache",
         //some proxies throw a "417" error for CURL calls; CURL is supposed
         //to retry the call, but doesn't, so just set "Expect" to nothing to
         //avoid this (this ensures that CURL doesn't set it to an unrecognized
         //value under the covers)
         "Expect:",

         // Sets request headers. This are important to be UTF-8 compliant
         // To ensure that POST parameters (passed in the body) are UTF-8 encoded:
         "Content-Type: application/x-www-form-urlencoded;charset=utf-8",
         // To Ensure the server sends back UTF-8 text
         "Accept-Charset: utf-8",
         "Accept: text/plain"
        );
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $curl_headers);
        curl_setopt($this->curl, CURLOPT_ENCODING , "gzip;q=1.0, deflate;q=0.5, identity;q=0.1");

   } // End constructor

   protected function checkRequiredParams($params, $fields) {

      foreach ($fields as $f) {

         if ( empty($params[$f]) && !(isset($params[$f]) && is_bool($params[$f])) ) {
            throw new MissingParameterException('Missing parameter: '.$f);
         }

      } // End fields loop

   } // End function checkRequiredParams()

   protected function addCustomerKey(&$params) {

      $key = TraackrApi::getCustomerKey();
      if ( !empty($key) && empty($params[PARAM_CUSTOMER_KEY]) ) {
         $params[PARAM_CUSTOMER_KEY] = $key;
      }
      return $params;

   } // End function addCustomerKey()

   private function call($decode) {

      // Make the call!
      $curl_exec = curl_exec($this->curl);

      if( $curl_exec === false ) {
         // $this->log('cUrl error: '.curl_error($this->ch), LOG_WARNING);
         $info = curl_getinfo($this->curl);
         throw new TraackrApiException('API call failed ('.$info['url'].'): '.curl_error($this->curl));
      }
      if ( is_null($curl_exec) ) {
         // $this->log('cUrl error: Return was null', LOG_WARNING);
         throw new TraackrApiException('API call failed. Response was null.');
      }
      $httpcode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
      if( $httpcode != "200" ) {
         $info = curl_getinfo($this->curl);
         // $this->log('cUrl HTTP error: '.$httpcode, LOG_WARNING);
         if ( $httpcode == "400") {
            // Let's try to see if it's a bad customer key
            if ( $curl_exec === "Customer key not found" ) {
               throw new InvalidCustomerKeyException(
                  'Invalid Customer Key (HTTP 400): '.$curl_exec,
                  $httpcode);
            }
            else {
               throw new MissingParameterException(
                  'Missing or Invalid argument/parameter (HTTP 400): '.$curl_exec,
                  $httpcode);
            }
         }
         elseif ( $httpcode == "403") {
            throw new InvalidApiKeyException(
               'Invalid API key (HTTP 403): '.$curl_exec,
               $httpcode);
         }
         elseif ( $httpcode == "404" ) {
            throw new NotFoundException(
               'API resource not found (HTTP 404): '.$info['url'],
               $httpcode);
         }
         else {
            throw new TraackrApiException(
               'API HTTP Error (HTTP '.$httpcode.'): '.$curl_exec,
               $httpcode);
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