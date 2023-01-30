<?php
/**
 * Copyright (c) 2011, BigCommerce Pty. Ltd. <http://www.bigcommerce.com>
 * All rights reserved.
 * 
 * This library is free software; refer to the terms in the LICENSE file found
 * with this source code for details about modification and redistribution.
 */

require_once __DIR__ . '/HttpTestCase.php';

class ClientTest extends HttpTestCase
{
	public function testGetRequest()
	{
		$client = new Net_Http_Client();
		$client->get(self::HOST.'/basic/get');
		$this->assertEquals(200, $client->getStatus());
		$this->assertStringContainsString("CANHAZHTTPGET", $client->getBody());
	}

	public function testPostRequest()
	{
		$client = new Net_Http_Client();
		$client->post(self::HOST.'/basic/post', array("greeting"=>"Hello", "from"=>"Net_Http_Client"));
		$this->assertEquals(200, $client->getStatus());
		$this->assertStringContainsString("Hello back", $client->getBody());
	}

	public function testNotFoundError()
	{
		$client = new Net_Http_Client();
		$client->get(self::HOST.'/basic/errors/missing');
		$this->assertEquals(404, $client->getStatus());
		$this->assertStringContainsString('Resource Not Found', $client->getBody());
	}

	public function testInternalServerError()
	{
		$client = new Net_Http_Client();
		$client->get(self::HOST.'/basic/errors/crash');
		$this->assertEquals(500, $client->getStatus());
		$this->assertStringContainsString('The Server Exploded', $client->getBody());
	}

	public function testFailOnErrorThrowsExceptionFromClientError()
	{
		$client = new Net_Http_Client();
		$client->failOnError();

		try {
			$client->get(self::HOST.'/basic/errors/missing');
		} catch(Net_Http_ClientError $e) {
			$this->assertStringContainsString('HTTP/2 404', $e->getMessage());
			$this->assertEquals(404, $e->getCode());
			$this->assertEquals(404, $e->getResponse()->getStatus());
			$this->assertStringContainsString('Resource Not Found', $e->getBody());
		}
	}

	public function testFailOnErrorThrowsExceptionFromServerError()
	{
		$client = new Net_Http_Client();
		$client->failOnError();

		try {
			$client->get(self::HOST.'/basic/errors/crash');
		} catch (Net_Http_ServerError $e) {
			$this->assertStringContainsString('HTTP/2 500', $e->getMessage());
			$this->assertEquals(500, $e->getCode());
			$this->assertEquals(500, $e->getResponse()->getStatus());
			$this->assertStringContainsString('The Server Exploded', $e->getBody());
		}
	}
}
