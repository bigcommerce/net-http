<?php
/**
 * Copyright (c) 2011, BigCommerce Pty. Ltd. <http://www.bigcommerce.com>
 * All rights reserved.
 * 
 * This library is free software; refer to the terms in the LICENSE file found
 * with this source code for details about modification and redistribution.
 */

/**
 * Client for interacting with the HTTP protocol.
 *
 * Provides a basic interface to the PHP curl extension and supports
 * all the most common HTTP verbs: head, get, post, put, and delete.
 *
 * Making a basic GET request:
 *
 *  <code>
 *     $client = new Net_Http_Client();
 *     $client->get("http://bigcommerce.com/");
 *     $body = $client->getBody();
 *     $contentType = $client->getHeader("Content-Type");
 *  </code>
 *
 * Posting to a remote endpoint:
 *
 *  <code>
 *     $client = new Net_Http_Client();
 *     $client->post("http://bigcommerce.com/about", array("key"=>"value"));
 *     $responseCode = $client->getStatus();
 *     if ($responseCode != 200) {
 *         // the request returned an error response
 *     }
 *  </code>
 *
 * @todo return Net_Http_Client_Http_Response object from request methods instead of self-reference
 */
class Net_Http_Client {

	/**
	 * @var cURL resource handle
	 */
	private $curl;

	/**
	 * @todo wrap this in Http_Request object
	 * @var hash of HTTP request headers
	 */
	private $headers;

	/**
	 * @todo wrap this in Http_Response object
	 * @var hash of headers from HTTP response
	 */
	private $responseHeaders;

	/**
	 * The status line of the response.
	 * @var string
	 */
	private $responseStatusLine;

	/**
	 * @todo wrap this in Http_Response object
	 * @var hash of headers from HTTP response
	 */
	private $responseBody;

	/**
	 * @var boolean
	 */
	private $failOnError;

	/**
	 * Manually follow location redirects. Used if CURLOPT_FOLLOWLOCATION
	 * is unavailable due to open_basedir restriction.
	 * @var boolean
	 */
	private $followLocation = false;

	/**
	 * Maximum number of redirects to try.
	 * @var int
	 */
	private $maxRedirects = 20;

	/**
	 * Number of redirects followed in a loop.
	 * @var int
	 */
	private $redirectsFollowed = 0;

	/**
	 * Deal with failed requests if failOnError is not set.
	 * @var boolean
	 */
	private $hasError;

	/**
	 * Current cURL error code.
	 */
	private $errorCode;

	/**
	 * Initializes the cURL resource handle.
	 */
	public function __construct()
	{
		$this->curl = curl_init();
		$this->headers = array();

		curl_setopt($this->curl, CURLOPT_HEADERFUNCTION, array($this, 'parseHeader'));
		curl_setopt($this->curl, CURLOPT_WRITEFUNCTION, array($this, 'parseBody'));

		if (!ini_get("open_basedir")) {
			curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
		} else {
			$this->followLocation = true;
		}

		if ($options = Net_Http::getOptions()) {
			curl_setopt_array($this->curl, $options);
		}
	}

	/**
	 * Throw an exception where the request encounters an HTTP error condition.
	 *
	 * <p>An error condition is considered to be:</p>
	 *
	 * <ul>
	 * 	<li>400-499 - Client error</li>
	 *	<li>500-599 - Server error</li>
	 * </ul>
	 *
	 * <p><em>Note that this doesn't use the builtin CURL_FAILONERROR option,
	 * as this fails fast, making the HTTP body and headers inaccessible.</em></p>
	 *
	 * @param boolean $option
	 */
	public function failOnError($option = true)
	{
		$this->failOnError = $option;
	}

	/**
	 * HTTP basic authentication.
	 *
	 * @param string $username
	 * @param string $password
	 */
	public function setBasicAuth($username, $password)
	{
		curl_setopt($this->curl, CURLOPT_USERPWD, "$username:$password");
	}

	/**
	 * Set a default timeout for the request. The client will error if the
	 * request takes longer than this to respond.
	 *
	 * @param int $timeout number of seconds to wait on a response
	 */
	public function setTimeout($timeout)
	{
		curl_setopt($this->curl, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, $timeout);
	}

