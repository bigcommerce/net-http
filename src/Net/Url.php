<?php

class Net_Url
{
	private $_raw;
	private $_parsed;
	private $_segments;
	private $_parameters;
	private $_resource;
	private $_identity;
	private $_extension;
	private $_query;
	private $_aspect;

	public function __construct($path) {
		$this->_raw = trim($path);
		$this->_segments = array();
		$this->_parameters = array();
		$this->parse();
	}
	
	private function parse()
	{
		$this->_parsed = parse_url($this->_raw);
		if (strlen($this->_raw) > 1) {
			if (isset($this->_parsed['query'])) {
				parse_str(strip_tags($this->_parsed['query']), $parameters);
				$this->_parameters = $parameters;
			}
			$path = $this->explodeSegmentPath($this->_parsed['path']);
			$this->addResource(array_pop($path));
			while ($segment = array_pop($path)) {
				$this->_segments[] = urldecode($segment);
			}
			$this->_segments = array_reverse($this->_segments);
		}
	}
	
	private function explodeSegmentPath($path)
	{
		if (substr($path, -1) == '/') $path = substr($path, 0, -1);
		return explode('/', substr($path, 1));
	}
	
	private function addResource($resource)
	{
		if (strstr($resource, ";")) {
			$aspect = explode(";", $resource);
			$this->_aspect = $aspect[1];
			$resource = $aspect[0];
		}
		if (strstr($resource, ".")) {
			$identity = explode(".", $resource);
			$this->_identity = $identity[0];
			$this->_extension = $identity[1];
		}
		$this->_resource = urldecode($resource);
		$this->_segments[] = urldecode($resource);
	}
	
	/**
	* <p>Gives the resource name part.</p>
	* <p>Eg: <code>resource.ext#fragment</code></p>
	*/
	public function resource()
	{
		if (isset($this->_parsed['fragment'])) {
			return $this->_resource . "#" . $this->_parsed['fragment'];
		} else {
			return $this->_resource;
		}
	}
	
	/**
	* <p>Gives the fragment identifier.</p>
	* <p>Eg: <code>#fragment</code></p>
	*/
	public function fragment()
	{
		if (isset($this->_parsed['fragment'])) {
			return $this->_parsed['fragment'];
		}
	}
	
	/**
	* <p>Gives the identity of the requested resource.</p>
	*/
	public function identity()
	{
		if (isset($this->_identity)) {
			return $this->_identity;
		} else {
			return $this->_resource;
		}
	}
	
	/**
	* Gives the file extension part of the requested resource.
	*/
	public function ext()
	{
		if (isset($this->_extension)) {
			return $this->_extension;
		}
	}
	
	/**
	 * Alias for ext.
	 */
	public function extension()
	{
		return $this->ext();
	}
	
	/**
	 * @todo implement multiple schemes
	 */
	public function scheme()
	{
		return 'http';
	}
	
	/**
	 * Returns the HTTP host of this uri.
	 */
	public function host()
	{
		return $_SERVER['HTTP_HOST'];
	}
	
	public function path()
	{
		return $this->_parsed['path'];
	}
	
	public function isEmpty()
	{
		return (strlen($this->_resource) == 0);
	}
	
	/**
	 * Returns the raw querystring.
	 */
	public function query()
	{
		if (isset($this->_parsed['query'])) {
			return $this->_parsed['query'];
		}
	}
	
	/**
	 * Returns a hash of parameters from the querystring.
	 */
	public function parameters()
	{
		return $this->_parameters;
	}
	
	/**
	 * Returns the value of given parameter.
	 */
	public function parameter($key)
	{
		if (isset($this->_parametes[$key])) return $this->_parameters[$key];
	}
	
	/**
	 * Returns the given segment of the URL, starting from 1.
	 * 
	 * Eg: /content/topic/id gives:
	 *    $uri->segment(1) => content
	 *    $uri->segment(2) => topic
	 * 	  $uri->segment(3) => id
	 */
	public function segment($index)
	{
		if (isset($this->_segments[$index])) return $this->_segments[$index];
	}
	
	/**
	 * Returns the base segment of the URI path.
	 * 
	 * Eg: /content/topic/id gives:
	 * 	  $uri->baseSegment => content
	 */
	public function baseSegment()
	{
		if (isset($this->_segments[0])) return $this->_segments[0];
	}
	
	/**
	 * Returns a hash of path segments
	 */
	public function segments()
	{
		return $this->_segments;
	}
	
	/**
	 * Returns number of segments in this url path.
	 */
	public function segmentsCount()
	{
		return count($this->_segments);
	}
	
	/**
	 * Returns array of segments appearing after a given index.
	 * 
	 * Eg: /content/topic/id gives:
	 * 	  $uri->segmentsFrom(0) => array("content", "topic", "id")
	 */
	public function segmentsFrom($index)
	{
		return array_slice($this->_segments, $index);
	}

	/**
	* Gives the aspect string of the requested URI
	* Eg: <code>/path/to/resource;aspect</code> returns <code>aspect</code>
	*/
	public function aspect()
	{
		if (isset($this->_aspect)) return $this->_aspect;
	}
	
	/**
	 * Returns the full url.
	 */
	public function url()
	{
		return $this->host() . $this->_raw;
	}
	
}
