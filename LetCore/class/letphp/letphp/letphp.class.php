<?php
/** Class LetPHP_LetPHP */

defined('LETPHP') or exit('NO EXISTE LETPHP');
include LETPHP_LETSITE_ENGINE. "start.config.php";

/**
 * Toda la ingenieria de LetPHP se encuentra en esta clase.
 *  Cargar Clases interaccion con ellas, crear instancias
 *  y el punto de ejecución
 * 
 * 
 * @copyright LetCode IO
 * @author Rodrigo Hernández Ortiz
 * @package LetPHP\LetPHP
 * @version .1
 */
class LetPHP
{
	/** Agente de Nevegador para API curl requests. */
	const BROWSER_AGENT = 'LetPHP';
	
  /** @var $_aObjects Se guardan los objetos inicializados. */
  private static $_aObjects = [];

  /** @var $_aLibraries Se guardan las Librerías que se han cargado. */
  private static $_aLibraries = [];

	/** @var $_classes Guardamos las clases */
	private static $_classes = [];
	
	/** @var $_classes Guardamos las contenedor de clases */
	private static $_classContainer = null;
	
	private static $libsManager = [];
	
	
	public function classes()
	{
		if( self::$_classes == null)
		{
			self::$_classes = new LetPHP_Classes_Container();
		}
		return self::$_classes;
	}
	
	public static function getClassCore(string $sType, $item = null)
	{
		if( self::$_classes == null)
		{
			self::$_classes = new LetPHP_Classes_Container();
		}
		return self::$_classes->getClass($sType, $item);
	}

	/**
   * Obtenemos un Fragmento 
   * @see LetPHP_App::getApp();
	 * @param string $sFragment Nombre del Fragmento.
	 * @param array $aParams Parametros a Pasar.
	 * @param bool $bValuesView Valores se imprimen en la vista
	 * 
	 * @return object
	 */
	public static function getFragment(string $sFragment, array $aParams = [], bool $bValuesView = false):object
	{
		return LetPHP::getClass('letphp.app')->getApp($sFragment, $aParams, 'Fragments', $bValuesView);
	}

	/** 
		* Llamamos a un model de alguna aplicación,
		* es un atajo, para llamarlo desde la clase principal.
		* @see LetPHP_App::getModel()
		* @param string $sModel Nombre del Model
    * @param array $aParams Parametros que se le pasan al Model
    * @return object
	*/
	public static function getModel(string $sModel = '', array $aParams = []): object
	{
		return LetPHP::getClass('letphp.app')->getApp($sModel, $aParams, 'Models');
		//return LetPHP::getClass('letphp.app')->getModel($sModel, $aParams);
	}
	
	/** 
		* Obtenemos la versión actual de LetPHP.
		* @return string 
	*/
	public static function getVersion():string
	{
		return LetPHP::getConfig('main.version');
	}
	
  /**
  * Obtenemos valor de una configuracion.
  * @see LetPHP_Config::getConfig()
  * @param mixed $sConfig  nombre de la configuración
  * @return string valor de la configuración
  */
  public static function getConfig( $sConfig = ''): string
  {
    return LetPHP::getClass('letphp.config')->getConfig($sConfig);
  }

	/** 
	* Verificamos e Incluimos el Script de la Clase.
	* @param string $sFileClass Nombre del Archivo de la clase
	*	@return bool regresa un valor boleano
	*/
  public static function getFileClass(string $sFileClass = ''): bool
  {
	  
    if(isset(self::$_aLibraries[$sFileClass]))
    { 
      return true; 
    }
    self::$_aLibraries[$sFileClass] = sha1($sFileClass);
    $sFileClass = str_replace('.', LETPHP_DS, $sFileClass);
    $sFile = LETPHP_LETCORE_CLASS.$sFileClass.'.class.php';
		
    if(file_exists($sFile))
    {
      require($sFile);
      return true;
    }

    $aParts = explode(LETPHP_DS, $sFileClass);

    if(isset($aParts[1]))
    {
      $sSubClass = LETPHP_LETCORE_CLASS. $sFileClass.LETPHP_DS.$aParts[1].'.class.php';
      // Existe Archivo con la clase
      if(file_exists($sSubClass))
      {
        require($sSubClass);
        return true;
      }
    }

    return false;
  }
	
	/** 
  * Llamamos La clase y creamos la instancia.
	* @param string $sClass nombre de la clase.
	* @param array $aParams valores que pasamos al objeto.
	* @return object regresa el objeto de la clase.
	*/
  public static function getClass(string $sClass = '', array $aParams = [])//: object
  {
	 
	  $sClassCache = 'class_'. $sClass;
	  if(isset(self::$libsManager[$sClassCache]))
	  {
		  return self::$libsManager[$sClassCache];
	  }
	  return (self::$libsManager[$sClassCache] = self::getClassContainer()->getClass($sClass));
	  /*
     if((substr($sClass, 0, 7) !== 'letphp.') || ($sClass === 'letphp.start'))
     {
       $sClass = 'letphp.'.$sClass;
     }
     $sHash = sha1($sClass. serialize($aParams));
     if(isset(self::$_aObjects[$sHash]))
     {
       return self::$_aObjects[$sHash];
     }
     LetPHP::getFileClass($sClass);
     $sClass = str_replace('letphp.letphp.', 'letphp.', $sClass);
     self::$_aObjects[$sHash] = LetPHP::getObject($sClass, $aParams);
     return self::$_aObjects[$sHash];
     */
  }
  
