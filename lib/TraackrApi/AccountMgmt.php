<?php

namespace Traackr;

class AccountMgmt extends TraackrApiObject {


   public static function customerkeyCreate($p = array()) {

      $account = new AccountMgmt();

      // Check required parameters
      $account->checkRequiredParams($p, array('customer_name'));

      return $account->post(TraackrApi::$apiBaseUrl.'account_mgmt/customerkey/create', $p);

   } // End function customerkeyCreate()


   public static function tagList($p = array()) {

      $account = new AccountMgmt();

      $p = $account->addCustomerKey($p);
      $account->checkRequiredParams($p, array('customer_key'));

      if ( isset($p['tag_prefix_filter']) ) {
         $p['tag_prefix_filter'] = is_array($p['tag_prefix_filter']) ?
            implode(',', $p['tag_prefix_filter']) : $p['tag_prefix_filter'];
      }
      return $account->get(TraackrApi::$apiBaseUrl.'account_mgmt/tag/list', $p);

   } // End function tagList()

   // Delete Customer Key Endpoint
   public static function customerKeyDelete($p = array()) {

      $account = new AccountMgmt();

      // Check required parameters
      $account->checkRequiredParams($p, array('customer_key'));

      return $account->delete(TraackrApi::$apiBaseUrl.'account_mgmt/customerkey/delete', $p);

   } // End function customerKeyDelete()

} // End class AccountMgmt