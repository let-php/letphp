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
	
	
	/**
		* Generamos un password de manera aleatoria
		* @param integer $iLength Tamaño del password.
		* @param integer $iStrength Fuerza en el password.
		* @return string $sPassword 
		*/
  public function generatePassword(int $iLength = 9, int $iStrength = 10)
  {
	  $sVowels = 'uoiea';
	  $sConsonants = 'bcdfgh';
	  $sConsonants .= 'tvwxyz';
	  $sConsonants .= 'jklmnopqrs';
	  
	  if($iStrength > 1)
	  {
		  $sConsonants .= "BCDFGHJKLMNPQRSTVWXYZ";
	  }
	  
	  if($iStrength > 2){
		  $sVowels .= "UEIOA";
	  }
	  
	  if($iStrength > 4){
		  $sConsonants .= "23456789";
	  }
	  
	  if($iStrength > 8){
		  $sConsonants .= "@#$%[]{}!?*;:";
	  }
	  
	  $sPassword = '';
	  $sAlt = LETPHP_TIME % 2;
	  for($i =0 ; $i < $iLength; $i++){
		  if($sAlt == 1){
			  $sPassword .= $sConsonants[(rand() % strlen($sConsonants))];
			  $sAlt = 0;
		  }else{
			  $sPassword .= $sVowels[(rand() % strlen($sVowels))];
			  $sAlt = 1;
		  }
	  }	  
	  return $sPassword;
  }

	/*
		* Encriptamos nuestras contraseñas con el algoritmo
		* password_hash(), más información en https://www.php.net/manual/function.password-hash.php
		* @param string $sPassword contraseña a encriptar 
		* @params integer $iCost 
		* @return string 
	*/
  public function encryptPassword(string $sPassword, int $iHashType = 1, int $iCost = 12): string
  {
	 $aHashTypes = [
		 CRYPT_BLOWFISH,
		 CRYPT_STD_DES,
		 CRYPT_EXT_DES,
		 CRYPT_MD5,
		 CRYPT_SHA256,
		 CRYPT_SHA512,
	 ];
	  
    $aOptions = [
      'cost'  => $iCost
    ];
    return password_hash($sPassword, $aHashTypes[$iHashType - 1], $aOptions); 
  }
  
  /*
		* Verificamos la contraseña ingresada y
		* la almacenada en la base de datos
		* password_verify(), más información en https://www.php.net/manual/function.password-verify.php
		* @param string $sPassword contraseña ingresada en el formulario 
		* @param string $sPasswordDatabase contraseña guardada en la base de datos 
		* @return bool 
	*/
  public function verifyPassword(string $sPassword , string $sPasswordDatabase): bool
  {
    return password_verify($sPassword, $sPasswordDatabase);
  }


	
}
	
?>