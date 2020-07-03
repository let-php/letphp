<?php
/** Class LetPHP_Javascript */
defined('LETPHP') or exit('NO EXISTE LETPHP');

/**
 * Esta clase no ayuda a interactuar con algunas funciones
 * de Javascript escritas en PHP y administrar los llamados 
 * con Ajax con el estilo de LetPHP.
 * 
 * @copyright LetCode IO
 * @author Rodrigo Hernández Ortiz
 * @package LetPHP\Javascript
 * @version .1
 */

class LetPHP_Javascript
{
	/** @var $_oHttp Guardamos el objeto de HTTP. */
	private $_oHttp = null;
	
	/**
	 * @var array Guardamos las peticiones que llegan vía Ajax.
	 */
	private static $_aRequests = [];
	
	/**
	 * @var array Guardamos los callbacks de Javascript.
	 */
	private static $_aCallBacks = [];
	
	/**
	 * @var array Cargamos las variables que llegan via $_REQUEST
	 */
	private $_aAjaxParams = [];
	
	
	/**
   * Inicializamos el Objeto Http
   * y guardamos las solicitudes Http
	 */
	public function __construct()
	{
		$this->_oHttp = LetPHP::getClass('letphp.http');
		// Obtenemos las variables via Ajax locales
		$this->_aAjaxParams = $this->_oHttp->getParamArray(LetPHP::getConfig('main.session_prefix'));
		//d($this->_oHttp->getParams());
	}
	
	/**
   * Mandamos a llamar a la clase y creamos una instancia
   * de la misma.
	 * @return void
	 */
	public function start()
	{
		$oApp = LetPHP_App::getInstance(); //LetPHP::getClass('letphp.app');
		
		$aParts = explode('@', str_replace('.', '@', $this->_aAjaxParams['call']));
		
		$sApp = $aParts[0];
		
		if(isset($aParts[1]))
		{
			$sApp = $aParts[0];
			$sFunction = $aParts[1];
			
			if(isset($aParts[2]))
			{
				$sApp = '';
				$sApp = $aParts[0];
				$sApp .= '.'. $aParts[1];	
				$sFunction = $aParts[2];
			}
			
			if(isset($aParts[3]))
			{
				$sApp = '';
				$sApp = $aParts[0];
				$sApp .= '.'. $aParts[1]. '.'. $aParts[2];
				$sFunction = $aParts[3];
			}
			
			if(isset($aParts[4]))
			{
				
				$sApp = '';
				$sApp = $aParts[0];
				$sApp .= '.'. $aParts[1]. '.'. $aParts[2]. '.'. $aParts[3];
				$sFunction = $aParts[4];	
			}
		}
		
		//echo '<br>'. $sFunction;
		//exit;
		foreach($this->_oHttp->getParams() as $sKey => $mValue)
		{
			self::$_aRequests[$sKey] = $mValue;
		}
		
		if($oObject = $oApp->getApp($sApp, [], 'Ajax'))
		{
			$oObject->$sFunction();	
		}
	}
	
	/**
   * Obtenemos los callbacks.
	 * @return string
	 */
	public function getCode():string
	{
		
		$sXml = '';
		foreach(self::$_aCallBacks as $mCall)
		{
			$sXml .= $this->_javascriptSafe($mCall);
		}
		return $sXml;		
	}
	
	/**
   * Obtenemos las variables $_POST/$_GET
	 * @param mixed $sValue Nombre del Valor
	 * @param null $mDef
	 * 
	 * @return mixed
	 */
	public function getParam($sValue, $mDef = null)
	{
		return isset(self::$_aRequests[$sValue]) ? self::$_aRequests[$sValue] : $mDef;
	}	
	
	
	/**
  * Obtenemos un parametro de la ruta 
  * y lo convertimos en entero
	* @param string $sName nombre del parametro
	* @return int Regresa un valor entero
	*/
  public function getParamInt(string $sName = ''): int
  {
	  return (int)($this->getParam($sName));
  }
	
  
  /** 
  * Obtenemos un parametro de la ruta 
  * y lo convertimos en arreglo
	* @param string $sName nombre del parametro
	* @return array Regresa un arreglo
	*/
  public function getParamArray(string $sName = ''): array
  {
	  return (array)((isset(self::$_aRequests[$sName]))? self::$_aRequests[$sName]: '');
  }
  
	/**
   * Obtenemos el Codigo de un Fragmento
	 * @param bool $bClean Si es verdadero , limpiamos el codigo del fragmento
	 * 
	 * @return string
	 */
	public function getContentFile(bool $bClean = true ):string
	{
		$sContentFile = ob_get_contents();
		ob_clean();
		if($bClean)
		{
			$sContentFile = str_replace(array("\n", "\t"), '', $sContentFile);					
			$sContentFile = str_replace('\\', '\\\\', $sContentFile);
			$sContentFile = str_replace("'", "\\'", $sContentFile);			
			$sContentFile = str_replace('"', '\"', $sContentFile);
		}
		//echo $sContentFile;
		return $sContentFile;
	}
	
	
  
	/**
		* Funcion que emula alert() 
		* de Javascript
    * @param string $sMessage Mesaje a imprimir.
    * @return object
	*/
	public function let_alert(string $sMessage = ''): object
	{
		$this->LetPHPJavascript("alert('".$sMessage."');");
		return $this;
	}
	
	/** 
		* funcion de Javascript console.log();
		* @param string $sMessage Mensaje que se mostrará en la consola
	*/
	public function let_console_log(string $sMessage = '')
	{
		$this->LetPHPJavascript("console.log('".$sMessage."');");
		return $this;
	}
	
	/**
   * Oculta un elemento HTML, desde nuestra rutina.
	 * @param string $sSelector ID/Class del Selector 
	 * @return object
	 */
	public function let_hide(string $sSelector):object
	{
		$this->LetPHPJavascript("document.querySelector('".$sSelector."').style.display= 'none';");
		return $this;
	}
	
	/**
   * Muestra un elemento HTML, desde nuestra rutina.
	 * @param string $sSelector ID/Class del Selector 
	 * @return object
	 */
	public function let_show(string $sSelector):object
	{
		$this->LetPHPJavascript("document.querySelector('".$sSelector."').style.display= 'block';");
		return $this;
	}
	
	/** 
		* Agregar HTML al selector HTML
		* @param string $sSelector Selector a donde se agregará el contenido
    * @param string $sContent Contenido que se agregara al Elemento 
    * @return object
	*/
	public function let_html(string $sSelector, string $sContent):object
	{
		$sContent = str_replace('\\', '\\\\', $sContent);
		$sContent = str_replace('"', '\"', $sContent);
		
		$this->LetPHPJavascript("document.querySelector('".$sSelector."').innerHTML = \"".$sContent."\" ;");
		return $this;
			
	}
	
	/**
   * Inyectamos código Javascript.
   * 
	 * @param string $sCallBack
	 * 
	 * @return object
	 */
	public function LetPHPJavascript(string $sCallBack = ''):object
	{
		self::$_aCallBacks[] = $sCallBack;
		return $this;
	}
	
	public static function getInstance()
	{
		return LetPHP::getClass('letphp.javascript');
	} 
	
	/**
   * Limpiamos el codigo Javascript.
	 * @param string $sString
	 * 
	 * @return string
	 */
	private function _javascriptSafe(string $sString):string
	{
		$sString = str_replace(array("\n", "\r"), '\\n', $sString);
		return $sString;
	}
}	
	
?>