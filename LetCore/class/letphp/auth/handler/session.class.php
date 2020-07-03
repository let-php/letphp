<?php
/** Class LetPHP_Auth_Session */
defined('LETPHP') or exit('NO EXISTE LETPHP');	

/**
 * Administramos las  Sesiones de nuestro sitio
 * 
 * 
 * @copyright LetCode IO
 * @author Rodrigo Hernández Ortiz
 * @package LetPHP\Session
 * @version .1
 */
class LetPHP_Auth_Handler_Session
{
	
	/**
	 * Prefijo de la session.
		* @var string $_sSessionPrefix
	*/
	private $_sSessionPrefix = '';


	/**
	 * Cargamos el prefijo de la sesión.
	 */
	public function __construct()
	{
		$this->_sSessionPrefix = LetPHP::getConfig('main.session_prefix');
  }

	/**
		* Iniciamos una sesión.
		* @param string $sName Nombre de la Sesion a iniciar.
		* @param string $sValue Valor de la sesion.
		* @return void
	*/
	public function setSession(string $sName, string $sValue)
	{
		$_SESSION[$this->_sSessionPrefix][$sName] = $sValue;
	}

	/**
		* Obtenemos la session que hemos iniciado con la
		* function $this->setSession()
		* @see LetPHP_Session::setSession()
		* @param string $sName Nombre de la Sesion a Obtener
		* @return mixed bool|string
	*/
	public function getSession(string $sName)
	{
		if (isset($_SESSION[$this->_sSessionPrefix][$sName]))
		{
			return (empty($_SESSION[$this->_sSessionPrefix][$sName]) ? true : $_SESSION[$this->_sSessionPrefix][$sName]);
		}
		return false;
	}

	
	/**
		* Terminamos la sesión que hemos iniciado.
		* @param string $sName Nombre de la Sesion a terminar
		* @return void
	*/
	public function removeSession(string $sName)
	{
		if (!is_array($sName))
		{
			$sName = array($sName);
		}

		foreach ($sName as $sValName)
		{
			unset($_SESSION[$this->_sSessionPrefix][$sValName]);
		}
	}


	/**
		* Iniciamos una sesion como un arreglo
		* @param string $sName Nombre de la Sesion a iniciar.
		* @param string $sValue Valor de la sesion.
		* @param string $sActualValue valor de la sesion.
		* @return void 
	*/
	public function setArray(string $sName, string $sValue, string $sActualValue)
	{
		$_SESSION[$this->_sSessionPrefix][$sName]['param_' . $sValue] = $sActualValue;
	}

	/**
	 * Obtenemos un sesión, pero como un arreglo.
	 * @param string $sName Nombre de la sesión.
	 * @param string $sValue Valor de la sesión.
	 * 
	 * @return mixed bool|string
	 */
	public function getArray(string $sName, string $sValue)
	{
		if (isset($_SESSION[$this->_sSessionPrefix][$sName]['param_' . $sValue]))
		{
			return $_SESSION[$this->_sSessionPrefix][$sName]['param_' . $sValue];
		}

		return false;
	}

	
}
	
?>