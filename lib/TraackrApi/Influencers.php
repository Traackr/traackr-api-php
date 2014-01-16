<?php

namespace Traackr;

class Influencers extends TraackrApiObject {


   /*
    * Get an influencer data. See second parameter to get channel info in
    * the response
    */
   public static function show($uid, $withChannels = false) {

      if ( empty($uid) ) {
         throw new MissingParameterException("Missing Influencer UID parameter.");
      }

      $inf = new Influencers();
      $uid = is_array($uid) ? implode(',', $uid) : $uid;
      $params = array('with_channels' => $withChannels?'true':'false');

      $inf = new Influencers();
      $params = $inf->addCustomerKey($params);
      return $inf->get(TraackrApi::$apiBaseUrl.'influencers/show/'.$uid, $params);

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

      $inf = new Influencers();
      return $inf->get(TraackrApi::$apiBaseUrl.'influencers/lookup/twitter/'.$username,
         array());

   } // End function lookupTwitter


   public static function tagAdd($uid, $tags, $strict = false) {

      if ( empty($uid) ) {
         throw new MissingParameterException("Missing Influencer UID parameter.");
      }

      $uid = is_array($uid) ? implode(',', $uid) : $uid;
      $tags = is_array($tags) ? implode(',', $tags) : $tags;
      $params = array('influencers' => $uid, 'tags' => $tags);

      $inf = new Influencers();
      $params = $inf->addCustomerKey($params);
      return $inf->post(TraackrApi::$apiBaseUrl.'influencers/tag/add/', $params);

   } // End function tagAdd()


   public static function tagRemove($uid, $tags, $all = false) {

      if ( empty($tags) ) {
         throw new MissingParameterException("Missing tags parameter.");
      }

      if ( is_array($uid) && sizeof($uid) ==  0 ) {
        $uid = '';
      }
      $uid = is_array($uid) ? implode(',', $uid) : $uid;
      $tags = is_array($tags) ? implode(',', $tags) : $tags;
      $params = array('influencers' => $uid, 'tags' => $tags);

      $inf = new Influencers();
      $params = $inf->addCustomerKey($params);
      return $inf->post(TraackrApi::$apiBaseUrl.'influencers/tag/remove/', $params);

   } // End function tagRemove()


   public static function tagList($tag) {

      if ( empty($tag) ) {
         throw new MissingParameterException("Missing tag parameter.");
      }

      // $tags = is_array($tags) ? implode(',', $tags) : $tags;
      $params = array('tag' => $tag);

      $inf = new Influencers();
      $params = $inf->addCustomerKey($params);
      return $inf->get(TraackrApi::$apiBaseUrl.'influencers/tag/list/', $params);

   } // End function tagList()


} // End class Influencer