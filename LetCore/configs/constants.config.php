<?php	
/*
 * @author 	Rodrigo Ortiz
 * @package	letphp
 * @version constants.config.php .01 2020-04-08 Roni_Ortiz 
*/
defined('LETPHP')  or exit('NO LETPHP!');


## Constantes del Directorio de LetCore
define('LETPHP_LETCORE_CLASS', LETPHP_LETCORE.'class'.LETPHP_DS );
define('LETPHP_LETCORE_DIRS', LETPHP_LETCORE.'dirs'.LETPHP_DS );
define('LETPHP_LETCORE_DIRS_CACHE', LETPHP_LETCORE_DIRS.'cache'.LETPHP_DS );
define('LETPHP_LETCORE_CONFIGS', LETPHP_LETCORE. 'configs'. LETPHP_DS );
define('LETPHP_LETCORE_CLASS_LETPHP', LETPHP_LETCORE_CLASS.'letphp'.LETPHP_DS );

## Constantes del Directorio de LetApps
define('LETPHP_LETAPPS', LETPHP_DIR_PARENT. 'LetApps'. LETPHP_DS);
define('LETPHP_LETAPPS_CONTROLLERS', 'Controllers');
define('LETPHP_LETAPPS_FRAGMENTS', 'Fragments');
define('LETPHP_LETAPPS_MODELS', 'Models');
define('LETPHP_LETAPPS_AJAX', 'Ajax');
define('LETPHP_OBJECT_CONTROLLER', '_Controller_');
define('LETPHP_OBJECT_FRAGMENT', '_Fragment_');
define('LETPHP_OBJECT_MODEL', '_Model_');
define('LETPHP_OBJECT_AJAX', '_Ajax_');
define('LETPHP_LETAPPS_VIEWS', 'Views'. LETPHP_DS);
define('LETPHP_LETAPPS_RESOURCES', 'Resources'. LETPHP_DS);

## Constantes de las Apps
define('LETPHP_APP_CORE', 'app');
define('LETPHP_APP_SUFFIX_CONTROLLER', 'controller.php');
define('LETPHP_APP_SUFFIX_VIEW', 'view.php');
define('LETPHP_APP_SUFFIX_FRAGMENT', 'fragment.php');
define('LETPHP_APP_SUFFIX_MODEL', 'model.php');
define('LETPHP_APP_SUFFIX_AJAX', 'ajax.php');
define('LETPHP_USER_ACCOUNT_ADMIN', 1);
define('LETPHP_USER_ACCOUNT_REGISTER', 2);
define('LETPHP_CACHE_SUFFIX', 'cache.php');
define('LETPHP_CONFIG_SUFFIX', 'config.php');
## Directorio LetSite
define('LETPHP_LETSITE', LETPHP_DIR_PARENT. 'LetSite'. LETPHP_DS );
define('LETPHP_LETSITE_UI', LETPHP_LETSITE.'ui'. LETPHP_DS );
define('LETPHP_LETSITE_PUBLIC', LETPHP_LETSITE.'public'. LETPHP_DS );
define('LETPHP_LETSITE_ENGINE', LETPHP_LETSITE_PUBLIC.'engine'. LETPHP_DS );
define('LETPHP_GET_METHOD', 'let');
define('LETPHP_INDEX_FILE', 'index.php');
?>