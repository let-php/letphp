<?php

namespace Lib;
class Http
{
	private static $_instance = null;
	
	public function __construct()
	{
		if(null === self::$_instance)
		{
			self::$_instance = \LetPHP_Http::getInstance();
		}
	}
	
	public function __call(string $sFunction, $aParams)
	{
		return call_user_func_array([self::$_instance, $sFunction], $aParams);
	}
}	
	
?>