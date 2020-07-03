<?php

LetPHP::getFileClass('letphp.database.dml');
class LetPHP_Database
{
	/** @var  */
	private static $_oObject = null;
	
	public function __construct()
	{
		
		$sHandler = '';
		if(!self::$_oObject)
		{
			switch(LetPHP::getConfig(['database', 'handler']))
			{
				case 'mysqli': 
					$sHandler = 'letphp.database.handler.mysqli';
					break;
				case 'pdo': 
					$sHandler = 'letphp.database.handler.pdo';
					break;
				default: 
					$sHandler = 'letphp.database.handler.mysqli';
					break;
			}
			
			self::$_oObject = LetPHP::getClass($sHandler);
			$sHost = LetPHP::getConfig(['database', 'host']);
			$sUser = LetPHP::getConfig(['database', 'user']);
			$sPassword = LetPHP::getConfig(['database', 'password']);
			$sDatabase = LetPHP::getConfig(['database', 'database']);
			self::$_oObject->connect( $sHost, $sUser, $sPassword, $sDatabase);	 
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