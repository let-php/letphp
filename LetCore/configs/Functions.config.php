<?php	
/**
	*
	**/
function App()
{
	return new \Lib\App();
}
	
function Auth()
{
	return new \Lib\Auth();
}
	
function Cache()
{
	return new \Lib\Cache();
}
	
function Config($config)
{
	$oConfig = new \Lib\Config();
	return $oConfig->getConfig($config);
}
	
function Db()
{
	return new \Lib\Database();
}
		
/**
	* Obtenemos la tabla con su prefijo.
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
	
function Input()
{
	return new \Lib\Input();
}
	
function Output()
{
	return new \Lib\Output();
}
	
function Http()
{
	return new \Lib\Http();
}
	
function JS()
{
	return new \Lib\Javascript();
}
	
function Paginator()
{
	return new \Lib\Paginator();
}
	
function Router()
{
	return new \Lib\Router();
}
	
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