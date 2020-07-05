<?php
/**
	* Configuraciones globales del Sitio.
	* @author Rodrigo Hernández Ortiz.
	* @package LetPHP
	* @version .1
	*
	*/
defined('LETPHP') or exit('NO EXISTE LETPHP');

$bIsHTTPS = false;
if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'){
    $bIsHTTPS= true;
}elseif(isset($_SERVER['SERVER_PORT']) and $_SERVER['SERVER_PORT'] == 443){
    $bIsHTTPS = true;
}elseif(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) and $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'){
    $bIsHTTPS = true;
}elseif(isset($_SERVER['HTTP_CF_VISITOR']) and strpos($_SERVER['HTTP_CF_VISITOR'],'https')){
    $bIsHTTPS = true;
}
defined('LETPHP_IS_HTTPS') or define('LETPHP_IS_HTTPS', $bIsHTTPS);

## Host
$_CONFIG['main.host'] = $_SERVER['HTTP_HOST'];
$_CONFIG['main.folder'] =  LETPHP_DS;
//$_CONFIG['main.path'] = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ) ? 'https' : 'http') . '://' . $_CONFIG['main.host'] . $_CONFIG['main.folder'];
$_CONFIG['main.path'] = ((LETPHP_IS_HTTPS) ? 'https': 'http' ). '://'. $_CONFIG['main.host']. $_CONFIG['main.folder'];;
$_CONFIG['main.version'] = '0.1';
$_CONFIG['main.installed'] = false;
$_CONFIG['main.debug'] = 0;
$_CONFIG['main.route_rewrite'] = 2; 
## 1 = www.letphp.run/letphp/letcode/ 
## 2 = www.letphp.run/index.php?let=letcode 

## Configuración de Sesiones
$_CONFIG['main.session_prefix'] = 'letphp';
$_CONFIG['main.session_lifetime'] = 86400 * 2; 
## Son los segundos de 1 día

## Configuracion del Sitio
$_CONFIG['main.site_name'] = "LetPHP";
$_CONFIG['main.site_title'] = "LetPHP Framework";
$_CONFIG['main.site_copyright'] = "LetPHP Framework creado por LetCode©";
$_CONFIG['main.site_title_delimiter'] = "»";
$_CONFIG['main.site_keywords'] = "{site_name}, framework desarrollado con PHP.";
$_CONFIG['main.site_description'] = "{site_name}, es un framework desarrollado con PHP, para hacer más sencillo y fácil el desarrollo de tus proyectos web.";
$_CONFIG['main.site_theme'] = "";
$_CONFIG['main.token'] = "1c5f1832d1422748a869efc3466baf74f44e1bb8";
$_CONFIG['main.site_secure'] = 2;
$_CONFIG['main.app_core'] = "app";
    
?>
