<?php

namespace Traackr;

class Influencers extends TraackrApiObject {

   /*
    * Get an influencer data. See second parameter to get channel info in
    * the response
    */
   public static function show($uid, $p = array('with_channel' => false)) {

      if ( empty($uid) ) {
         throw new MissingParameterException("Missing Influencer UID parameter.");
      }
      //Sanatize default values
      $p['with_channels'] = empty($p['with_channels']) ? 'false' : 'true';

      $inf = new Influencers();
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

      return $inf->post(TraackrApi::$apiBaseUrl.'add/twitter', $p);

   } // End function attTwitter()


   public static function tagAdd($p = array('strict' => false)) {

      $inf = new Influencers();

      // Sanatize default values
      $p['strict'] = empty($p['strict']) ? 'false' : 'true';

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
      $p['all'] = empty($p['all']) ? 'false' : 'true';

      $p = $inf->addCustomerKey($p);
      $inf->checkRequiredParams($p, array('influencers', 'tags', 'customer_key', 'all'));

      // support for multi params
      $p['influencers'] = is_array($p['influencers']) ?
         implode(',', $p['influencers']) : $p['influencers'];
      $p['tags'] = is_array($p['tags']) ?
         implode(',', $p['tags']) : $p['tags'];

      return $inf->post(TraackrApi::$apiBaseUrl.'influencers/tag/remove', $p);

   } // End function tagRemove()


   public static function tagList($p = array()) {

      $inf = new Influencers();

      $p = $inf->addCustomerKey($p);
      $inf->checkRequiredParams($p, array('tag', 'customer_key'));

      // $p['tag'] = is_array($p['tag']) ? implode(',', $p['tag']) : $p['tag'];

      return $inf->get(TraackrApi::$apiBaseUrl.'influencers/tag/list', $p);

   } // End function tagList()


   public function lookup($p = array(
      'gender' => 'all',
      'count' => 25, 'page' => 0,
      'sort' => 'name', 'sort_order' => 'asc')) {

      $inf = new Influencers();
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

      return $inf->get(TraackrApi::$apiBaseUrl.'influencers/lookup', $p);

   } // End function lookup()

   public function search($p = array('lang' => 'all', 'count' => 25)) {

      $inf = new Influencers();
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
      if ( isset($p['exlcusion_keywords']) ) {
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

       return $inf->get(TraackrApi::$apiBaseUrl.'influencers/search', $p);

   } // End function lookup()

} // End class Influencer