<?php
/**
 * Copyright (c) 2011, BigCommerce Pty. Ltd. <http://www.bigcommerce.com>
 * All rights reserved.
 * 
 * This library is free software; refer to the terms in the LICENSE file found
 * with this source code for details about modification and redistribution.
 */

Autoloader::addPath(__DIR__);
Autoloader::addPath(__DIR__.'/Tests');
Autoloader::register();

class Autoloader
{
	private static $loadPaths = array();

	public static function addPath($path)
	{
		self::$loadPaths[] = $path;
	}

	public static function register()
	{
		spl_autoload_register(array('Autoloader', 'autoload'));
	}

	public static function autoload($className)
	{
		$fileName = str_replace("\\", "/", $className);
		$fileName = str_replace('_', '/', $fileName).'.php';
		foreach(self::$loadPaths as $path) {
			$classPath = $path.'/'.$fileName;
			if(is_file($classPath)) {
				require_once $classPath;
				return true;
			}
		}

		@include_once $fileName;
		return class_exists($className, false);
	}
}