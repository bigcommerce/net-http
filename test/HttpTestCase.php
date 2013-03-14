<?php
/**
 * Copyright (c) 2011, BigCommerce Pty. Ltd. <http://www.bigcommerce.com>
 * All rights reserved.
 * 
 * This library is free software; refer to the terms in the LICENSE file found
 * with this source code for details about modification and redistribution.
 */

require_once __DIR__ . '/../vendor/autoload.php';

abstract class HttpTestCase extends PHPUnit_Framework_TestCase
{
	/**
	 * This is a set of URL endpoints I set up a while ago for testing generic HTTP clients
	 */
	const HOST = 'http://conformity.sourceforge.net';
}