<?php

namespace Traackr;

class Posts extends TraackrApiObject {

   public static function lookup($p = array(
      'is_tag_prefix' => false,
      'lang' => 'all',
      'include_entities' => false,
      'count' => 25,
      'page' => 0) ) {

      $posts = new Posts();
      $p = $posts->addCustomerKey($p);

      // Sanitize default values
      $p['is_tag_prefix'] = $posts->convertBool($p, 'is_tag_prefix');
      $p['include_entities'] = $posts->convertBool($p, 'include_entities');
      $p['include_brand_content'] = $posts->convertBool($p, 'include_brand_content');
      $p['include_shared_content'] = $posts->convertBool($p, 'include_shared_content');
      $p['force_vit_legacy'] = $posts->convertBool($p, 'force_vit_legacy');

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
      if ( isset($p['root_urls_inclusive']) ) {
         $p['root_urls_inclusive'] = is_array($p['root_urls_inclusive']) ?
               implode(',', $p['root_urls_inclusive']) : $p['root_urls_inclusive'];
      }
      if ( isset($p['root_urls_exclusive']) ) {
         $p['root_urls_exclusive'] = is_array($p['root_urls_exclusive']) ?
            implode(',', $p['root_urls_exclusive']) : $p['root_urls_exclusive'];
      }
      if ( isset($p['publication_types_inclusive']) ) {
         $p['publication_types_inclusive'] = is_array($p['publication_types_inclusive']) ?
               implode(',', $p['publication_types_inclusive']) : $p['publication_types_inclusive'];
      }
      if ( isset($p['publication_types_exclusive']) ) {
         $p['publication_types_exclusive'] = is_array($p['publication_types_exclusive']) ?
               implode(',', $p['publication_types_exclusive']) : $p['publication_types_exclusive'];
      }
      if (isset($p['posts_inclusive'])) {
         $p['posts_inclusive'] = is_array($p['posts_inclusive']) ?
               implode(',', $p['posts_inclusive']) : $p['posts_inclusive'];
      }
      if (isset($p['posts_exclusive'])) {
         $p['posts_exclusive'] = is_array($p['posts_exclusive']) ?
               implode(',', $p['posts_exclusive']) : $p['posts_exclusive'];
      }
      return $posts->post(TraackrApi::$apiBaseUrl.'posts/lookup', $p);

   }

   public static function search($p = array(
      'is_tag_prefix' => false,
      'lang' => 'all',
      'include_keyword_matches' => false,
      'include_entities' => false,
      'count' => 25,
      'page' => 0,
      'sort' => 'date') ) {

      $posts = new Posts();
      $p = $posts->addCustomerKey($p);
      $posts->checkRequiredParams($p, array('keywords'));

      // Sanitize default values
      $p['is_tag_prefix'] = $posts->convertBool($p, 'is_tag_prefix');
      $p['include_keyword_matches'] = $posts->convertBool($p, 'include_keyword_matches');
      $p['include_entities'] = $posts->convertBool($p, 'include_entities');
      $p['include_brand_content'] = $posts->convertBool($p, 'include_brand_content');
      $p['include_shared_content'] = $posts->convertBool($p, 'include_shared_content');
      $p['force_vit_legacy'] = $posts->convertBool($p, 'force_vit_legacy');

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
      if ( isset($p['publication_types_inclusive']) ) {
         $p['publication_types_inclusive'] = is_array($p['publication_types_inclusive']) ?
               implode(',', $p['publication_types_inclusive']) : $p['publication_types_inclusive'];
      }
      if ( isset($p['publication_types_exclusive']) ) {
         $p['publication_types_exclusive'] = is_array($p['publication_types_exclusive']) ?
               implode(',', $p['publication_types_exclusive']) : $p['publication_types_exclusive'];
      }
      if (isset($p['posts_inclusive'])) {
         $p['posts_inclusive'] = is_array($p['posts_inclusive']) ?
               implode(',', $p['posts_inclusive']) : $p['posts_inclusive'];
      }
      if (isset($p['posts_exclusive'])) {
         $p['posts_exclusive'] = is_array($p['posts_exclusive']) ?
               implode(',', $p['posts_exclusive']) : $p['posts_exclusive'];
      }
      return $posts->post(TraackrApi::$apiBaseUrl.'posts/search', $p);

   }
}
