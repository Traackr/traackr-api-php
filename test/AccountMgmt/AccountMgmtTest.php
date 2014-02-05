<?php

require_once(dirname(__FILE__) . '/../../lib/TraackrApi.php');

class AccountMgmtTest extends PHPUnit_Framework_TestCase {

   private $infUid = '1395be8293373465ab172b8b1b677e31';
   private $infTag = 'traackr-api-test';
   private $infName = 'David Chancogne';

   private $savedCustomerKey;

   public function setUp() {

      $this->savedCustomerKey = Traackr\TraackrApi::getCustomerKey();

      // Ensure outout is PHP by default
      Traackr\TraackrApi::setJsonOutput(false);

   } // End function setUp()

   public function tearDown() {

      Traackr\TraackrApi::setCustomerKey($this->savedCustomerKey);

   } // End functiuon tearDown()


   public function testCustomerkeyCreate() {

      try {
         Traackr\AccountMgmt::customerkeyCreate(array('customer_name' => 'traackr-api-test'));
      }
      catch (Traackr\TraackrApiException $e) {
         $this->assertEquals($e->getMessage(), 'Missing or Invalid argument/parameter (HTTP 400): Customer key exists for given api_key/customer_name');
         $this->assertEquals($e->getCode(), 400);
      }

   } // End function testCreateCustomerKey()

   public function testTagList() {

      $tags = Traackr\AccountMgmt::tagList();

      // $tags_list = array_reduce($tags, function(&$results, $item) { $results[] = $item['tag']; }, array());
      foreach ($tags['account']['tags'] as $tag) {
         if ( $tag['tag'] == $this->infTag ) {
            $this->assertSame(0, $tag['ref_count']);
         }
      }

      Traackr\Influencers::tagAdd(array(
         'influencers' => $this->infUid,
         'tags' => $this->infTag));
      $tags = Traackr\AccountMgmt::tagList();
      foreach ($tags['account']['tags'] as $tag) {
         if ( $tag['tag'] == $this->infTag ) {
            $this->assertSame(1, $tag['ref_count']);
         }
      }
      $tags = Traackr\AccountMgmt::tagList(array('tag_prefix_filter' => 'traackr-api'));
      foreach ($tags['account']['tags'] as $tag) {
         if ( $tag['tag'] == $this->infTag ) {
            $this->assertSame(1, $tag['ref_count']);
         }
      }

      Traackr\Influencers::tagRemove(array(
         'influencers' => $this->infUid,
         'tags' => $this->infTag));

   } // End function testTagList()

   /**
    * @expectedException Traackr\InvalidCustomerKeyException
    */
   public function testSTagListInvalidCustomerKey() {

      Traackr\TraackrApi::setCustomerKey('xxxRandomInvalidCustomerKeyxxxx');
      Traackr\AccountMgmt::tagList();

   } // End function testShowNotFound()


} // End class AccountMgmt