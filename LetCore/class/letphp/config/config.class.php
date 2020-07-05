<?php

/** Class LetPHP_Config */

defined('LETPHP') or exit('NO EXISTE LETPHP');

/**
 * Clase que controla las configuraciones globales del sitio.
 * esas configuraciones puedes modificarlas 
 * desde el archivo site.config.php .
 * 
 * @copyright LetCode IO
 * @author Rodrigo Hernández Ortiz
 * @package LetPHP\Config
 * @version .1
 * 
 */
class LetPHP_Config
{

	/** @var array $_aConfigs Arreglo donde guardaremos las configuraciones. */
  private $_aConfigs = [];


  /** 
    * Cargamos las configuraciones desde el 
    * archivo site.config.php
	  *
	*/
  public function __construct()
  {
	  if(!self::_requirements())
	  {
		  if(file_exists(LETPHP_LETSITE_ENGINE. 'requirements.php'))
		  {
				require(LETPHP_LETSITE_ENGINE. 'requirements.php');	  
			  exit;  
		  }
	  }
	  
    $_CONFIG = [];
    
    ## Configuraciones para el sitio
     $sFileCoreConfig = LETPHP_LETCORE_CONFIGS.'site.config.php';
    if(file_exists($sFileCoreConfig)){ require_once($sFileCoreConfig); }

    ## Configuraciones para la base de datos
    $sFileDBConfig = LETPHP_LETCORE_CONFIGS.'database.config.php';
    if(file_exists($sFileDBConfig)){ require_once($sFileDBConfig); }
    $this->_aConfigs = $_CONFIG;
  }

  /**
   * Obtenemos valor de alguna configuración.
   * @param mixed $mConfig Nombre  de la configuracion, puede ser un String o un Array
   * 
   * @return string
   */
  public function getConfig($mConfig): string
  {
    if(is_array($mConfig))
    {
      $sConfig = ((isset($this->_aConfigs[$mConfig[0]][$mConfig[1]]))? $this->_aConfigs[$mConfig[0]][$mConfig[1]] :'No existe configuración');
    }
    else
    {
      $sConfig = ((isset($this->_aConfigs[$mConfig])) ?$this->_aConfigs[$mConfig] :'No existe configuración');
    } 
    return $sConfig;
  }
  
  /**
   * Asignamos configuraciones para poder utilizar.
   * en todo el sitio.
   * @param mixed $mParam Nombre de la configuración.
   * @param string $mValue Valor de la configuración.
   * 
   * @return mixed
   */
  public function setConfig($mParam, string $mValue = '')
  {
	  if(!is_array($mParam))
	  {
		  $mParam = [$mParam => $mValue];
	  }
	  
	  foreach($mParam as $mKey => $mValue)
	  {
		  $this->_aConfigs[$mKey] = $mValue;
	  }
	  
  }
  
  public static function getInstance()
  {
	  return LetPHP::getClass('letphp.config');
  }
  
  /* 
	  * Cargamos las configuraciones de la base de datos  y las guardamos en Cache
	*/
  /*public function loadConfigs()
  {
	  $oCache = LetPHP::getClass('letphp.cache');
	  $sId = $oCache->setCache('configs');
	  
	  if(!($aRows = $oCache->getCache($sId)))
	  { 
		  $oDatabase = LetPHP::getClass('letphp.database');
		  $sTableSiteConfigs = LetPHP::getTable('site_configs');
		  $aRows = $oDatabase
		  ->select("site_config_id, site_config_name, site_config_value, site_config_value_default, site_config_app")
		  ->table($sTableSiteConfigs)
		  ->run('rows');
		  $oCache->saveCache($sId, $aRows);
	  }
	  
	  ## reescribimos las configuracions
	  foreach($aRows as $iKey => $aRow)
	  {
		  $this->_aConfigs[$aRow['site_config_app']. '.'. $aRow['site_config_name']] = (($aRow['site_config_value'] != '') ? $aRow['site_config_value'] :$aRow['site_config_value_default']);
	  }
	  
  }*/
  
  
  public static function _requirements()
  {
	  error_reporting(E_ALL);
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
			'PHP Version' => [version_compare(PHP_VERSION, '7.1', '>=')],
			//'PHP EXEC Function' => [function_exists('exec'), 'Habilita la función PHP "exec"'],
			//'PHP GD' => [(extension_loaded('gd') && function_exists('gd_info')), 'Missing PHP library GD'],
			//'PHP ZipArchive' => [(class_exists('ZipArchive')), 'Missing PHP ZipArchive'],
			'PHP CURL' => [(extension_loaded('curl') && function_exists('curl_init'))],
			'PHP Multibyte String' => [function_exists('mb_strlen')],
			//'PHP XML extension' => [extension_loaded('xml'), 'Missing PHP library XML'],
			'PHP memory_limit' => [($memory == '-1' ? true : $bMemory)],
			'Cache escritura' => [(is_writable(LETPHP_LETCORE_DIRS_CACHE. 'views'. LETPHP_DS))]
		];
		$bPassed = true;
		foreach($aRequirements as $bRe){ $bPassed *= $bRe[0]; }
		return $bPassed;
		
  } 
  

}

?>