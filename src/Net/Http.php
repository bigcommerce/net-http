<?php
/**
 * Copyright (c) 2011, BigCommerce Pty. Ltd. <http://www.bigcommerce.com>
 * All rights reserved.
 * 
 * This library is free software; refer to the terms in the LICENSE file found
 * with this source code for details about modification and redistribution.
 */


/**
 * HTTP client wrapper.
 *
 * Options set here will apply globally to all HTTP requests.
 */
class Net_Http {

	/**
	 * @var array
	 */
	private static $options = array();

	/**
	 * Returns the list of configured options.
	 *
	 * @return array
	 */
	public static function getOptions()
	{
		return self::$options;
	}

	/**
	 * Clears all globally configured options.
	 */
	public static function clearOptions()
	{
		self::$options = array();
	}

	/**
	 * HTTP basic authentication.
	 */
	public static function setBasicAuth($username, $password)
	{
		self::$options[CURLOPT_USERPWD] = "$username:$password";
	}

	/**
	 * Set a default timeout for the request. The client will error if the
	 * request takes longer than this to respond.
	 *
	 * @param int $timeout number of seconds to wait on a response
	 */
	public static function setTimeout($timeout)
	{
		self::$options[CURLOPT_TIMEOUT] = $timeout;
		self::$options[CURLOPT_CONNECTTIMEOUT] = $timeout;
	}

	/**
	 * Set a proxy server for outgoing requests to tunnel through.
	 *
	 * @param string $server
	 * @param int $port
	 */
	public static function setProxy($server, $port=false)
	{
		self::$options[CURLOPT_PROXY] = $server;

		if ($port) {
			self::$options[CURLOPT_PROXYPORT] = $port;
		}
	}

	/**
	 * @todo may need to handle CURLOPT_SSL_VERIFYHOST and CURLOPT_CAINFO as well
	 * @todo need to test against SSL servers
	 * @param boolean
	 */
	public static function setVerifyPeer($peer=false)
	{
		self::$options[CURLOPT_SSL_VERIFYPEER] = $peer;
	}

	/**
	 * Provide a local certificate to use when verifying an SSL connection.
	 *
	 * @param $path full path to the local PEM file
	 * @param $password optional password required to open the PEM file
	 */
	public static function setSslCertificate($path, $password=false)
	{
		self::$options[CURLOPT_SSLCERT] = $path;

		if ($password) {
			self::$options[CURLOPT_SSLCERTPASSWD] = $password;
		}
	}

	/**
	 * The SSL version to use (2 or 3).
	 *
	 * PHP will try to determine this version automatically by default. Use this
	 * method in the case that the value must be set manually.
	 *
	 * @param int $version 2 or 3
	 */
	public static function setSslVersion($version)
	{
		self::$options[CURLOPT_SSLVERSION] = $path;
	}
}