  public static function getClassContainer(): object
  {
	  if(null == self::$_classContainer)
	  {
		  self::$_classContainer = new LetPHP_Class_Container();
	  }
	  return self::$_classContainer;
  } 

	/**
	* Obtenemos y creamos el Objeto de una clase.
	* @param string $sClass nombre de la clase.
	* @param array $aParams valores que le pasamos a la Clase.
	* @return object Regresa el Objeto creado.
	*/
  public static function &getObject(string $sClass = '', array $aParams = []):object
  {
	  
	  $sHash = sha1($sClass. serialize($aParams));
    if(isset(self::$_aObjects[$sHash]))
    {
      return self::$_aObjects[$sHash];
    }
    $sClass = str_replace(['.', '-'], '_', $sClass );
    if(!class_exists($sClass))
    {
      exit('Lo sentimos, no existe la clase');
    }
    
    // Verificamos pasamos parametros 
    if($aParams)
    {
      self::$_aObjects[$sHash] = new $sClass($aParams);
    }
    else 
    {
      self::$_aObjects[$sHash] = new $sClass();
    }
    
    if(method_exists(self::$_aObjects[$sHash], 'getObject'))
    {
	    return self::$_aObjects[$sHash]->getObject();
    }
    return  self::$_aObjects[$sHash];
  }
  
  /**
   * función que inicializa LetPHP
   * @return void
   */
  public static function start()
  {
	  $oView = LetPHP_View::getInstance();
	  $oApp = LetPHP_App::getInstance();
	  $oApp->setController();
	  $oApp->getController();
	  $oView->getView($oView->sDisplayView);
  }
  
  
  /**
   * Asignamos un mensaje
   * @param string $sMessage
   * 
   * @return void
   */
  public static function setMessage(string $sMessage = '')
  {
    LetPHP::getClass('letphp.auth')->setSession('message', $sMessage);
  }
  
  /**
   * Obtenemos el mensaje.
   * @return string
   */
  public static function getMessage():string
	{
		return LetPHP::getClass('letphp.auth')->getSession('message');
	}
	
  /**
   * Limpiamos el mensaje
   * @return void
   */
  public static function resetMessage()
  {
	  LetPHP::getClass('letphp.auth')->removeSession('message');
  }
  
  
  
  
  
  public static function _requirements()
  {
	  
	  if(!is_writable(LETPHP_LETCORE_DIRS_CACHE. 'views'. LETPHP_DS))
	  {
		  $memory = @ini_get('memory_limit');
			$subString = substr($memory, -1);
			$iString = (int) $memory;
			switch ($subString) {
				case 'K':
					$iString = $iString/1000;
					break;
				case 'G':
					$iString = $iString*1000;
					break;
				default:
					# code...
					break;
			}
			
			if ($iString >= 64) {
				$bMemory = true;
			} else {
				$bMemory = false;
			}

		  		  
			$aRequirements = [
				'PHP Version' => [version_compare(PHP_VERSION, '7.1', '>='), 'Le versión de PHP que tienes es ' . PHP_VERSION . '. LetPHP requiere la versión 7.1 o una versión mayor.'],
				//'PHP EXEC Function' => [function_exists('exec'), 'Habilita la función PHP "exec"'],
				//'PHP GD' => [(extension_loaded('gd') && function_exists('gd_info')), 'Missing PHP library GD'],
				//'PHP ZipArchive' => [(class_exists('ZipArchive')), 'Missing PHP ZipArchive'],
				'PHP CURL' => [(extension_loaded('curl') && function_exists('curl_init')), 'Debes de habilitar la Libreria CURL de PHP'],
				'PHP Multibyte String' => [function_exists('mb_strlen'), 'Debes habilitar la libreria Multibyte String de PHP'],
				//'PHP XML extension' => [extension_loaded('xml'), 'Missing PHP library XML'],
				'PHP memory_limit' => [($memory == '-1' ? true : $bMemory), 'El límite de memoria de su servidor es ' . $memory . '. LetPHP requiere 64MB o más.'],
				//'Cache escritura' => [(is_writable(LETPHP_LETCORE_DIRS_CACHE. 'views'. LETPHP_DS)), 'El archivo no tiene permisos de escritura']
			];
			
			$iValid = true;
			foreach($aRequirements AS $iKey => $mValue)
			{
				$iValid *= $mValue[0];
			}
			
		  //LetPHP_View::getInstance()->getView('requirements');

		  exit;
	  }
	  
  }
  
  
}
?>