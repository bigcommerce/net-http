<?php
/**
 * Copyright (c) 2011, BigCommerce Pty. Ltd. <http://www.bigcommerce.com>
 * All rights reserved.
 * 
 * This library is free software; refer to the terms in the LICENSE file found
 * with this source code for details about modification and redistribution.
 */

/**
 * Any general HTTP protocol error.
 *
 * See {@link Net_Http_ClientError} and {@link Net_Http_ServerError}
 */
abstract class Net_Http_ProtocolError extends Net_Http_Exception
{
	/**
	 * @var Net_Http_Response
	 */
	private $response;
	
	/**
	 * @param string $message
	 * @param int $status
	 * @param Net_Http_Response $response
	 */	
	public function __construct($message, $status, $response)
	{
		$this->response = $response;
		parent::__construct($message, $status);
	}
	
	/**
	 * @return int
	 */
	public function getStatus()
	{
		return $this->getCode();
	}
	
	/**
	 * @return Net_Http_Response
	 */
	public function getResponse()
	{
		return $this->response;
	}
	
	/**
	 * @return string
	 */
	public function getBody()
	{
		return $this->response->getBody();
	}
}