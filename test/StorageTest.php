<?php

require_once __DIR__ . '/../vendor/autoload.php';

class StorageTest extends PHPUnit_Framework_TestCase
{
	public function testCanReadBackFromObjectWrite()
	{
		$storage = new Net_Http_Storage(__DIR__);
		
		$object = (object)array(
			"id" => 1,
			"name" => __METHOD__,
		);
		
		$checksum = md5(__METHOD__);
		
		$storage->write($checksum, $object);

		$this->assertEquals($object, $storage->read($checksum));
		
		$this->assertTrue($storage->delete($checksum));
	}
}