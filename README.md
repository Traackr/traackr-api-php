Traackr API - PHP Client
========================

Introduction
------------

This is a PHP implementation of the Traackr API.
For documentation on the Traackr API, see: [http://iodocs.traackr.com/](http://iodocs.traackr.com/)


Installation
------------

You can clone this GitHub repository to get the latest version of the code.

This library is also available via Composer if that's what you use to manage your dependencies. To do this, simply add this to your `composer.json` file:

	"minimin-stability": "dev"",
	"require": {
		"traackr/traackr-api-php": "dev-master"
	}


Usage
-----

You will need an API key to make use this library. Contact api@traackr.com to get your key.

### Include the library ###

You include this library with:

	require_once('lib/TraackrApi.php');

### Set your API key ###

To set your API key use:

	TraackrApi::setApiKey(<your-api-key>);

Some calls require a Customer Key (see [documentation](http://iodocs.traackr.com)). You do not need to pass this key to these calls. You can set (once and for all) your Customer Key with:

	TraackrApi::setCustomerKey(<your-customer-key);

The client library will take care of including your customer key when needed.

### API calls ###

All API calls map to static functions with parameters matching the API call parameters (see [documentation](http://iodocs.traackr.com)). For instance to call `/influencers/show` you can use:

	Influencers::show(<influencer-uid>);


Unit Tests
----------

To run unit tests, you will need to install dependencies required by this project.
First, install Composer locally:

	$ curl -sS https://getcomposer.org/installer | php


Then install dependencies:

	$ php composer.phar install


Before you run unit tests, you need to specify an API key and a Customer key. These 2 values can be specified via ENV variables (so they don't have to be hardcoded on the unit tests).

	# export TRAACKR_API_KEY=<your-api-key>
	# export TARACKR_CUSTOMER_KEY=<your-customer-key>

Run the entire test suite:

	./bin/phpunit test

