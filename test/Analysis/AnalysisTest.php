<?php

class AnalysisTest extends PHPUnit_Framework_TestCase {

   private $infUid = 'ee62ec2b3657e0a44e30d3b7f086273d';
   private $infName = 'David Chancogne';

   private $infUid2 = 'ae1955b0f92037c895e5bfdd259a1304';

   private $testTag = 'TraackrApiPhpAnalysisTestTag';

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
   public function testToplinks() {

      $infs = array($this->infUid, $this->infUid2);
      $posts = Traackr\Analysis::toplinks(array('influencers' => $infs));
      $this->assertArrayHasKey('links', $posts);
      if ( sizeof($posts['links']) > 0 ) {
         $this->assertTrue(in_array($posts['links'][0]['linkbacks'][0]['influencer_uid'], $infs));
      }

      Traackr\TraackrApi::setJsonOutput(true);
      $jsonOne =  Traackr\Analysis::toplinks(array('influencers' => $infs));
      $jsonTwo = Traackr\Analysis::toplinks(array('influencers' => $this->infUid.','.$this->infUid2));
      $this->assertJsonStringEqualsJsonString($jsonOne, $jsonTwo);

   } // End function toplinksTest()

    /**
    * @group read-only
    */
    public function testKeywords() {
        $json = array('keywords' => array(
            array('label' => 'default',
            'context' => 'POST',
            'query_string' => 'hello world')));
        $result = Traackr\Analysis::keywords($json);
        $this->assertTrue($result['keywords']['default']['is_valid']);

        $json = array('keywords' => array(
            array('label' => 'default',
            'context' => 'POST',
            'query_string' => 'a')));
        $result = Traackr\Analysis::keywords($json);
        $this->assertFalse($result['keywords']['default']['is_valid']);
    }

   /**
    * Tests that the same topLinks information is returned when looking up links for
    * influencers by tags or by influencer uids
    */
   public function testParams() {

      // Add Tags
      $tagAddParams = array(
         'influencers' => array($this->infUid, $this->infUid2),
         'tags' => array($this->testTag)
      );
      Traackr\Influencers::tagAdd($tagAddParams);

      $infs = array($this->infUid, $this->infUid2);
      $tags = array($this->testTag);

      // First test that we have top links to compare
      $posts = Traackr\Analysis::toplinks(array('influencers' => $infs, 'count' => 1)); # 1 post
      $this->assertCount(1, $posts['links']); #just 1 post see above
      $this->assertTrue(in_array($posts['links'][0]['linkbacks'][0]['influencer_uid'], $infs));

      Traackr\TraackrApi::setJsonOutput(true);
      $posts1 = Traackr\Analysis::toplinks(array('influencers' => $infs));
      $posts2 = Traackr\Analysis::toplinks(array('tags' => $tags));
      $this->assertJsonStringEqualsJsonString($posts1, $posts2);

      // Remove Tags
      $tagRemoveParams = array(
         'all' => true,
         'tags' => array($this->testTag)
      );
      Traackr\Influencers::tagRemove($tagRemoveParams);
   }
} // End class AnalysisTest
