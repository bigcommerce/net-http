<?php
/**
 * Copyright (c) 2011, BigCommerce Pty. Ltd. <http://www.bigcommerce.com>
 * All rights reserved.
 * 
 * This library is free software; refer to the terms in the LICENSE file found
 * with this source code for details about modification and redistribution.
 */

class ResponseTest extends PHPUnit_Framework_TestCase {

	public function testObjectConstruction()
	{
		$response = new Net_Http_Response(200, array("Content-Type"=>"text/plain"), "hello world");
		
		$this->assertTrue($response->isOk());
		$this->assertEquals("text/plain", $response->getHeader("Content-Type"));
		$this->assertEquals("hello world", $response->getBody());
	}

}