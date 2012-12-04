<?php
/**
 * Copyright (c) 2011, BigCommerce Pty. Ltd. <http://www.bigcommerce.com>
 * All rights reserved.
 * 
 * This library is free software; refer to the terms in the LICENSE file found
 * with this source code for details about modification and redistribution.
 */

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

	public function tearDown()
	{
		Net_Http::clearOptions();
	}

}