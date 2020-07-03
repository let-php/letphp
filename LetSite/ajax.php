<?php



// Comparamos Versiones
if (version_compare(phpversion(), '7.1', '<') === true) 
{
  exit('LetPHP requiere una version mayor a 7.1 para funcionar.');
}

// Activamos el Buffer de Salida
ob_start();
define('LETPHP_DIR_PARENT', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
require('../LetCore/start.php');


/*if(isset($_GET['js_image']))
{
	
	$oJavascript = LetPHP::getClass('letphp.javascript');
	$oJavascript->start();
	echo $oJavascript->getCode();
	//$oJavascript->let_html('#message', 'muy bien');
	//$aImage = $oJavascript->getParamArray('image');
	
	//echo $aImage['name'];
	
}
else
{*/
$oJavascript =  LetPHP_Javascript::getInstance();
$oJavascript->start();
echo $oJavascript->getCode();
//}
ob_end_flush();
?>
