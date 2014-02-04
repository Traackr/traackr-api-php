<?php

require_once(dirname(__FILE__) . '/../../lib/TraackrApi.php');

class InfluencersTest extends PHPUnit_Framework_TestCase {

   private $infUid = '1395be8293373465ab172b8b1b677e31';
   private $infTag = 'traackr-api-test';
   private $infName = 'David Chancogne';

   private $infUid2 = 'ae1955b0f92037c895e5bfdd259a1304';

   private $savedCustomerKey;

   public function setUp() {

      $this->savedCustomerKey = Traackr\TraackrApi::getCustomerKey();

      // remove all tags
      Traackr\Influencers::tagRemove(array(
         'tags' => $this->infTag,
         'all' => true)
      );

      // Ensure outout is PHP by default
      Traackr\TraackrApi::setJsonOutput(false);

   } // End function setUp()

   public function tearDown() {

      Traackr\TraackrApi::setCustomerKey($this->savedCustomerKey);

   } // End functiuon tearDown()


   public function testShow() {

      // Ensure JSON output
      Traackr\TraackrApi::setJsonOutput(true);

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
             "reach":"0.25","resonance":"0.57",
             "tags":[]
            }
         }}',
         Traackr\Influencers::show($this->infUid)
      );

      Traackr\TraackrApi::setCustomerKey('');
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
             "reach":"0.25","resonance":"0.57"
            }
         }}',
         Traackr\Influencers::show($this->infUid)
      );

      // Revert back to PHP output
      Traackr\TraackrApi::setJsonOutput(false);
      $inf = Traackr\Influencers::show($this->infUid);
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

      $inf = Traackr\Influencers::show($this->infUid, array('with_channels' => true) );
      $this->assertTrue(isset($inf['influencer'][$this->infUid]['channels']),
         'Channels not returned');
      $twitter = array_values(array_filter($inf['influencer'][$this->infUid]['channels'],
         function($elm) { return $elm['root_domain'] == 'twitter';}));
      $this->assertEquals('http://twitter.com/dchancogne', $twitter[0]['url']);

      $infs = Traackr\Influencers::show(array($this->infUid, $this->infUid2));
      $this->assertCount(2, $infs['influencer'],
         'Incorrected number of influencers returned');
      $infs = Traackr\Influencers::show($this->infUid.','.$this->infUid2);
      $this->assertCount(2, $infs['influencer'],
         'Incorrected number of influencers returned');

   } // End function testShow()

   /**
    * @expectedException Traackr\NotFoundException
    */
   public function testShowNotFound() {

      Traackr\Influencers::show('00000');

   } // End function testShowNotFound()

   /**
    * @expectedException Traackr\MissingParameterException
    */
   public function testShowMissingParameter() {

      Traackr\Influencers::show('');

   } // End function testShowMissingParameter()


   public function testConnections() {

      // Ensure JSON output
      Traackr\TraackrApi::setJsonOutput(true);

      $this->assertJsonStringEqualsJsonString(
         '{
           "influencer":{
             "1395be8293373465ab172b8b1b677e31":{
               "uid":"1395be8293373465ab172b8b1b677e31",
               "connections_to":[{
                 "type":"TRAACKR",
                 "native_id":"ae1955b0f92037c895e5bfdd259a1304",
                 "connection_score":"199",
                 "connection_metrics":{
                   "mention_percent_frequency":"0.12",
                   "retweet_percent_frequency":"0.0",
                   "mention_frequency":"11",
                   "mention_count":"18",
                   "retweet_frequency":"0",
                   "retweet_count":"0"
                 }
               },{
                 "type":"TWITTER_USER",
                 "native_id":"influence_this",
                 "connection_score":"16",
                 "connection_metrics":{
                   "mention_percent_frequency":"0.04",
                   "retweet_percent_frequency":"0.0",
                   "mention_frequency":"4",
                   "mention_count":"4",
                   "retweet_frequency":"0",
                   "retweet_count":"0"
                 }
               }]
             }
           }
         }',
         Traackr\Influencers::connections($this->infUid, 'to')
      );

      $this->assertJsonStringEqualsJsonString(
         '{
           "influencer":{
             "1395be8293373465ab172b8b1b677e31":{
               "uid":"1395be8293373465ab172b8b1b677e31",
               "connections_from":[]
             }
           }
         }',
         Traackr\Influencers::connections($this->infUid, 'from')
      );

      $this->assertJsonStringEqualsJsonString(
         '{
           "influencer":{
             "1395be8293373465ab172b8b1b677e31":{
               "uid":"1395be8293373465ab172b8b1b677e31",
               "connections_to":[{
                 "type":"TRAACKR",
                 "native_id":"ae1955b0f92037c895e5bfdd259a1304",
                 "connection_score":"199",
                 "connection_metrics":{
                   "mention_percent_frequency":"0.12",
                   "retweet_percent_frequency":"0.0",
                   "mention_frequency":"11",
                   "mention_count":"18",
                   "retweet_frequency":"0",
                   "retweet_count":"0"
                 }
               },{
                 "type":"TWITTER_USER",
                 "native_id":"influence_this",
                 "connection_score":"16",
                 "connection_metrics":{
                   "mention_percent_frequency":"0.04",
                   "retweet_percent_frequency":"0.0",
                   "mention_frequency":"4",
                   "mention_count":"4",
                   "retweet_frequency":"0",
                   "retweet_count":"0"
                 }
               }],
               "connections_from":[]
             }
           }
         }',
         Traackr\Influencers::connections($this->infUid)
      );

   } // End function testConnections

   /**
    * @expectedException Traackr\NotFoundException
    */
   public function testConnectionsNotFound() {

      Traackr\Influencers::connections('to', '00000');
      Traackr\Influencers::connections('from', '00000');

   } // End function testConnectionsNotFound()


   public function testLookupTwitter() {

      // Ensure JSON output
      Traackr\TraackrApi::setJsonOutput(true);

      $this->assertJsonStringEqualsJsonString(
         '{
           "influencer":{
             "dchancogne":{
               "uid":"1395be8293373465ab172b8b1b677e31",
               "name":"David Chancogne",
               "description":"Web. Geek: http://traackr-people.tumblr.com. Traackr: http://traackr.com. Propz: http://propz.me",
               "primary_affiliation":"Traackr",
               "title":"CTO",
               "location":"Cambridge, MA, United States",
               "email":"dchancogne@traackr.com",
               "thumbnail_url":"http://pbs.twimg.com/profile_images/2678827459/a1d9ca2d94e329636cc753133b98525a.png",
               "avatar":{
                 "large":"http://pbs.twimg.com/profile_images/2678827459/a1d9ca2d94e329636cc753133b98525a.png",
                 "medium":"http://pbs.twimg.com/profile_images/2678827459/a1d9ca2d94e329636cc753133b98525a_bigger.png",
                 "small":"http://pbs.twimg.com/profile_images/2678827459/a1d9ca2d94e329636cc753133b98525a_normal.png"
               },
               "reach":"0.25",
               "resonance":"0.57"
             }
           }
         }',
         Traackr\Influencers::lookupTwitter('dchancogne')
      );

      // NOTE
      // Disable customer key so that 'show' doe not return tags b/c lookupTwitter doesn't currently
      Traackr\TraackrApi::setCustomerKey('');
      Traackr\TraackrApi::setJsonOutput(false);
      $inf = Traackr\Influencers::show($this->infUid);
      $twitter = Traackr\Influencers::lookupTwitter('dchancogne');
      $this->assertJsonStringEqualsJsonString(
         json_encode($inf['influencer'][$this->infUid]),
         json_encode($twitter['influencer']['dchancogne'])
      );

   } // End function testLookupTwitter()

   /**
    * @expectedException Traackr\NotFoundException
    */
   public function testLookupTwitterNotFound() {

      Traackr\Influencers::lookupTwitter('000RandomHandle000');

   } // End function testLookupTwitterNotFound()


   public function testTagAdd() {

      $inf = Traackr\Influencers::show($this->infUid);
      $this->assertCount(0, $inf['influencer'][$this->infUid]['tags']);

      Traackr\Influencers::tagAdd(array(
         'influencers' => $this->infUid,
         'tags' => $this->infTag));
      $inf = Traackr\Influencers::show($this->infUid);
      $this->assertCount(1, $inf['influencer'][$this->infUid]['tags']);
      $this->assertTrue(in_array($this->infTag, $inf['influencer'][$this->infUid]['tags']));

      Traackr\Influencers::tagRemove(array(
         'influencers' => $this->infUid,
         'tags' => $this->infTag));

      $inf = Traackr\Influencers::show($this->infUid);
      $this->assertCount(0, $inf['influencer'][$this->infUid]['tags']);
      $inf2 = Traackr\Influencers::show($this->infUid2);
      $this->assertCount(0, $inf2['influencer'][$this->infUid2]['tags']);

      Traackr\Influencers::tagAdd(array(
         'influencers' => array($this->infUid, $this->infUid2),
         'tags' => $this->infTag));
      $inf = Traackr\Influencers::show($this->infUid);
      $this->assertCount(1, $inf['influencer'][$this->infUid]['tags']);
      $this->assertTrue(in_array($this->infTag, $inf['influencer'][$this->infUid]['tags']));
      $inf2 = Traackr\Influencers::show($this->infUid2);
      $this->assertCount(1, $inf2['influencer'][$this->infUid2]['tags']);
      $this->assertTrue(in_array($this->infTag, $inf2['influencer'][$this->infUid2]['tags']));

      Traackr\Influencers::tagRemove(array(
         'influencers' => $this->infUid,
         'tags' => $this->infTag));
      Traackr\Influencers::tagRemove(array(
         'influencers' => $this->infUid2,
         'tags' => $this->infTag));

      $inf = Traackr\Influencers::show($this->infUid);
      $this->assertCount(0, $inf['influencer'][$this->infUid]['tags']);
      $inf2 = Traackr\Influencers::show($this->infUid2);
      $this->assertCount(0, $inf2['influencer'][$this->infUid2]['tags']);

      Traackr\Influencers::tagAdd(array(
         'influencers' => $this->infUid.','.$this->infUid2,
         'tags' => $this->infTag));
      $inf = Traackr\Influencers::show($this->infUid);
      $this->assertCount(1, $inf['influencer'][$this->infUid]['tags']);
      $this->assertTrue(in_array($this->infTag, $inf['influencer'][$this->infUid]['tags']));
      $inf2 = Traackr\Influencers::show($this->infUid2);
      $this->assertCount(1, $inf2['influencer'][$this->infUid2]['tags']);
      $this->assertTrue(in_array($this->infTag, $inf2['influencer'][$this->infUid2]['tags']));

      Traackr\Influencers::tagRemove(array(
         'influencers' => $this->infUid,
         'tags' => $this->infTag));
      Traackr\Influencers::tagRemove(array(
         'influencers' => $this->infUid2,
         'tags' => $this->infTag));


   } // End function testTagAdd()

   public function testTagRemove() {

      $inf = Traackr\Influencers::show($this->infUid);
      $this->assertCount(0, $inf['influencer'][$this->infUid]['tags']);

      Traackr\Influencers::tagAdd(array(
         'influencers' => $this->infUid,
         'tags' => $this->infTag));

      try {
         Traackr\Influencers::tagRemove(array(
            'tags' => $this->infTag));
      }
      catch (Traackr\MissingParameterException $e) {
         $this->assertInstanceOf('Traackr\MissingParameterException', $e, 'Missing argument missed');
      }
      // Make sure exception is not thrown
      Traackr\Influencers::tagRemove(array(
         'tags' => $this->infTag,
         'all' => true));
      $inf = Traackr\Influencers::show($this->infUid);
      $this->assertCount(0, $inf['influencer'][$this->infUid]['tags']);

      Traackr\Influencers::tagAdd(array(
         'influencers' => $this->infUid,
         'tags' => $this->infTag));
      Traackr\Influencers::tagRemove(array(
         'influencers' => $this->infUid,
         'tags' => $this->infTag));
      $this->assertCount(0, $inf['influencer'][$this->infUid]['tags']);
      $this->assertTrue(!in_array($this->infTag, $inf['influencer'][$this->infUid]['tags']));

   } // End function testTagRemove()

   public function testTagList() {

      $infs = Traackr\Influencers::tagList(array('tag' => 'SomeRandomTagNeverUser'));
      $this->assertCount(0, $infs['influencers']);

      Traackr\Influencers::tagAdd(array(
         'influencers' => $this->infUid,
         'tags' => $this->infTag));
      $infs = Traackr\Influencers::tagList(array('tag' => $this->infTag));
      $this->assertCount(1, $infs['influencers']);
      $this->assertTrue(in_array($this->infUid, $infs['influencers']));
      Traackr\Influencers::tagRemove(array(
         'influencers' => $this->infUid,
         'tags' => $this->infTag));

   } // End function testTagList()

   public function testLookup() {

      $inf = Traackr\Influencers::lookup(array('name' => $this->infName));
      $this->assertTrue(isset($inf['page_info']), 'No paging info');
      $this->assertCount(1, $inf['influencers'], 'Found multiple results');
      $this->assertEquals($this->infUid, $inf['influencers'][0]['uid'], 'Invalid influencer/UID found');

      $inf = Traackr\Influencers::lookup(array('name' => 'xxxXXXxxx'));
      $this->assertCount(0, $inf['influencers'], 'Results found');

      // Ensure JSON output
      Traackr\TraackrApi::setJsonOutput(true);
      $this->assertJsonStringEqualsJsonString(
         '{
           "page_info":{
             "has_more":false,
             "current_page":0,
             "next_page":0,
             "page_count":25,
             "results_count":1,
             "total_results_count":1
           },
           "influencers":[{
             "uid":"1395be8293373465ab172b8b1b677e31",
             "name":"David Chancogne",
             "description":"Web. Geek: http://traackr-people.tumblr.com. Traackr: http://traackr.com. Propz: http://propz.me",
             "primary_affiliation":"Traackr",
             "title":"CTO",
             "location":"Cambridge, MA, United States",
             "thumbnail_url":"http://pbs.twimg.com/profile_images/2678827459/a1d9ca2d94e329636cc753133b98525a.png",
             "avatar":{
               "large":"http://pbs.twimg.com/profile_images/2678827459/a1d9ca2d94e329636cc753133b98525a.png",
               "medium":"http://pbs.twimg.com/profile_images/2678827459/a1d9ca2d94e329636cc753133b98525a_bigger.png",
               "small":"http://pbs.twimg.com/profile_images/2678827459/a1d9ca2d94e329636cc753133b98525a_normal.png"
             },
             "reach":"0.25",
             "resonance":"0.57",
             "relevance":"0.0"
           }]
         }',
         Traackr\Influencers::lookup(array('name' => $this->infName)),
         'Record not extact'
      );

   } // End function testLookup()

   public function testSearch() {

      $inf = Traackr\Influencers::search(array('keywords' => 'traackr'));
      $this->assertGreaterThan(0, $inf['influencers'], 'No results found');

      $inf = Traackr\Influencers::search(array('keywords' => 'xxxaaaxxx'));
      $this->assertCount(0, $inf['influencers'], 'Results found');

   } // End fucntion testSearch()

} // End class InfluencersTest