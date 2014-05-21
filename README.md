Traackr API - PHP Client
========================
[![Build Status](https://travis-ci.org/Traackr/traackr-api-php.png?branch=master)](https://travis-ci.org/Traackr/traackr-api-php)

Introduction
------------

This is a PHP implementation of the Traackr API. Documentation for this PHP client is available here: [http://traackr.github.io/traackr-api-docs](http://traackr.github.io/traackr-api-docs).  
For documentation for the Traackr API itself, see: [http://http://api.docs.traackr.com](http://http://api.docs.traackr.com).


Installation
------------

You can clone this GitHub repository to get the latest version of the code.

This library is also available via Composer if that's what you use to manage your dependencies. To do this, simply add this to your `composer.json` file:

	"minimum-stability": "dev",
	"require": {
		"traackr/traackr-api-php": "dev-master"
	}


Usage
-----

You will need an API key to make use this library. Contact api@traackr.com to get your key.

### Include the library ###

You include this library with:

    require_once('lib/TraackrApi.php');

If you are using Composer the autoload functionality should automatically load the appropriate PHP files (i.e. `require 'vendor/autoload.php'` will load the library files).

### Set your API key ###

To set your API key use:

	TraackrApi::setApiKey(<your-api-key>);

Some calls require a Customer Key (see [documentation](http://iodocs.traackr.com)). You do not need to pass this key to these calls. You can set (once and for all) your Customer Key with:

	TraackrApi::setCustomerKey(<your-customer-key);

The client library will take care of including your customer key when needed.

> Note that you can also specify your API key and customer key via environment variables. See the unit tests section.


### API calls ###

All API calls map to static functions with parameters matching the API call parameters (see [documentation](http://iodocs.traackr.com)). For instance to call `/influencers/show` you can use:

	Influencers::show(<influencer-uid>);


Unit Tests
----------

### Setup ###

To run unit tests, you will need to install dependencies required by this project.
First, install Composer locally:

	$ curl -sS https://getcomposer.org/installer | php


Then install dependencies:

	$ php composer.phar install


### Running the tests ###

The unit tests come with a public API key that you can use to run the unit tests. However the API key provided in the unit tests is only allowed to access read-only end points (i.e. API calls that do not add, modify or delete any data).  
To run these read-only tests you can execute:

    ./bin/phpunit --group read-only test

If you try to run the entire test suite with this public API key you will get errors when trying to access functions that change data.

### Running the entire test suite ###

To run the entire test suite you will need an API key that has full access to the API. You can request one by emailing [api-support@traackr.com](maitto:api-support@traackr.com).

Before you run unit tests, you need to specify an API key and a Customer key. These 2 values can be specified via ENV variables (so they don't have to be hardcoded in the unit tests).

	# export TRAACKR_API_KEY=<your-api-key>
	# export TRAACKR_CUSTOMER_KEY=<your-customer-key>

Run the entire test suite:

	./bin/phpunit test
