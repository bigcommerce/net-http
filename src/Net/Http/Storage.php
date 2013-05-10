<?php

class Net_Http_Storage
{
	private $directory;
	
	public function __construct($directory)
	{
		$this->directory = $directory;
	}
	
	public function read($hash)
	{
		$filename = $this->directory . '/' . $hash;
		return json_decode(file_get_contents($filename));
	}
	
	public function write($hash, $object)
	{
		$filename = $this->directory . '/' . $hash;
		file_put_contents($hash, json_encode($object));
	}
	
	public function delete($hash)
	{
		$filename = $this->directory . '/' . $hash;
		return unlink($filename);
	}
}