	/**
	 * Push outgoing requests through the specified proxy server.
	 *
	 * @param string $host
	 * @param int $port
	 */
	public function setProxy($host, $port=false)
	{
		curl_setopt($this->curl, CURLOPT_PROXY, $host);

		if ($port) {
			curl_setopt($this->curl, CURLOPT_PROXYPORT, $port);
		}
	}

	/**
	 * @todo may need to handle CURLOPT_SSL_VERIFYHOST and CURLOPT_CAINFO as well
	 * @todo need to test against SSL servers
	 * @param boolean
	 */
	public function setVerifyPeer($peer=false)
	{
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
	}

	/**
	 * Provide a local certificate to use when verifying an SSL connection.
	 *
	 * @param $path full path to the local PEM file
	 * @param $password optional password required to open the PEM file
	 */
	public function setSslCertificate($path, $password=false)
	{
		curl_setopt($this->curl, CURLOPT_SSLCERT, $path);

		if ($password) {
			curl_setopt($this->curl, CURLOPT_SSLCERTPASSWD, $password);
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
	public function setSslVersion($version)
	{
		curl_setopt($this->curl, CURLOPT_SSLVERSION, $version);
	}

	/**
	 * Assign the entire set of request header lines from given array.
	 *
	 * @param array $headers
	 */
	public function setHeaders($headers)
	{
		foreach($headers as $header => $value) {
			$this->setHeader($header, $value);
		}
	}

	/**
	 * Set a request header.
	 *
	 * @param string $header name of the HTTP header
	 * @param string $value value of the HTTP header
	 */
	public function setHeader($header, $value)
	{
		$this->headers[$header] = "$header: $value";
	}

	/**
	 * Assign a custom user agent to the request.
	 *
	 * @param string $value HTTP user agent
	 */
	public function setUserAgent($value)
	{
		$this->setHeader('User-Agent', $value);
	}

	/**
	 * Clear previously cached request data and prepare for
	 * making a fresh request.
	 */
	private function initializeRequest()
	{
		$this->isComplete = false;
		$this->responseBody = "";
		$this->responseHeaders = array();
		curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headers);
	}

	/**
	 * Check the response for possible errors. If failOnError is true
	 * then throw a protocol level exception. Network errors are always
	 * raised as exceptions.
	 *
	 * @throws Net_Http_ClientError
	 * @throws Net_Http_NetworkError
	 * @throws Net_Http_ServerError
	 */
	private function checkResponse()
	{
		if (curl_errno($this->curl)) {
			throw new Net_Http_NetworkError(curl_error($this->curl), curl_errno($this->curl));
		}
		if ($this->failOnError) {
			$status = $this->getStatus();
			if ($status >= 400 && $status <= 499) {
				throw new Net_Http_ClientError($this->getStatusMessage(), $status);
			} elseif ($status >= 500 && $status <= 599) {
				throw new Net_Http_ServerError($this->getStatusMessage(), $status);
			}
		}
		if ($this->followLocation) {
			$this->followRedirectPath();
		}
	}

	/**
	 * Recursively follow redirect until an OK response is recieved or
	 * the maximum redirects limit is reached.
	 *
	 * Only 301 and 302 redirects are handled. Redirects from POST and PUT requests will
	 * be converted into GET requests, as per the HTTP spec.
	 */
	private function followRedirectPath()
	{
		$this->redirectsFollowed++;

		if ($this->getStatus() == 301 || $this->getStatus() == 302) {

			if ($this->redirectsFollowed < $this->maxRedirects) {

				$location = $this->getHeader('Location');
				$forwardTo = parse_url($location);

				if (isset($forwardTo['scheme']) && isset($forwardTo['host'])) {
					$url = $location;
				} else {
					$forwardFrom = parse_url(curl_getinfo($this->curl, CURLINFO_EFFECTIVE_URL));
					$url = $forwardFrom['scheme'] . '://' . $forwardFrom['host'] . $location;
				}

				$this->get($url);

			} else {
				$errorString = "Too many redirects when trying to follow location.";
				throw new Net_Http_NetworkError($errorString, CURLE_TOO_MANY_REDIRECTS);
			}
		} else {
			$this->redirectsFollowed = 0;
		}
	}

	/**
	 * Make an HTTP GET request to the specified endpoint.
	 *
	 * @param string $uri URI address to request
	 * @param mixed $query querystring parameters
	 */
	public function get($uri, $query=false)
	{
		$this->initializeRequest();

		if (is_array($query)) {
			$uri .= "?" . http_build_query($query);
		} elseif ($query) {
			$uri .= "?" . $query;
		}

		curl_setopt($this->curl, CURLOPT_URL, $uri);
		curl_setopt($this->curl, CURLOPT_HTTPGET, true);
		curl_exec($this->curl);

		$this->checkResponse();

		return $this;
	}

	/**
	 * Make an HTTP POST request to the specified endpoint.
	 *
	 * @param string $uri URI address to request
	 * @param mixed $query querystring parameters
	 */
	public function post($uri, $data)
	{
		$this->initializeRequest();

		if (is_array($data)) {
			$data = http_build_query($data);
		}

		curl_setopt($this->curl, CURLOPT_URL, $uri);
		curl_setopt($this->curl, CURLOPT_POST, true);
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
		curl_exec($this->curl);

		$this->checkResponse();

		return $this;
	}

	/**
	 * Make an HTTP HEAD request to the specified endpoint.
	 *
	 * @param string $uri URI address to request
	 */
	public function head($uri)
	{
		$this->initializeRequest();

		curl_setopt($this->curl, CURLOPT_URL, $uri);
		curl_setopt($this->curl, CURLOPT_NOBODY, true);
		curl_exec($this->curl);

		$this->checkResponse();

		return $this;
	}

	/**
	 * Make an HTTP PUT request to the specified endpoint.
	 *
	 * Requires a tmpfile() handle to be opened on the system, as the cURL
	 * API requires it to send data.
	 *
	 * @param string $uri URI address to request
	 * @param string $data data of the request body
	 */
	public function put($uri, $data)
	{
		$this->initializeRequest();

		$handle = tmpfile();
		fwrite($handle, $data);
		fseek($handle, 0);
		curl_setopt($this->curl, CURLOPT_INFILE, $handle);
		curl_setopt($this->curl, CURLOPT_INFILESIZE, strlen($data));

		curl_setopt($this->curl, CURLOPT_URL, $uri);
		curl_setopt($this->curl, CURLOPT_PUT, true);
		curl_exec($this->curl);

		$this->checkResponse();

		return $this;
	}

	/**
	 * Make an HTTP DELETE request to the specified endpoint.
	 *
	 * @param string $uri URI address to request
	 */
	public function delete($uri)
	{
		$this->initializeRequest();

		curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
		curl_setopt($this->curl, CURLOPT_URL, $uri);
		curl_exec($this->curl);

		$this->checkResponse();

		return $this;
	}

	/**
	 * Callback method collects body content from the response.
	 */
	private function parseBody($curl, $body)
	{
		$this->responseBody .= $body;
		return strlen($body);
	}

	/**
	 * Callback methods collects header lines from the response.
	 */
	private function parseHeader($curl, $headers)
	{
		if (!$this->responseStatusLine && strpos($headers, 'HTTP/') === 0) {
			$this->responseStatusLine = $headers;
		} else {
			$parts = explode(': ', $headers);
			if (isset($parts[1])) {
				$this->responseHeaders[$parts[0]] = trim($parts[1]);
			}
		}
        return strlen($headers);
	}

	/**
	 * @return Net_Http_Response
	 */
	public function getResponse()
	{
		return new Net_Http_Response($this->getStatus(), $this->getHeaders(), $this->getBody());
	}

	/**
	 * Access the status code of the response.
	 *
	 * @return int
	 */
	public function getStatus()
	{
		return curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
	}

	/**
	 * Access the message string from the status line of the response.
	 *
	 * @return string
	 */
	public function getStatusMessage()
	{
		return $this->responseStatusLine;
	}

	/**
	 * Access the content body of the response
	 *
	 * @return string
	 */
	public function getBody()
	{
		return $this->responseBody;
	}

	/**
	 * Access value of given header from the response.
	 *
	 * @return string
	 */
	public function getHeader($header)
	{
		if (array_key_exists($header, $this->responseHeaders)) {
			return $this->responseHeaders[$header];
		}
	}

	/**
	 * Return the full list of response headers
	 *
	 * @return array
	 */
	public function getHeaders()
	{
		return $this->responseHeaders;
	}

	/**
	 * Close the cURL resource when the instance is garbage collected
	 */
	public function __destruct()
	{
		curl_close($this->curl);
	}
}