<?php
defined('LETPHP') or exit('NO LETPHP');

class LetPHP_Auth
{
	
	/*
		* Cargamos el Objeto de la clase
		* @var object $_oObject 
	*/
	private static $_oObject = null;
	
	public function __construct()
	{
		if(!self::$_oObject)
		{		
			$sHandler = "letphp.auth.handler.session";
			self::$_oObject = LetPHP::getClass($sHandler);
		}
	}
	
	public function &getObject()
	{
		return self::$_oObject;
	}
	
	public static function getInstance()
	{
		if(!self::$_oObject)
		{
			new self();
		}
		return self::$_oObject;
	}
}
	
?>