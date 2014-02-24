<?php

require_once(dirname(__FILE__) . '/../../lib/TraackrApi.php');

class AnalysisTest extends PHPUnit_Framework_TestCase {

   private $infUid = '1395be8293373465ab172b8b1b677e31';
   private $infName = 'David Chancogne';

   private $infUid2 = 'ae1955b0f92037c895e5bfdd259a1304';

   private $testTag = 'TraackrApiPhpAnalysisTestTag';

   private $savedCustomerKey;

   public function setUp() {

      $this->savedCustomerKey = Traackr\TraackrApi::getCustomerKey();

      // Ensure outout is PHP by default
      Traackr\TraackrApi::setJsonOutput(false);
      $tagAddParams = array( 
         'influencers' => array($this->infUid, $this->infUid2),
         'tags' => array($this->testTag)
      );
      Traackr\Influencers::tagAdd($tagAddParams);

   } // End function setUp()

   public function tearDown() {

      Traackr\TraackrApi::setCustomerKey($this->savedCustomerKey);
      $tagRemoveParams = array( 
         'all' => true,
         'tags' => array($this->testTag)
      );
      Traackr\Influencers::tagRemove($tagRemoveParams);
   } // End functiuon tearDown()

   public function testToplinks() {

      $infs = array($this->infUid, $this->infUid2);
      $posts = Traackr\Analysis::toplinks(array('influencers' => $infs));
      $this->assertCount(5, $posts['links']);
      $this->assertTrue(in_array($posts['links'][0]['linkbacks'][0]['influencer_uid'], $infs));

      Traackr\TraackrApi::setJsonOutput(true);
      $jsonOne =  Traackr\Analysis::toplinks(array('influencers' => $infs));
      $jsonTwo = Traackr\Analysis::toplinks(array('influencers' => $this->infUid.','.$this->infUid2));
      $this->assertJsonStringEqualsJsonString($jsonOne, $jsonTwo);

   } // End function toplinksTest()

   public function testParams() {
      $infs = array($this->infUid, $this->infUid2);
      $tags = array($this->testTag);

      // First test that we have top links to compare
      $posts = Traackr\Analysis::toplinks(array('influencers' => $infs));      
      $this->assertCount(5, $posts['links']);
      $this->assertTrue(in_array($posts['links'][0]['linkbacks'][0]['influencer_uid'], $infs));

      Traackr\TraackrApi::setJsonOutput(true);
      $posts1 = Traackr\Analysis::toplinks(array('influencers' => $infs));
      $posts2 = Traackr\Analysis::toplinks(array('tags' => $tags));
      $this->assertJsonStringEqualsJsonString($posts1, $posts2);
   }
} // End class AnalysisTest