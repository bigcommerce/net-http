<?php

class Net_Http_Recorder
{
	public function __construct($directory)
	{
		$this->storage = new Net_Http_Storage($directory);
	}
	
	public function get($request)
	{
		
	}
}