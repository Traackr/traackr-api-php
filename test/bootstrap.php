<?php

// Load API library
require_once(dirname(__FILE__) . '/../lib/TraackrApi.php');

// Set API key if none is defined in the env
// We want the env value to take precedence to enable testing against QA
if ( !isset($_ENV['TRAACKR_API_KEY']) ) {
   Traackr\TraackrApi::setApiKey('5adab9df789c2147116881f36785f6c3');
}

Traackr\TraackrApi::setExtraHeaders(array("X-TraackrApp-Session: TraackrAPI-UnitTest"));