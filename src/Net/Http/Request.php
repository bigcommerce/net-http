<?php
/**
 * Copyright (c) 2011, BigCommerce Pty. Ltd. <http://www.bigcommerce.com>
 * All rights reserved.
 * 
 * This library is free software; refer to the terms in the LICENSE file found
 * with this source code for details about modification and redistribution.
 */

/**
 * HTTP request envelope.
 *
 * This class has changed from the original facade for making actual requests,
 * and is now just an object that represents the structure of a request itself,
 * separate from the transport.
 */
class Net_Http_Request
{
	private $url;
	private $method;
	private $parameters;
	private $headers;

	public function __construct($method='get', $url=null)
	{
		$this->url = $url;
		$this->method = $method;
		$this->parameters = false;
		$this->headers = array();
	}

	/**
	 * Set the request method verb.
	 */
	public function setMethod($method)
	{
		$this->method = $method;
	}

	/**
	 * Set the request URL.
	 */
	public function setUrl($url)
	{
		$this->url = $url;
	}

	/**
	 * Assign the entire set of header lines from given array.
	 */
	public function setHeaders($headers)
	{
		foreach($headers as $header => $value) {
			$this->setHeader($header, $value);
		}
	}

	/**
	 * Set a request header.
	 */
	public function setHeader($header, $value)
	{
		$this->headers[$header] = "$header: $value";
	}

	/**
	 * Assign a user agent string to the request.
	 */
	public function setUserAgent($value)
	{
		$this->setHeader('User-Agent', $value);
		return $this;
	}

	/**
	 * Assign the Content-Type of the request to the given MIME type.
	 */
	public function setMime($mime)
	{
		$this->setHeader('Content-Type', $mime);
		return $this;
	}

	/**
	 * Return the full list of given headers.
	 */
	public function getHeaders()
	{
		return $this->headers;
	}

	/**
	 * Access given header.
	 */
	public function getHeader($header)
	{
		if (array_key_exists($header, $this->headers)) {
			return $this->headers[$header];
		}
	}

	/**
	 * Assign a key-value parameter to the request.
	 *
	 * This will generally be sent
	 * as application/x-www-form-urlencoded data with a POST body or the
	 * querystring of a GET.
	 */
	public function setParameter($key, $value)
	{
		$this->parameters[$key] = $value;
		return $this;
	}

	/**
	 * Assign the entire array of key-value parameters to the request.
	 *
	 * This will generally be sent
	 * as application/x-www-form-urlencoded data with a POST body or the
	 * querystring of a GET.
	 */
	public function setParameters($parameters)
	{
		$this->parameters = $parameters;
		return $this;
	}

	public function getData()
	{
		return $this->parameters;
	}

	/**
	 * Set the entire body of a POST or PUT request.
	 * The provided MIME type must reflect the content format of the string.
	 */
	public function setBody($content, $mime)
	{
		$this->parameters = $content;
		$this->setMime($mime);
		return $this;
	}

	/**
	 * Return the HTTP method verb for this request.
	 */
	public function getMethod()
	{
		return strtoupper($this->method);
	}

	/**
	 * Return the URL being this request is being sent to.
	 */
	public function getUrl()
	{
		return $this->url;
	}
}