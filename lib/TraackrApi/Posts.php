<?php

namespace Traackr;

class Posts extends TraackrApiObject {

   public static function lookup($p = array(
      'is_tag_prefix' => false,
      'lang' => 'all',
      'include_entities' => false,
      'count' => 25, 'page' => 0) ) {

      $posts = new Posts();
      $p = $posts->addCustomerKey($p);

      // Sanatize default values
      // $p['is_tag_prefix'] = empty($p['is_tag_prefix']) ? 'false' : 'true';
      $p['is_tag_prefix'] = $posts->convertBool($p['is_tag_prefix']);
      // $p['include_entities'] = empty($p['include_entities']) ? 'false' : 'true';
      $p['include_entities'] = $posts->convertBool($p['include_entities']);

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

      return $posts->post(TraackrApi::$apiBaseUrl.'posts/lookup', $p);

   } // End function lookup()

   public static function search($p = array(
      'is_tag_prefix' => false,
      'lang' => 'all',
      'include_keyword_matches' => false,
      'include_entities' => false,
      'enable_keyword_aggregation' => false,
      'enable_influencer_aggregation' => false,
      'enable_domain_aggregation' => false,
      'enable_monthly_aggregation' => false,
      'enable_weekly_aggregation' => false,
      'enable_daily_aggregation' => false,
      'count' => 25, 'page' => 0, 'sort' => 'date') ) {

      $posts = new Posts();
      $p = $posts->addCustomerKey($p);
      $posts->checkRequiredParams($p, array('keywords'));

      // Sanatize default values
      // $p['is_tag_prefix'] = empty($p['is_tag_prefix']) ? 'false' : 'true';
      $p['is_tag_prefix'] = $posts->convertBool($p['is_tag_prefix']);
      // $p['include_keyword_matches'] = empty($p['include_keyword_matches']) ? 'false' : 'true';
      $p['include_keyword_matches'] = $posts->convertBool($p['include_keyword_matches']);
      // $p['include_entities'] = empty($p['include_entities']) ? 'false' : 'true';
      $p['include_entities'] = $posts->convertBool($p['include_entities']);
      // $p['enable_keyword_aggregation'] = empty($p['enable_keyword_aggregation']) ? 'false' : 'true';
      $p['enable_keyword_aggregation'] = $posts->convertBool($p['enable_keyword_aggregation']);
      // $p['enable_influencer_aggregation'] = empty($p['enable_influencer_aggregation']) ? 'false' : 'true';
      $p['enable_influencer_aggregation'] = $posts->convertBool($p['enable_influencer_aggregation']);
      // $p['enable_domain_aggregation'] = empty($p['enable_domain_aggregation']) ? 'false' : 'true';
      $p['enable_domain_aggregation'] = $posts->convertBool($p['enable_domain_aggregation']);
      // $p['enable_monthly_aggregation'] = empty($p['enable_monthly_aggregation']) ? 'false' : 'true';
      $p['enable_monthly_aggregation'] = $posts->convertBool($p['enable_monthly_aggregation']);
      // $p['enable_weekly_aggregation'] = empty($p['enable_weekly_aggregation']) ? 'false' : 'true';
      $p['enable_weekly_aggregation'] = $posts->convertBool($p['enable_weekly_aggregation']);
      // $p['enable_daily_aggregation'] = empty($p['enable_daily_aggregation']) ? 'false' : 'true';
      $p['enable_daily_aggregation'] = $posts->convertBool($p['enable_daily_aggregation']);

      // Validate business requirements
      if ( $p['enable_keyword_aggregation'] === 'true' && $p['include_keyword_matches'] === 'false' ) {
         throw new MissingParameterException("'include_keyword_matches' needs to be set to true for 'keyword_aggregation' to work");
      }

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

       return $posts->post(TraackrApi::$apiBaseUrl.'posts/search', $p);

   } // End function lookup()


} // End clas Posts
