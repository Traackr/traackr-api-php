<?php

namespace Traackr;

class Posts extends TraackrApiObject {

   public static function lookup($p = array(
      'lang' => 'all',
      'include_entities' => false,
      'count' => 25, 'page' => 0) ) {

      $posts = new Posts();
      $p = $posts->addCustomerKey($p);

      // Sanatize default values
      $p['include_entities'] = empty($p['include_entities']) ? 'false' : 'true';

      // support for multi params
      if ( isset($p['influencers']) ) {
         $p['influencers'] = is_array($p['influencers']) ?
            implode(',', $p['influencers']) : $p['influencers'];
      }
      if ( isset($p['tags']) ) {
         $p['tags'] = is_array($p['tags']) ?
            implode(',', $p['tags']) : $p['tags'];
      }
      if ( isset($p['root_urls_inclusive']) ) {
         $p['root_urls_inclusive'] = is_array($p['root_urls_inclusive']) ?
               implode(',', $p['root_urls_inclusive']) : $p['root_urls_inclusive'];
      }
      if ( isset($p['root_urls_exclusive']) ) {
         $p['root_urls_exclusive'] = is_array($p['root_urls_exclusive']) ?
            implode(',', $p['root_urls_exclusive']) : $p['root_urls_exclusive'];
      }

      return $posts->get(TraackrApi::$apiBaseUrl.'posts/lookup', $p);

   } // End function lookup()

   public static function search($p = array(
      'lang' => 'all',
      'include_keyword_matches' => false,
      'include_entities' => false,
      'count' => 25, 'page' => 0, 'sort' => 'date') ) {

      $posts = new Posts();
      $p = $posts->addCustomerKey($p);
      $posts->checkRequiredParams($p, array('keywords'));


      // Sanatize default values
      $p['include_keyword_matches'] = empty($p['include_keyword_matches']) ? 'false' : 'true';
      $p['include_entities'] = empty($p['include_entities']) ? 'false' : 'true';

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

       return $posts->post(TraackrApi::$apiBaseUrl.'posts/search', $p);

   } // End function lookup()


} // End clas Posts
