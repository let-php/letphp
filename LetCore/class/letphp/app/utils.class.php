<?php
/** Class LetPHP_Utils */
defined('LETPHP') or exit('NO EXISTE LETPHP');

/**
 * Esta clase nos ayuda a interactuar de manera
 * sencilla con algunas clases de LetPHP.
 * 
 * @copyright LetCode IO
 * @author Rodrigo Hernández Ortiz
 * @package LetPHP\Utils
 * @version .1
 */

class LetPHP_Utils
{
	
	/**
	 * guardamos el nombre de la aplicación.
	 * @var string $_sApp 
	 */
	private $_sApp = '';
	
	/**
	 * Guardamos el nombre del controlador.
	 * @var string $_sDirController 
	 */
	private $_sDirController = '';

	/** @var array Parametros que se pasan a los Controladores/Fragmentos */
	private static $_aParams = []; 
	
	/**
	 * Cargamos los Parametros y el controlador/fragmento actual.
	 * @param array $aParams
	 * @return void
	 */
	public function __construct(array $aParams = [])
	{
		
		$this->_sApp = $aParams['sApp'];
		$this->_sDirController = $aParams['sDirController'];
		$this->setValue($aParams['aParams']);
	}
	
	/**
	 * Asignamos un valor a los fragmentos/controladores.
	 * @param mixed $mParam Nombre del Valor 
	 * @param string $sValue  Valor
	 * 
	 * @return void
	 */
	public function setValue($mParam, string $sValue = '')
	{
		if(!is_array($mParam))
		{
			$mParam = [$mParam => $sValue];
		}
		
		foreach($mParam as $sVar => $mValue)
		{
			self::$_aParams[$sVar] = $mValue;
		}
		
	}
	
	/**
	 * Obtenemos el valor
	 * @param mixed $mValue valor a obtener.
	 * 
	 * @return void
	 */
	public function getValue($mValue)
	{
		return ((isset(self::$_aParams[$mValue]))? self::$_aParams[$mValue] : null);
	}
	
	/**
		* Heredamos La clase Model
		* @see LetPHP_App::getModel()
		* @param string $sModel nombre del Model
		* @return object 
	*/
	protected function model(string $sModel = '' ): object
	{
		return LetPHP_App::getInstance()->getModel($sModel); //LetPHP::getClass('letphp.app')->getModel($sModel);	
	}
	
	/**
		* Heredamos la clase LetPHP_View para otras clases.
		* @see LetPHP_View
		* @return object regresa el objeto de la clase
	*/
	protected function view():object
	{
		return  LetPHP_View::getInstance();	//LetPHP::getClass('letphp.view');
	}
	
	
	/**
		* Heredamos la clase LetPHP_Router para otras clases
		* @see LetPHP_Router
		* @return object regresa el objeto de la clase
	*/
	protected function router(): object
	{
		return LetPHP_Router::getInstance(); //  LetPHP::getClass('letphp.router');
	}  
	
	
	/**
		* Heredamos la clase LetPHP_Http para otras clases
		* @return object regresa el objeto de la clase
	*/
	protected function http(): object
	{
		return LetPHP_Http::getInstance(); // LetPHP::getClass('letphp.http');
	}  
	
	
	/**
		* Heredamos la clase LetPHP_Database para otras clases
		* @return object Objetos de la Clase Database
	*/
	protected function database()
	{
		return LetPHP_Database::getInstance(); //LetPHP::getClass('letphp.database');
	}
	
	
	/**
	 * Heredamos la clase LetPHP_Database para otras clases
	 * @see LetPHP_Database
	 * @return LetPHP_App
	 */
	public function app()
	{
		return LetPHP_App::instance(); //LetPHP::getClass('letphp.app');
	}
	
	/**
		*
		* Obtenemos el nombre de la tabla con el preffix.
		* @param string $sTable Nombre de la tabla
		* @return string Nombre de la Tabla 
	*/
	protected function table(string $sTable): string
	{
		return LetPHP::getConfig(['database', 'prefix']). $sTable;
	}
	
	
}
	
	
?>