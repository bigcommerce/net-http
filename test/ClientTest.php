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
		$this->assertContains("CANHAZHTTPGET", $client->getBody());
	}

	public function testPostRequest()
	{
		$client = new Net_Http_Client();
		$client->post(self::HOST.'/basic/post', array("greeting"=>"Hello", "from"=>"Net_Http_Client"));
		$this->assertEquals(200, $client->getStatus());
		$this->assertContains("Hello back", $client->getBody());
	}

	public function testHeadRequestWithSetHeader()
	{
		$client = new Net_Http_Client();
		$client->setHeader('X-Requested-Square', 4);
		$client->head(self::HOST.'/basic/head');
		$this->assertEquals(200, $client->getStatus());
		$this->assertEquals('True', $client->getHeader('X-Requested-By-Head'));
		$this->assertEquals(4*4, $client->getHeader('X-Requested-Result'));
	}

	public function testContentNegotiationOnMultipleRequests()
	{
		$client = new Net_Http_Client();

		$client->setHeader('Accept', 'application/xml');
		$client->get(self::HOST.'/basic/content');
		$this->assertEquals(200, $client->getStatus());
		$this->assertContains('<title>Hello World</title>', $client->getBody());

		$client->setHeader('Accept', 'text/javascript');
		$client->get(self::HOST.'/basic/content');
		$this->assertEquals(200, $client->getStatus());
		$this->assertContains('{message:"Hello World"}', $client->getBody());
	}

	public function testBasicAuthenticationOnMultipleRequests()
	{
		$client = new Net_Http_Client();

		$client->get(self::HOST.'/basic/auth/basic');
		$this->assertEquals(401, $client->getStatus());
		$this->assertContains('You are not authorized', $client->getBody());

		$client->setBasicAuth('random', 'random');
		$client->get(self::HOST.'/basic/auth/basic');
		$this->assertEquals(401, $client->getStatus());
		$this->assertContains('Invalid username and password', $client->getBody());

		$client->setBasicAuth('username', 'password');
		$client->get(self::HOST.'/basic/auth/basic');
		$this->assertEquals(200, $client->getStatus());
		$this->assertContains('You are logged in', $client->getBody());
	}

	public function testNotFoundError()
	{
		$client = new Net_Http_Client();
		$client->get(self::HOST.'/basic/errors/missing');
		$this->assertEquals(404, $client->getStatus());
		$this->assertContains('Resource Not Found', $client->getBody());
	}

	public function testInternalServerError()
	{
		$client = new Net_Http_Client();
		$client->get(self::HOST.'/basic/errors/crash');
		$this->assertEquals(500, $client->getStatus());
		$this->assertContains('The Server Exploded', $client->getBody());
	}

	public function testFailOnNetworkTimeout()
	{
		$client = new Net_Http_Client();
		$client->setTimeout(1);

		try {
			$client->get(self::HOST.'/basic/errors/timeout');
		} catch(Net_Http_NetworkError $e) {
			$this->assertContains("timed out", $e->getMessage());
			$this->assertEquals(28, $e->getCode());
		}
	}

	public function testFailOnErrorThrowsExceptionFromClientError()
	{
		$client = new Net_Http_Client();
		$client->failOnError();

		try {
			$client->get(self::HOST.'/basic/errors/missing');
		} catch(Net_Http_ClientError $e) {
			$this->assertContains('Not Found', $e->getMessage());
			$this->assertEquals(404, $e->getCode());
			$this->assertEquals(404, $e->getResponse()->getStatus());
			$this->assertContains('Resource Not Found', $e->getBody());
		}
	}

	public function testFailOnErrorThrowsExceptionFromServerError()
	{
		$client = new Net_Http_Client();
		$client->failOnError();

		try {
			$client->get(self::HOST.'/basic/errors/crash');
		} catch(Net_Http_ServerError $e) {
			$this->assertContains('Internal Server Error', $e->getMessage());
			$this->assertEquals(500, $e->getCode());
			$this->assertEquals(500, $e->getResponse()->getStatus());
			$this->assertContains('The Server Exploded', $e->getBody());
		}
	}
}