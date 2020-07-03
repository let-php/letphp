<?php
defined('LETPHP') or exit('NO LETPHP!');

interface LetPHP_Database_Interface
{
	
	/*
		* Hacemos una conexión a la Base de Datos
		* @param string $sHost Host del Servidor
		* @param string $sUser Nombre del Usuario de la Base de Datos
		* @param string $sPassword Contraseña del Usuario de la Base de Datos
	*/
  public function connect(string $sHost, string $sUser, string $sPassword, string $sName,  $sPort = false, bool $bPersitent = false);
  
  
  public function query(string $sSql, &$hLink = '');
}

?>