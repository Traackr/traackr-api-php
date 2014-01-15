<?php

namespace Traackr;

// Objects
require(dirname(__FILE__) . '/TraackrApi/TraackrApiObject.php');
require(dirname(__FILE__) . '/TraackrApi/Influencers.php');

// Exceptions
require(dirname(__FILE__) . '/TraackrApi/Exceptions/TraackrApiException.php');
require(dirname(__FILE__) . '/TraackrApi/Exceptions/NotFoundException.php');
require(dirname(__FILE__) . '/TraackrApi/Exceptions/InvalidCustomerKeyException.php');
require(dirname(__FILE__) . '/TraackrApi/Exceptions/MissingParameterException.php');

define('PARAM_API_KEY', 'api_key');
define('PARAM_CUSTOMER_KEY', 'customer_key');

new TraackrApi();

final class TraackrApi {


   private static $apiKey;

   public static $apiBaseUrl = 'http://api.traackr.com/1.0/';

   private static $customerKey = '';

   private static $jsonOutput = false;

   public function __construct() {

      // Get ENV values for API Key and Customer keys is defined
     if ( isset($_ENV['TRAACKR_API_KEY']) ) {
        TraackrApi::setApiKey($_ENV['TRAACKR_API_KEY']);
     }
     if ( isset($_ENV['TRAACKR_CUSTOMER_KEY']) ) {
         TraackrApi::setCustomerKey($_ENV['TRAACKR_CUSTOMER_KEY']);
     }
     if ( isset($_ENV['TRAACKR_API_URL']) ) {
      self::$apiBaseUrl = $_ENV['TRAACKR_API_URL'];
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


   public static function isJsonOutput() {

      return self::$jsonOutput;

   } // End function isJsonOutput()

   public static function setJsonOutput($json = true) {

      self::$jsonOutput = $json;

   } // End function isJsonOutput()


} // End class TraackrApi