<?php
namespace Libs;
class Start
{
	private static $self = null;
	
	public function LoadClasses(string $sDir = '')
	{
		// Vendor Compose
		if(file_exists($sDir. LETPHP_DS. "vendor". LETPHP_DS. "autoload.php"))
		{
			require_once($sDir. LETPHP_DS. "vendor". LETPHP_DS. "autoload.php");
		}
		require($sDir. LETPHP_DS. 'configs'. LETPHP_DS. 'constants.config.php');
		return $this;
	}
	
	public function LoadFunctions()
	{
		if(file_exists(LETPHP_LETSITE. 'Libs'. LETPHP_DS. 'Functions.php'))
		{
			require(LETPHP_LETSITE. 'Libs'. LETPHP_DS. 'Functions.php');
		}
	}
	
	
	public static function Run()
	{
		if(!self::$self)
		{
			self::$self = new self();
		}
		return self::$self;
	}
} 

?>