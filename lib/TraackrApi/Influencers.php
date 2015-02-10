<?php

namespace Traackr;

class Influencers extends TraackrApiObject {

   /*
    * Get an influencer data.
    *
    */
   public static function show($uid, $p = array('with_channels' => false)) {

      if ( empty($uid) ) {
         throw new MissingParameterException("Missing Influencer UID parameter.");
      }

      // API Object
      $inf = new Influencers();

      //Sanatize default values
      $p['with_channels'] = $inf->convertBool($p, 'with_channels');

      // Add customer key + check required params
      $p = $inf->addCustomerKey($p);
      $inf->checkRequiredParams($p, array('with_channels'));
      // support for multi params
      $uid = is_array($uid) ? implode(',', $uid) : $uid;

      return $inf->get(TraackrApi::$apiBaseUrl.'influencers/show/'.$uid, $p);

   } // End function show()


   /*
    * Returns an infuencer's connections
    */
   public static function connections($uid, $direction = '') {

      if ( empty($uid) ) {
         throw new MissingParameterException("Missing Influencer UID parameter");
      }

      $uid = is_array($uid) ? implode(',', $uid) : $uid;
      $direction = empty($direction) ? '' : $direction.'/';
      $inf = new Influencers();
      return $inf->get(TraackrApi::$apiBaseUrl.'influencers/connections/'.$direction.$uid,
         array());

   } // End function connections()

   /*
    * Lopokup Influencer by a Twitter handle
    */
   public static function lookupTwitter($username) {

      if ( empty($username) ) {
         throw new MissingParameterException("Missing username parameter");
      }

      $inf = new Influencers();
      return $inf->get(TraackrApi::$apiBaseUrl.'influencers/lookup/twitter/'.$username,
         array());

   } // End function lookupTwitter


   /*
    * Add Twitter account
    */
   public static function addTwitter($p = array()) {

      $inf = new Influencers();

      $p = $inf->addCustomerKey($p);
      $inf->checkRequiredParams($p, array('username', 'customer_key'));

      // support multi params
      if ( !empty($p['tags']) ) {
         $p['tags'] = is_array($p['tags']) ? implode(',', $p['tags']) : $p['tags'];
      }

      return $inf->post(TraackrApi::$apiBaseUrl.'influencers/add/twitter', $p);

   } // End function addTwitter()


   /*
    * Add influencer by name and primary URL
    */
   public static function add($p = array()) {

      $inf = new Influencers();

      $p = $inf->addCustomerKey($p);
      $inf->checkRequiredParams($p, array('name', 'url', 'customer_key'));

      // support multi params
      if ( !empty($p['tags']) ) {
         $p['tags'] = is_array($p['tags']) ? implode(',', $p['tags']) : $p['tags'];
      }

      return $inf->post(TraackrApi::$apiBaseUrl.'influencers/add', $p);

   } // End function add()


   public static function tagAdd($p = array('strict' => false)) {

      $inf = new Influencers();

      // Sanatize default values
      $p['strict'] = $inf->convertBool($p, 'strict');

      $p = $inf->addCustomerKey($p);
      $inf->checkRequiredParams($p, array('influencers', 'tags', 'customer_key', 'strict'));

      // support for multi params
      $p['influencers'] = is_array($p['influencers']) ?
         implode(',', $p['influencers']) : $p['influencers'];
      $p['tags'] = is_array($p['tags']) ?
         implode(',', $p['tags']) : $p['tags'];

      return $inf->post(TraackrApi::$apiBaseUrl.'influencers/tag/add', $p);

   } // End function tagAdd()


   public static function tagRemove($p = array('all' => false)) {

      $inf = new Influencers();

      // Sanatize default values
      $p['all'] = $inf->convertBool($p, 'all');

      $p = $inf->addCustomerKey($p);
      // 'influencers' is not required if 'all' is set to true
      // by then 'all' has already be converted to a string
      if ( $p['all'] === 'false' ) {
         $inf->checkRequiredParams($p, array('influencers', 'tags', 'customer_key', 'all'));
      } else {
         $inf->checkRequiredParams($p, array('tags', 'customer_key', 'all'));
      }

      // support for multi params
      if ( !empty($p['influencers']) ) {
         $p['influencers'] = is_array($p['influencers']) ?
            implode(',', $p['influencers']) : $p['influencers'];
      }
      $p['tags'] = is_array($p['tags']) ?
         implode(',', $p['tags']) : $p['tags'];

      return $inf->post(TraackrApi::$apiBaseUrl.'influencers/tag/remove', $p);

   } // End function tagRemove()


