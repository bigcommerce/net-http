<?php
/**
 * Copyright (c) 2011, BigCommerce Pty. Ltd. <http://www.bigcommerce.com>
 * All rights reserved.
 * 
 * This library is free software; refer to the terms in the LICENSE file found
 * with this source code for details about modification and redistribution.
 */

require_once __DIR__ . '/HttpTestCase.php';

class RequestTest extends HttpTestCase {

	public function testGetRequest()
	{
		$url = self::HOST.'/basic/get';

		$request = new Net_Http_Request('get', $url);

		$this->assertEquals($request->getUrl(), $url);
		$this->assertEquals($request->getMethod(), 'GET');
	}

	public function testPostRequest()
	{
		$url = self::HOST.'/basic/post';
		$vars = array("greeting"=>"Hello", "from"=>"Net_Http_Request");

		$request = new Net_Http_Request('post', $url);
		$request->setParameters($vars);

		$this->assertEquals($request->getUrl(), $url);
		$this->assertEquals($request->getMethod(), 'POST');
	}

}