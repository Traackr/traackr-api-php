<?php

class AnalysisTest extends PHPUnit_Framework_TestCase {

   private $infUid = '1395be8293373465ab172b8b1b677e31';
   private $infName = 'David Chancogne';

   private $infUid2 = 'ae1955b0f92037c895e5bfdd259a1304';

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
      $this->assertCount(5, $posts['links']);
      $this->assertTrue(in_array($posts['links'][0]['linkbacks'][0]['influencer_uid'], $infs));

      Traackr\TraackrApi::setJsonOutput(true);
      $jsonOne =  Traackr\Analysis::toplinks(array('influencers' => $infs));
      $jsonTwo = Traackr\Analysis::toplinks(array('influencers' => $this->infUid.','.$this->infUid2));
      $this->assertJsonStringEqualsJsonString($jsonOne, $jsonTwo);

   } // End function toplinksTest()

   /**
    * @group read-only
    * @expectedException Traackr\MissingParameterException
    */
   public function testToplinksMissingParameter() {

      Traackr\Analysis::toplinks();

   } // End function testShowMissingParameter()


} // End class AnalysisTest