<?php

namespace Traackr;

// Interfaces
require(dirname(__FILE__) . '/ApiLoggerInterface.php');

// Objects
require(dirname(__FILE__) . '/TraackrApi/TraackrApiObject.php');
require(dirname(__FILE__) . '/TraackrApi/Influencers.php');
require(dirname(__FILE__) . '/TraackrApi/Posts.php');
require(dirname(__FILE__) . '/TraackrApi/Analysis.php');
require(dirname(__FILE__) . '/TraackrApi/AccountMgmt.php');

// Exceptions
require(dirname(__FILE__) . '/TraackrApi/Exceptions/TraackrApiException.php');
require(dirname(__FILE__) . '/TraackrApi/Exceptions/NotFoundException.php');
require(dirname(__FILE__) . '/TraackrApi/Exceptions/InvalidApiKeyException.php');
require(dirname(__FILE__) . '/TraackrApi/Exceptions/InvalidCustomerKeyException.php');
require(dirname(__FILE__) . '/TraackrApi/Exceptions/MissingParameterException.php');

define('PARAM_API_KEY', 'api_key');
define('PARAM_CUSTOMER_KEY', 'customer_key');

new TraackrApi();

class DefaultApiLogger implements ApiLoggerInterface
{
   public function debug($string) {
      //do nothing
   }
   public function error($string) {
      //do nothing
   }
}

final class TraackrApi {


   private static $apiKey;

   public static $apiBaseUrl = 'https://api.traackr.com/1.0/';

   private static $customerKey = '';

   private static $extraHeaders = array();

   private static $jsonOutput = false;

   private static $logger = null;

   public function __construct() {

      // Get ENV values for API Key and Customer keys is defined
      if ( getenv('TRAACKR_API_KEY') !== FALSE ) {
         TraackrApi::setApiKey(getenv('TRAACKR_API_KEY'));
      }
      if ( getenv('TRAACKR_CUSTOMER_KEY') !== FALSE ) {
         TraackrApi::setCustomerKey(getenv('TRAACKR_CUSTOMER_KEY'));
      }
      if ( getenv('TRAACKR_API_URL') !== FALSE ) {
         self::$apiBaseUrl = getenv('TRAACKR_API_URL');
      }

      if ( strrpos(self::$apiBaseUrl, '/') !== strlen(self::$apiBaseUrl)-1 ) {
         self::$apiBaseUrl .= '/';
      }

   } // End constructor

   public static function getApiKey() {

      return self::$apiKey;

   } // End function getApiKey()

   public static function setApiKey($key) {

      self::$apiKey = $key;

   } // End function getApiKey()

   public static function getCustomerKey() {

      return self::$customerKey;

   } // End function getCustomerKey()


   public static function setCustomerKey($key) {

      self::$customerKey = $key;

   } // End function setCustomerKey()


   public static function setExtraHeaders($headers) {

      if ( is_string($headers) ) {
         self::$extraHeaders = array($headers);
         return true;
      }
      else if ( is_array($headers) ) {
         self::$extraHeaders = $headers;
         return true;
      }
      else {
         return false;
      }

   } // End function setExtraHeaders()


   public static function getExtraHeaders() {

      return self::$extraHeaders;

   } // End function getExtraHeaders()


   public static function isJsonOutput() {

      return self::$jsonOutput;

   } // End function isJsonOutput()

   public static function setJsonOutput($json = true) {

      self::$jsonOutput = $json;

   } // End function isJsonOutput()

   public static function getLogger() {

      if (empty(self::$logger)) {
         self::$logger = new DefaultApiLogger();
      }

      return self::$logger;

   } // End function getLogger()

   public static function setLogger(ApiLoggerInterface $obj) {

      self::$logger = $obj;

   } // End function setLogger()

} // End class TraackrApi
