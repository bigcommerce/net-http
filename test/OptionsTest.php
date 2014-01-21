<?php
/**
 * Copyright (c) 2011, BigCommerce Pty. Ltd. <http://www.bigcommerce.com>
 * All rights reserved.
 * 
 * This library is free software; refer to the terms in the LICENSE file found
 * with this source code for details about modification and redistribution.
 */

require_once __DIR__ . '/HttpTestCase.php';

class OptionsTest extends HttpTestCase {

	public function testCanSetGlobalTimeoutOption()
	{
		Net_Http::setTimeout(1);

		try {
			$clientOne = new Net_Http_Client();
			$clientOne->get(self::HOST.'/basic/errors/timeout');
		} catch(Net_Http_NetworkError $e) {
			$this->assertContains("timed out", $e->getMessage());
			$this->assertEquals(28, $e->getCode());
		}
	}

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

	public function tearDown()
	{
		Net_Http::clearOptions();
	}

}