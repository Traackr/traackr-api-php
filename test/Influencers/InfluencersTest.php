<?php

require_once(dirname(__FILE__) . '/../../lib/TraackrApi.php');

class InfluencersTest extends PHPUnit_Framework_TestCase {

   private $infUid = '1395be8293373465ab172b8b1b677e31';
   private $infTag = 'traackr-api-test';

   private $infUid2 = 'ae1955b0f92037c895e5bfdd259a1304';

   private $savedCustomerKey;

   public function setUp() {

      $this->savedCustomerKey = TraackrApi::getCustomerKey();

      // Ensure JSON output
      TraackrApi::setJsonOutput(true);

   } // End function setUp()

   public function tearDown() {

      TraackrApi::setCustomerKey($this->savedCustomerKey);

   } // End functiuon tearDown()


   public function testShow() {

      $this->assertJsonStringEqualsJsonString(
         '{"influencer":{"1395be8293373465ab172b8b1b677e31":
            {"uid":"1395be8293373465ab172b8b1b677e31",
             "name":"David Chancogne",
             "description":"Web. Geek: http://traackr-people.tumblr.com. Traackr: http://traackr.com. Propz: http://propz.me",
             "primary_affiliation":"Traackr",
             "title":"CTO",
             "location":"Cambridge, MA, United States",
             "email":"dchancogne@traackr.com",
             "thumbnail_url":"http://pbs.twimg.com/profile_images/2678827459/a1d9ca2d94e329636cc753133b98525a.png",
             "avatar":{"large":"http://pbs.twimg.com/profile_images/2678827459/a1d9ca2d94e329636cc753133b98525a.png","medium":"http://pbs.twimg.com/profile_images/2678827459/a1d9ca2d94e329636cc753133b98525a_bigger.png","small":"http://pbs.twimg.com/profile_images/2678827459/a1d9ca2d94e329636cc753133b98525a_normal.png"},
             "reach":"0.24","resonance":"0.57",
             "tags":[]
            }
         }}',
         Influencers::show($this->infUid)
      );

      TraackrApi::setCustomerKey('');
      $this->assertJsonStringEqualsJsonString(
         '{"influencer":{"1395be8293373465ab172b8b1b677e31":
            {"uid":"1395be8293373465ab172b8b1b677e31",
             "name":"David Chancogne",
             "description":"Web. Geek: http://traackr-people.tumblr.com. Traackr: http://traackr.com. Propz: http://propz.me",
             "primary_affiliation":"Traackr",
             "title":"CTO",
             "location":"Cambridge, MA, United States",
             "email":"dchancogne@traackr.com",
             "thumbnail_url":"http://pbs.twimg.com/profile_images/2678827459/a1d9ca2d94e329636cc753133b98525a.png",
             "avatar":{"large":"http://pbs.twimg.com/profile_images/2678827459/a1d9ca2d94e329636cc753133b98525a.png","medium":"http://pbs.twimg.com/profile_images/2678827459/a1d9ca2d94e329636cc753133b98525a_bigger.png","small":"http://pbs.twimg.com/profile_images/2678827459/a1d9ca2d94e329636cc753133b98525a_normal.png"},
             "reach":"0.24","resonance":"0.57"
            }
         }}',
         Influencers::show($this->infUid)
      );


      TraackrApi::setJsonOutput(false);
      $inf = Influencers::show($this->infUid);
      $this->assertTrue(isset($inf['influencer']),
         'Unable to find "influencer" field');
      $this->assertTrue(isset($inf['influencer'][$this->infUid]),
         'Unable to find matchiung UID field');
      $this->assertFalse(isset($inf['influencer'][$this->infUid]['channels']),
         'Channels should not have been returned');
      $this->assertEquals($this->infUid, $inf['influencer'][$this->infUid]['uid'],
         'Unable to find matching "uid" field');
      $this->assertEquals('David Chancogne', $inf['influencer'][$this->infUid]['name'],
         'Unable to find matching "name" field');

      $inf = Influencers::show($this->infUid, true);
      $this->assertTrue(isset($inf['influencer'][$this->infUid]['channels']),
         'Channels not returned');

      $infs = Influencers::show(array($this->infUid, $this->infUid2));
      $this->assertCount(2, $infs['influencer'],
         'Incorrected number of influencers returned');
      $infs = Influencers::show($this->infUid.','.$this->infUid2);
      $this->assertCount(2, $infs['influencer'],
         'Incorrected number of influencers returned');

   } // End function testShow()

   /**
    * @expectedException NotFoundException
    */
   public function testShowNotFound() {

      Influencers::show('00000');

   } // End function testShowNotFound()

   /**
    * @expectedException MissingParameterException
    */
   public function testShowMissingParameter() {

      Influencers::show('');

   } // End function testShowMissingParameter()


   // public function testConnections() {

   //    $this->assertJsonStringEqualsJsonString(
   //       '{"influencer":{"1395be8293373465ab172b8b1b677e31":{"uid":"1395be8293373465ab172b8b1b677e31","connections_to":[{"type":"TRAACKR","native_id":"ae1955b0f92037c895e5bfdd259a1304","connection_score":"92","connection_metrics":{"mention_percent_frequency":"0.08","retweet_percent_frequency":"0.0","mention_frequency":"7","mention_count":"13","retweet_frequency":"0","retweet_count":"0"}},{"type":"TWITTER_USER","native_id":"influence_this","connection_score":"25","connection_metrics":{"mention_percent_frequency":"0.06","retweet_percent_frequency":"0.0","mention_frequency":"5","mention_count":"5","retweet_frequency":"0","retweet_count":"0"}}]}}}',
   //       Influencers::connections('to', $this->infUid)
   //    );

   //    $this->assertJsonStringEqualsJsonString(
   //       '{"influencer":{"1395be8293373465ab172b8b1b677e31":{"uid":"1395be8293373465ab172b8b1b677e31","connections_from":[]}}}',
   //       Influencers::connections('from', $this->infUid)
   //    );

   // } // End function testConnections

   /**
    * @expectedException NotFoundException
    */
   public function testConnectionsNotFound() {

      Influencers::connections('to', '00000');
      Influencers::connections('from', '00000');

   } // End function testConnectionsNotFound()


   public function testLookupTwitter() {

      $this->assertJsonStringEqualsJsonString(
         '{"influencer":{"dchancogne":{"uid":"1395be8293373465ab172b8b1b677e31","name":"David Chancogne","description":"Web. Geek: http://traackr-people.tumblr.com. Traackr: http://traackr.com. Propz: http://propz.me","primary_affiliation":"Traackr","title":"CTO","location":"Cambridge, MA, United States","email":"dchancogne@traackr.com","thumbnail_url":"http://pbs.twimg.com/profile_images/2678827459/a1d9ca2d94e329636cc753133b98525a.png","avatar":{"large":"http://pbs.twimg.com/profile_images/2678827459/a1d9ca2d94e329636cc753133b98525a.png","medium":"http://pbs.twimg.com/profile_images/2678827459/a1d9ca2d94e329636cc753133b98525a_bigger.png","small":"http://pbs.twimg.com/profile_images/2678827459/a1d9ca2d94e329636cc753133b98525a_normal.png"},"reach":"0.24","resonance":"0.57"}}}',
         Influencers::lookupTwitter('dchancogne')
      );
      // $this->assertJsonStringEqualsJsonString(
      //    Influencers::show('1395be8293373465ab172b8b1b677e31'),
      //    Influencers::lookupTwitter('dchancogne')
      // );

   } // End function testLookupTwitter()

   /**
    * @expectedException NotFoundException
    */
   public function testLookupTwitterNotFound() {

      Influencers::lookupTwitter('000RandomHandle000');

   } // End function testLookupTwitterNotFound()


   public function testTagAdd() {

      TraackrApi::setJsonOutput(false);

      $inf = Influencers::show($this->infUid);
      $this->assertCount(0, $inf['influencer'][$this->infUid]['tags']);

      Influencers::tagAdd($this->infUid, $this->infTag);
      $inf = Influencers::show($this->infUid);
      $this->assertCount(1, $inf['influencer'][$this->infUid]['tags']);
      $this->assertTrue(in_array($this->infTag, $inf['influencer'][$this->infUid]['tags']));

      Influencers::tagRemove($this->infUid, $this->infTag);

   } // End function testTagAdd()

   public function testTagRemove() {

      TraackrApi::setJsonOutput(false);

      $inf = Influencers::show($this->infUid);
      $this->assertCount(0, $inf['influencer'][$this->infUid]['tags']);

      Influencers::tagAdd($this->infUid, $this->infTag);
      Influencers::tagRemove($this->infUid, $this->infTag);
      $this->assertCount(0, $inf['influencer'][$this->infUid]['tags']);
      $this->assertTrue(!in_array($this->infTag, $inf['influencer'][$this->infUid]['tags']));

   } // End function testTagRemove()

   public function testTagList() {

      TraackrApi::setJsonOutput(false);

      $infs = Influencers::tagList('SomeRandomTagNeverUser');
      $this->assertCount(0, $infs['influencers']);

      Influencers::tagAdd($this->infUid, $this->infTag);
      $infs = Influencers::tagList($this->infTag);
      $this->assertCount(1, $infs['influencers']);
      $this->assertTrue(in_array($this->infUid, $infs['influencers']));
      Influencers::tagRemove($this->infUid, $this->infTag);

   } // End function testTagList()

} // End class InfluencersTest