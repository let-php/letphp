<?php	
/**
	*
	*
	* @package LetPHP
	* @author Rodrigo Hernández Ortiz
	* 
	*/

/**
	* Instancia Class App
	*
	* @return Object \Lib\App 
	*/
function App()
{
	return new \Lib\App();
}

/**
	* Instancia para llamar a los Modelos
	* @param string $sModel Nombre del Modelo.
	* @param array $aParams Parametros a pasar al Modelo.
	* @return object
	*
	*/
function Model(string $sModel, array $aParams = [])
{
	return App()->getApp($sModel, $aParams, 'Models');
}


/**
	* Instancia para llamar a los Fragmentos
	* @param string $sFragment Nombre del Fragmento.
	* @param array $aParams Parametros a pasar al Fragmento.
	* @return object
	*
	*/
function Fragment(string $sFragment, array $aParams = [], bool $bValuesView = false)
{
	return App()->getApp($sFragment, $aParams, 'Fragments', $bValuesView);
}

	
/**
	* Instancia Class Auth
	*
	* @return Object \Lib\Auth 
	*/
function Auth()
{
	return new \Lib\Auth();
}
	
/**
	* Instancia Class Cache
	*
	* @return Object \Lib\Cache 
	*/
function Cache()
{
	return new \Lib\Cache();
}
	
/**
	* Instancia Class Config
	* @param mixed $config 
	* @return String Valor de la configuración.
	*/
function Config($config)
{
	$oConfig = new \Lib\Config();
	return $oConfig->getConfig($config);
}


/**
	* Instancia Class Auth
	* @param mixed $config
	* @param string $valur
	* @return Void 
	*/
function SetConfig($config, string $value)
{
	$oConfig = new \Lib\Config();
	$oConfig->setConfig($config, $value);
}
	
/**
	* Instancia Class Database
	*
	* @return Object \Lib\Database 
	*/
function Db()
{
	return new \Lib\Database();
}
		
/**
	* Obtenemos la tabla con su prefijo.
	* @param string $sTable Nombre de la tabla( sin prefijo).
	* @return string $sTable;
	*/
function DbTable(string $sTable = '')
{
	$sTable = Config(['database', 'prefix']).$sTable;
	return $sTable;
}
	
/**
	* Función para la consulta SQL INSERT.
	*/
function DbInsert(string $sTable, array $aParams)
{
	$oDb = Db();
	return $oDb->insert(DbTable($sTable), $aParams);
}
	
/**
	* Función para la consulta SQL UPDATE.
	*/
function DbUpdate(string $sTable, array $aParams, string $sCondition)
{
	$oDb = Db();
	return $oDb->update(DbTable($sTable), $aParams, $sCondition);
}
	
/**
	* Función para la consulta SQL DELETE.
	*/
function DbDelete(string $sTable, string $sCondition)
{		
	$oDb = Db();
	return $oDb->delete(DbTable($sTable), $sCondition);	
}
	
/**
	* Instancia Class Filter_Input
	*
	* @return Object \Lib\Input 
	*/
function Input()
{
	return new \Lib\Input();
}
	
/**
	* Instancia Class Filter_Output
	*
	* @return Object \Lib\Output 
	*/
function Output()
{
	return new \Lib\Output();
}

/**
	* Instancia Class Http
	*
	* @return Object \Lib\Http 
	*/
function Http()
{
	return new \Lib\Http();
}
	
/**
	* Instancia Class Javascript
	*
	* @return Object \Lib\Javascript 
	*/
function JS()
{
	return new \Lib\Javascript();
}

/**
	* Instancia Class Paginator
	*
	* @return Object \Lib\Paginator 
	*/
function Paginator()
{
	return new \Lib\Paginator();
}
	
/**
	* Instancia Class Router
	*
	* @return Object \Lib\Router
	*/
function Router()
{
	return new \Lib\Router();
}

/**
	* Instancia Class View
	*
	* @return Object \Lib\View 
	*/	
function View()
{
	return new \Lib\View();
}
		
function d($aParams = [])
{
	echo '<pre>';
	print_r($aParams);
	echo '</pre>';
}
		
function letPHPInitSession()
{	
	((!isset($_SESSION) )? session_start(['cookie_lifetime' => Config('main.session_lifetime')])	:null);
}
letPHPInitSession();
?>	