   public static function tagList($p = array('is_prefix' => false)) {

      $inf = new Influencers();

      // Sanatize default values
      $p['is_prefix'] = $inf->convertBool($p, 'is_prefix');

      $p = $inf->addCustomerKey($p);
      $inf->checkRequiredParams($p, array('tag', 'is_prefix', 'customer_key'));

      return $inf->get(TraackrApi::$apiBaseUrl.'influencers/tag/list', $p);

   } // End function tagList()


   public static function lookup($p = array(
      'is_tag_prefix' => false,
      'gender' => 'all',
      'enable_tags_aggregation' => false,
      'enable_country_aggregation' => false,
      'enable_audience_aggregation' => false,
      'count' => 25, 'page' => 0,
      'sort' => 'name', 'sort_order' => 'asc')) {

      $inf = new Influencers();

      // Sanatize default values
      $p['is_tag_prefix'] = $inf->convertBool($p, 'is_tag_prefix');
      $p['enable_tags_aggregation'] = $inf->convertBool($p, 'enable_tags_aggregation');
      $p['enable_country_aggregation'] = $inf->convertBool($p, 'enable_country_aggregation');
      $p['enable_audience_aggregation'] = $inf->convertBool($p, 'enable_audience_aggregation');

      $p = $inf->addCustomerKey($p);

      // support for multi params
      if ( isset($p['influencers']) ) {
         $p['influencers'] = is_array($p['influencers']) ?
            implode(',', $p['influencers']) : $p['influencers'];
      }
      if ( isset($p['tags']) ) {
         $p['tags'] = is_array($p['tags']) ?
            implode(',', $p['tags']) : $p['tags'];
      }
      if ( isset($p['tags_exclusive']) ) {
         $p['tags_exclusive'] = is_array($p['tags_exclusive']) ?
            implode(',', $p['tags_exclusive']) : $p['tags_exclusive'];
      }
      if ( isset($p['emails']) ) {
         $p['emails'] = is_array($p['emails']) ?
            implode(',', $p['emails']) : $p['emails'];
      }
      return $inf->post(TraackrApi::$apiBaseUrl.'influencers/lookup', $p);

   } // End function lookup()

   public static function search($p = array(
      'is_tag_prefix' => false,
      'gender' => 'all',
      'lang' => 'all',
      'enable_audience_aggregation' => false,
      'enable_country_aggregation' => false,      
      'count' => 25)) {

      $inf = new Influencers();

      // Sanatize default values
      $p['is_tag_prefix'] = $inf->convertBool($p, 'is_tag_prefix');
      $p['enable_audience_aggregation'] = $inf->convertBool($p, 'enable_audience_aggregation');
      $p['enable_country_aggregation'] = $inf->convertBool($p, 'enable_country_aggregation');

      $p = $inf->addCustomerKey($p);
      $inf->checkRequiredParams($p, array('keywords'));

      // support for multi params
      $p['keywords'] = is_array($p['keywords']) ?
         implode(',', $p['keywords']) : $p['keywords'];
      if ( isset($p['influencers']) ) {
         $p['influencers'] = is_array($p['influencers']) ?
            implode(',', $p['influencers']) : $p['influencers'];
      }
      if ( isset($p['tags']) ) {
         $p['tags'] = is_array($p['tags']) ?
            implode(',', $p['tags']) : $p['tags'];
      }
      if ( isset($p['tags_exclusive']) ) {
         $p['tags_exclusive'] = is_array($p['tags_exclusive']) ?
            implode(',', $p['tags_exclusive']) : $p['tags_exclusive'];
      }
      if ( isset($p['exclusion_keywords']) ) {
         $p['exclusion_keywords'] = is_array($p['exclusion_keywords']) ?
            implode(',', $p['exclusion_keywords']) : $p['exclusion_keywords'];
      }
      if ( isset($p['root_urls_inclusive']) ) {
         $p['root_urls_inclusive'] = is_array($p['root_urls_inclusive']) ?
               implode(',', $p['root_urls_inclusive']) : $p['root_urls_inclusive'];
      }
      if ( isset($p['root_urls_exclusive']) ) {
         $p['root_urls_exclusive'] = is_array($p['root_urls_exclusive']) ?
            implode(',', $p['root_urls_exclusive']) : $p['root_urls_exclusive'];
      }
      if ( isset($p['emails']) ) {
         $p['emails'] = is_array($p['emails']) ?
            implode(',', $p['emails']) : $p['emails'];
      }
      return $inf->post(TraackrApi::$apiBaseUrl.'influencers/search', $p);

   } // End function search()

} // End class Influencer