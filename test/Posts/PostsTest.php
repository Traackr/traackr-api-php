<?php

class PostsTest extends PHPUnit_Framework_TestCase {

   private $infUid = '1395be8293373465ab172b8b1b677e31';

   private $savedCustomerKey;

   public function setUp() {

      $this->savedCustomerKey = Traackr\TraackrApi::getCustomerKey();

      // Ensure outout is PHP by default
      Traackr\TraackrApi::setJsonOutput(false);

   } // End function setUp()

   public function tearDown() {

      Traackr\TraackrApi::setCustomerKey($this->savedCustomerKey);

   } // End functiuon tearDown()


   /**
    * @group read-only
    */
   public function testLookup() {

      $posts = Traackr\Posts::lookup(array('influencers' => $this->infUid));
      $this->assertArrayHasKey('page_info', $posts, 'No paging info');
      $this->assertGreaterThan(0, $posts['posts'], 'No results found');
      $this->assertEquals($this->infUid, $posts['posts'][0]['influencer_uid'], 'Invalid influencer author found');

      $posts = Traackr\Posts::lookup(array('influencers' => '000000'));
      $this->assertCount(0, $posts['posts'], 'Results found');

   } // End function testLookup()

   /**
    * @group read-only
    */
   public function testSearch() {

      $posts = Traackr\Posts::search(array('keywords' => array('traackr', '"content marketing"')));
      $this->assertArrayHasKey('posts', $posts, 'No posts found');

   } // End function testSearch()

} // End class PostsTest
