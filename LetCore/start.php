<?php
	
declare(strict_types = 1);

// Comparamos Versiones
if (version_compare(phpversion(), '7.1', '<') === true) 
{
  exit('LetPHP requiere una version mayor a 7.1 para funcionar.');
}

// Activamos el Buffer de Salida
ob_start();
if(!defined('LETPHP'))
{
  define('LETPHP', true);
  define('LETPHP_DS', DIRECTORY_SEPARATOR);
  define('LETPHP_LETCORE', dirname(__FILE__) . LETPHP_DS);

  if(function_exists('date_default_timezone_set'))
  {
    date_default_timezone_set('America/Mexico_City');
    define('LETPHP_TIME', time());
  }
  else 
  {
    define('LETPHP_TIME', strtotime(gmdate("d M Y H:i:s", time())));
  }
}

defined('LETPHP') or exit('NO LETPHP');
@ini_set('memory_limit', '-1');
@ini_set('default_charset', "UTF-8");
@set_time_limit(0);
header('Content-type: text/html; charset=utf-8');
if(file_exists(dirname(dirname(__FILE__)). LETPHP_DS. 'LetSite'. LETPHP_DS. 'Libs'. LETPHP_DS. 'Start.php'))
{
	require(dirname(dirname(__FILE__)). LETPHP_DS. 'LetSite'. LETPHP_DS. 'Libs'. LETPHP_DS. 'Start.php');
	\Libs\Start::Run()->LoadClasses(dirname(dirname(__FILE__)). LETPHP_DS. 'LetCore')->LoadFunctions();
	//echo Config('main.site_title');
	error_reporting((Config('main.debug') > 0)? E_ALL: 0);
}


?>