<?php
/**
 * Copyright (c) 2011, BigCommerce Pty. Ltd. <http://www.bigcommerce.com>
 * All rights reserved.
 * 
 * This library is free software; refer to the terms in the LICENSE file found
 * with this source code for details about modification and redistribution.
 */

require_once __DIR__ . '/HttpTestCase.php';

class OptionsTest extends HttpTestCase
{
	public function testMergesHttpOptions()
	{
		Net_Http::setTimeout(1000);

		$client = new Net_Http_Client;
		$opts   = $client->getOptions();

		$this->assertArrayHasKey(CURLOPT_TIMEOUT, $opts);
		$this->assertEquals(1000, $opts[CURLOPT_TIMEOUT]);
	}

	public function testClientOptionsOverrideHttpOptions()
	{
		Net_Http::setTimeout(1000);

		$client = new Net_Http_Client;
		$client->setTimeout(2000);

		$opts = $client->getOptions();

		$this->assertArrayHasKey(CURLOPT_TIMEOUT, $opts);
		$this->assertEquals(2000, $opts[CURLOPT_TIMEOUT]);
	}

	public function tearDown(): void
	{
		Net_Http::clearOptions();
	}
}
