<?php
	
/** Class LetPHP_App */
defined('LETPHP') or exit('LETPHP NO DEFINIDO');
/**
 * Administra las Aplicaciones
 * 
 * Esta clase hace un llamado a nuestras aplicaciones
 * que vamos desarrollando, al igual que a los sus componentes
 * como Controladores, Fragmentos y Modelos.
 * 
 * @copyright LetCode IO
 * @author Rodrigo Hernández Ortiz
 * @package LetPHP\App 
 * @version .1
*/
class LetPHP_App 
{

	/** @var array $_aControllers Arreglo para guardar los controladores que van cargando */
  private $_aControllers = [];
  
  /** @var array $_aModels Arreglo para guardar los controladores que van cargando */ 
	private $_aModels = [];
  
  /** @var string $_sController Controlador que se cargar por default, para ejecutar */
  private  $_sController = 'index';
  
  
  /** @var string $_sControllerView Guardamos la vista del controlador que se carga */
  private $_sControllerView = '';
  
  /** @var string $_sApp Nombre  de app que se va a ejecutar */
  private $_sApp = '';
  
  /** @var array $_aReturn  Guardamos el valor de las clases de los controladores, fragmentos y modelos */
	private $_aReturn = [];
	
  /**
	* Seleccionamos la aplicacion principal
  */
  public function __construct()
  {
	  $this->_sApp = LetPHP::getConfig('main.app_core');
  }

	/**
		* Cargamos el Fragmento
		* @param string $sFragment
		*/
	public function getFragment(string $sFragment = '', array $aParams = [])
	{
		return LetPHP_App::getInstance()->getApp($sFragment, $aParams, 'Fragments');
	}

  /**
   * Obtenemos el Controlador que se ha asignado 
	 * en la funcion setController()
   * @see $this->setController()
   */
  public function getController()
  {
	  
	  /*if(LetPHP::getConfig('main.app_core') !== '')
	  {
		  $oHttp = LetPHP::getClass('letphp.http');
		  echo $this->_sController;
		  //$sParam1 = $oHttp->getParams('param1');
	//	  echo '<br>'.$this->_sApp = $oHttp->getParam('param1'). '<br/>';  //(($sParam1 = $oHttp->getParam('param1')) ?strtolower($sParam1) : LetPHP::getConfig('main.app_core'));  
			//$this->_sController = $this->_sApp. '.'. $this->_sController; //$this->_sApp. '.'. $this->_sController;
	  }*/
	  //$this->_oController = $this->getApp($this->_sController, [], 'Controllers');
    $this->_oController = $this->getApp($this->_sApp.'.'. $this->_sController, [], 'Controllers');
  }
  
  
	/**
	 * Regresa el nombre del controlador actual. 
	 * @return string 
	*/
  public function getControllerName():string
  {
	  return  $this->_sController;
  }

  /**
   * Asignamos el nombre del controlador, esta función  
   * carga el controlador con el cual trabajaremos  
   * @param string $sController nombre del controlador que vamos a cargar.
   */
	public function setController(string $sController = '')
  {
	  
    if($sController != '')
    {
      $aControllers = explode('.', $sController);
      $this->_sApp = $aControllers[0];
      $this->_sController = substr_replace($sController, '', 0, strlen($this->_sApp.'_'));
      $this->getController();
      return;
    }
    $oHttp = LetPHP::getClass('letphp.http');
    $oRouter = LetPHP::getClass('letphp.router');

    $this->_sApp = (($sParam1 = $oHttp->getParam('param1')) ?strtolower($sParam1) : LetPHP::getConfig('main.app_core'));  
    $sDir = LETPHP_LETAPPS.$this->_sApp.LETPHP_DS;
    
    
    if ($oHttp->getParam('param2') && 
    file_exists($sDir. 'Controllers'. LETPHP_DS. strtolower($oHttp->getParam('param2')). '.'. LETPHP_APP_SUFFIX_CONTROLLER))
		{
      $this->_sController = strtolower($oHttp->getParam('param2'));
		}
		elseif($oHttp->getParam('param2') && file_exists($sDir. 'Controllers'. LETPHP_DS. strtolower($oHttp->getParam('param2')). 'index.'. LETPHP_APP_SUFFIX_CONTROLLER)  )		
		{
			$this->_sController = strtolower($oHttp->getParam('param2')).'.index';
		}
		elseif($oHttp->getParam('param2') && file_exists($sDir. 'Controllers'. LETPHP_DS. strtolower($oHttp->getParam('param2')). LETPHP_DS. 'index.'. LETPHP_APP_SUFFIX_CONTROLLER))
		{
			$this->_sController = strtolower($oHttp->getParam('param2')).'.index';
		}
		//echo $sDir. 'Controllers'. LETPHP_DS. strtolower($oHttp->getParam('param2')). LETPHP_DS. $oHttp->getParam('param3'). '.'. LETPHP_APP_SUFFIX_CONTROLLER;
		/*elseif($oHttp->getParam('param3') && file_exists($sDir. 'Controllers'. LETPHP_DS. strtolower($oHttp->getParam('param2')). LETPHP_DS. $oHttp->getParam('param3'). '.'. LETPHP_APP_SUFFIX_CONTROLLER))
		{
			echo 'Existe';
			//$this->_sController = strtolower($oHttp->getParam('param3'). '.index');
		}
		//d($oHttp);
		echo $sDir. 'Controllers'. LETPHP_DS. strtolower($oHttp->getParam('param2')). LETPHP_DS. $oHttp->getParam('param3'). '.'. LETPHP_APP_SUFFIX_CONTROLLER;*/
		
    if(($this->_sApp == LetPHP::getConfig('main.app_core')) && ($this->_sController == 'index') )
    {
      $this->_sController = 'index';
    }

    
		if((LetPHP::getConfig('main.site_secure') == 1) && (!isset($_SERVER['HTTPS'])) )
		{
			header('Location: https://'.LetPHP::getConfig('main.host'));
			exit;
		}
	   
  }
  
  /*public function setController(string $sController = '')
  {
    if($sController != '')
    {
	    $aControllers = explode('.', $sController);
      $this->_sApp = $aControllers[0];
      $this->_sController = substr_replace($sController, '', 0, strlen($this->_sApp.'_'));
      $this->getController();
      return;
    }
    $oHttp = LetPHP::getClass('letphp.http');
    
    /*if($oHttp->getParam('let') != '' && $oHttp->getParam('let') != LETPHP_DS ) 
    {
	    $sController = trim($oHttp->getParam('let'), '/');
	    $aPartsController = explode(LETPHP_DS, $sController); 
	    $this->_sApp = $aPartsController[0];
		  $aPartsController = (array_slice($aPartsController, 1));
		  
		  $this->_sController = $this->_sApp.'.' .'index';
		  if(count($aPartsController))
		  {
				$sController = implode('.', $aPartsController);  
				$this->_sController = $this->_sApp. '.'. $sController;
		  }
		  //$this->_sController = $this->_sApp. '.'. $sController;
		  
	    //array_
	    //d($aPartsController);
	    //$aParts = explode(, )
    }
    else 
    {
	    $this->_sController = 'index';
	    if(LetPHP::getConfig('main.app_core') != '')
	    {
		    $this->_sApp = (($sParam1= $oHttp->getParam('param1'))? $oHttp->getParam('param1') : LetPHP::getConfig('main.app_core'));
		    
		    echo $this->_sController = $this->_sApp. '.'. $this->_sController;
		    if($oHttp->getParam('param2')){ $this->_sController = $this->_sApp. '.'. $oHttp->getParam('param2'); }
		    elseif($oHttp->getParam('param3')){ $this->_sController = $this->_sApp. '.'. $oHttp->getParam('param3'); }
		    elseif($oHttp->getParam('param4')){ $this->_sController = $this->_sApp. '.'. $oHttp->getParam('param4'); }
		    elseif($oHttp->getParam('param5')){ $this->_sController = $this->_sApp. '.'. $oHttp->getParam('param5'); }
		    elseif($oHttp->getParam('param6')){ $this->_sController = $this->_sApp. '.'. $oHttp->getParam('param6'); }
		    elseif($oHttp->getParam('param7')){ $this->_sController = $this->_sApp. '.'. $oHttp->getParam('param7'); }
		    elseif($oHttp->getParam('param8')){ $this->_sController = $this->_sApp. '.'. $oHttp->getParam('param8'); }
		    
		    //echo $this->_sController;
		    
		    /*$this->_sController = (($oHttp->getParam('param2') != '')? $oHttp->getParam('param2'): 'index');
		    $this->_sController = $this->_sApp. '.'. $this->_sController; 
		    
		    $this->_sController = (($oHttp->getParam('param3') != '')? $oHttp->getParam('param3'): 'index');
		    $this->_sController = $this->_sApp. '.'. $this->_sController; 
		    
		    $this->_sController = (($oHttp->getParam('param4') != '')? $oHttp->getParam('param4'): 'index');
		    $this->_sController = $this->_sApp. '.'. $this->_sController; 
		    
		    $this->_sController = (($oHttp->getParam('param5') != '')? $oHttp->getParam('param5'): 'index');
		    $this->_sController = $this->_sApp. '.'. $this->_sController; 
		    
		    $this->_sController = (($oHttp->getParam('param6') != '')? $oHttp->getParam('param6'): 'index');
		    $this->_sController = $this->_sApp. '.'. $this->_sController; */
	    //} 
    //}
    
    // Exist app principal
    
    /*if(LetPHP::getConfig('main.app_core') != '')
    {
	    $aRequests = $oHttp->getParams();
	    $this->_sApp = LetPHP::getConfig('main.app_core');
	    if(!isset($aRequests[LETPHP_GET_METHOD]) OR ($aRequests[LETPHP_GET_METHOD] == LETPHP_DS))
	    {
		    $this->_sController = $this->_sApp. '.index'; 
	    }
	    //else if(isset($aP))
	    else if(isset($aRequests['let']) && ($aRequests['let'] != ''))
	    {
		    $this->_sApp = $oHttp->getParam('param1');
		    $this->_sController = '';
		    foreach($aRequests AS $iKey => $mValue )
				{
					if($iKey != LETPHP_GET_METHOD && $iKey != 'param1')
					{
						$this->_sController .=  $mValue. '.';
					}
				}
	    }
	    //echo '<br/>'. $this->_sController;
	    
	    //d($oHttp);
	    
    }
    else
    {
	    echo 'No';
    }*/
    
    
    /*d($oHttp);
		$this->_sController =  'index';
		if($oHttp->getParam('let') != '')
		{
			
			$this->_sController = str_replace(LETPHP_DS, '.', $oHttp->getParam('let'));
			if($oHttp->getParam('let') === '/')
			{
				$this->_sController = 'index';
			}
			
			if(LetPHP::getConfig('main.app_core') !== '')
			{	
				echo $this->_sController = $oHttp->getParam('param1');
				foreach($oHttp->getParams() AS $iKey => $mValue )
				{
					if($iKey != LETPHP_GET_METHOD)
					{
						$this->_sController .=  $mValue. '.';
					}
				}
				
				//d($oHttp);
				//echo '<br/>Controller'. $this->_sController. 'SS';
				/*d($oHttp->getParams());
				
				$aParts = explode(LETPHP_DS, $oHttp->getParam('let'));
				d($aParts);
				echo '<br/>Hola'.$this->_sController = str_replace(LETPHP_DS, '.', $oHttp->getParam('let'));
				if($this->_sController === '' || $oHttp->getParam('let') === '/')
				{
					$this->_sController = 'index';
				}*/
			/*}
			
			//exit;
			
			//$this->_sController = str_replace(LETPHP_DS, '.', $oHttp->getParam('let'));
		}
		
		/*
    $this->_sApp = ( ($sParam1 = $oHttp->getParam('param1')) ?strtolower($sParam1) : LetPHP::getConfig('main.app_core'));  
    echo '<br>Dir'. $sDir = LETPHP_LETAPPS.$this->_sApp.LETPHP_DS;
    exit;
    if ($oHttp->getParam('param2') && 
    file_exists($sDir. 'Controllers'. LETPHP_DS. strtolower($oHttp->getParam('param2')). '.'. LETPHP_APP_SUFFIX_CONTROLLER))
		{
      $this->_sController = strtolower($oHttp->getParam('param2'));
		}
		elseif($oHttp->getParam('param2') && file_exists($sDir. 'Controllers'. LETPHP_DS. strtolower($oHttp->getParam('param2')). LETPHP_DS. 'index.'. LETPHP_APP_SUFFIX_CONTROLLER)  )		
		{
			$this->_sController = strtolower($oHttp->getParam('param2')).'.index';
		}
    
    if(($this->_sApp == LetPHP::getConfig('main.app_core')) && ($this->_sController == 'index') )
    {
      $this->_sController = 'index';
    }
    
		if((LetPHP::getConfig('main.site_secure') == 1) && (!isset($_SERVER['HTTPS'])) )
		{
			header('Location: https://'.LetPHP::getConfig('main.host'));
			exit;
		}
		
  }*/


  /**
   * Carga una App, junto a sus Controladores y su Fragmentos
   * Controladores : Construyen la página del sitio
   * Fragmentos : Son pequeñas partes de un controlador
   * @param string $sClass Nombre del componente a cargar
   * @param array $aParams (Opcional) 
   * @param string $sType (Opcional) 
	 * @param bool $bViewValues false
	 * @return mixed Regresa el controlador, fragmento, modelo o Ajax que se requiera
  */
  public function getApp(string $sClass = '', array $aParams = [], string $sType = '', bool $bViewValues = false)
  {
	  $aReplace = [
	  LETPHP_LETAPPS_CONTROLLERS,
	  LETPHP_LETAPPS_FRAGMENTS,
	  LETPHP_LETAPPS_MODELS,
	  LETPHP_LETAPPS_AJAX
	  ];

	  $aReplaceSuffix = [
	  	LETPHP_APP_SUFFIX_CONTROLLER, 
	  	LETPHP_APP_SUFFIX_FRAGMENT, 
	  	LETPHP_APP_SUFFIX_MODEL, 
	  	LETPHP_APP_SUFFIX_AJAX
	  ];

	  $aReplaceObject = [
		  LETPHP_OBJECT_CONTROLLER,
		  LETPHP_OBJECT_FRAGMENT,
		  LETPHP_OBJECT_MODEL,
		  LETPHP_OBJECT_AJAX
	  ];

		
		$mSuffixType = str_replace($aReplace, $aReplaceSuffix, $sType);
		$mObjectClass = str_replace($aReplace, $aReplaceObject, $sType);
		$sFile = '';
		$bExists = false;
		
		
		//echo LETPHP_LETAPPS. $sType. LETPHP_DS. str_replace('.', LETPHP_DS, $sClass). '.'. $mSuffixType;
		/*if(strpos($sClass, '_'))
		{
			$aPartsClass = explode('.', $sClass);
			$sClass = $aPartsClass[1];
		}
		
		/*echo '<br>'. LETPHP_LETAPPS. str_replace('.index', '', $sClass). '.' .$mSuffixType;
		echo '<br>'. LETPHP_LETAPPS. str_replace('.', LETPHP_DS, $sClass). '.' .$mSuffixType;
		echo '<br>'. LETPHP_LETAPPS. str_replace('.', LETPHP_DS, $sClass). LETPHP_DS. 'index.' .$mSuffixType;
		echo '<br>------------------------------ carpeta por tipo <br>';
		echo '<br>'. LETPHP_LETAPPS. $sType. LETPHP_DS. str_replace( '.index', '', $sClass). '.'. $mSuffixType;  
		echo '<br>'. LETPHP_LETAPPS. $sType. LETPHP_DS. str_replace('.', LETPHP_DS, $sClass). '.'. $mSuffixType;  
		echo '<br>'. LETPHP_LETAPPS. $sType. LETPHP_DS. str_replace('.', LETPHP_DS, $sClass). LETPHP_DS. 'index.'. $mSuffixType;  */
		if(file_exists(LETPHP_LETAPPS. str_replace('.index', '', $sClass). '.' .$mSuffixType))
		{
			//Bien
			$sFile = LETPHP_LETAPPS. str_replace('.index', '', $sClass). '.' .$mSuffixType;
			$sObject = str_replace('.index', '', $sClass). rtrim($mObjectClass, '_');
			if($sType !== 'Models'){ $this->_sControllerView = $sClass; }
			$bExists = true;
		}
		elseif(file_exists(LETPHP_LETAPPS. str_replace('.', LETPHP_DS, $sClass). '.' .$mSuffixType))
		{
			//Bien
			$sFile = LETPHP_LETAPPS. str_replace('.', LETPHP_DS, $sClass). '.' .$mSuffixType;
			$sObject = str_replace('.', '_', $sClass). rtrim($mObjectClass, '_');
			if($sType !== 'Models'){ $this->_sControllerView = $sClass; }
			$bExists = true;
		}
		elseif(file_exists(LETPHP_LETAPPS. str_replace('.', LETPHP_DS, $sClass). LETPHP_DS. 'index.' .$mSuffixType))
		{
			// Bien
			$sFile = LETPHP_LETAPPS. str_replace('.', LETPHP_DS, $sClass). LETPHP_DS. 'index.' .$mSuffixType;
			$sObject = str_replace('.', '_', $sClass ). '_Index'. rtrim($mObjectClass, '_');
			if($sType !== 'Models'){  $this->_sControllerView = $sClass; }
			$bExists = true;
		}
		elseif(file_exists(LETPHP_LETAPPS. $sType. LETPHP_DS. str_replace( '.index', '', $sClass). '.'. $mSuffixType))
		{
			// Bien
			$sFile = LETPHP_LETAPPS. $sType. LETPHP_DS. str_replace( '.index', '', $sClass). '.'. $mSuffixType;  
			$sObject = ltrim($mObjectClass, '_'). str_replace('.index', '', $sClass);
			if($sType !== 'Models'){ $this->_sControllerView = $sType. '.'. $sClass; }
			$bExists = true;
		}
		elseif(file_exists(LETPHP_LETAPPS. $sType. LETPHP_DS. str_replace('.', LETPHP_DS, $sClass). '.'. $mSuffixType))
		{
			// Bien
			$sFile = LETPHP_LETAPPS. $sType. LETPHP_DS. str_replace('.', LETPHP_DS, $sClass). '.'. $mSuffixType;
			$sObject = ltrim($mObjectClass, '_') . str_replace('.', '_', $sClass);
			if($sType !== 'Models'){ $this->_sControllerView = $sType. '.'. $sClass; }
			$bExists = true;
		}
		elseif(file_exists(LETPHP_LETAPPS. $sType. LETPHP_DS. str_replace('.', LETPHP_DS, $sClass). LETPHP_DS. 'index.'. $mSuffixType))
		{
			$sFile = LETPHP_LETAPPS. $sType. LETPHP_DS. str_replace('.', LETPHP_DS, $sClass). LETPHP_DS. 'index.'. $mSuffixType;
			$sObject = ltrim($mObjectClass, '_'). str_replace('.', '_', $sClass). '_Index' ;
			if($sType !== 'Models'){  $this->_sControllerView = $sType. '.'. $sClass; }
			$bExists = true;
		}
		
		
		
		/*if(($bExists) && (file_exists($sFile)))
		{
			$sHash = sha1($sClass. str_replace($aReplace, ['controller', 'fragment', 'model', 'ajax'], $sType));
			require($sFile);
			$this->_aControllers[$sHash] = LetPHP::getObject($sObject, ['sApp' => '', 'sDirController' => '', 'aParams' => $aParams]);
			
			d($this->_aControllers);
			return $this->_aControllers[$sHash];
			
		}*/
		
		
		  /*if(file_exists(LETPHP_LETAPPS. $sClass. '.'. $mType))
		  {
			 	$sFile = LETPHP_LETAPPS. $sClass. '.'. $mType;
			 	$sObject = ucfirst($sClass). '_'. ucfirst(str_replace('.php', '', $mType));
			 	if($sType !== 'Models' ){ $this->_sControllerView = $sClass; }
			  $bExists = true;
		  }
		  
		   
		  // Si no existe en la carpeta raíz. buscamos dentro de una carpeta cualquier archivo
		  if((!$bExists) && (file_exists(LETPHP_LETAPPS. str_replace('.', LETPHP_DS, $sClass). '.'. $mType)))
		  {
			  $sClass = str_replace( '.', LETPHP_DS, $sClass);
			  $sFile = LETPHP_LETAPPS. ($sClass). '.'. $mType; 
			  $mType = str_replace('.php', '', $mType);
			  $sObject = str_replace(LETPHP_DS, '_', $sClass). '_'. $mType ;
			  if($sType !== 'Models'){ $this->_sControllerView = $sClass; }
			  $bExists = true;
		  }
		  
		  // Si no existe en la carpeta raíz. buscamos dentro de una carpeta el archivo index
		  if((!$bExists) && (file_exists(LETPHP_LETAPPS. str_replace('.', LETPHP_DS, $sClass). LETPHP_DS. 'index.'. $mType)))
		  { 
			  $sFile = LETPHP_LETAPPS. str_replace('.', LETPHP_DS, $sClass). LETPHP_DS. 'index.'. $mType;
			 	$sObject = ($sClass). '_Index_'. str_replace('.php', '', $mType);
			 	if($sType !== 'Models'){ $this->_sControllerView = $sClass; }
			  $bExists = true;
		  }
		  
		  // Buscado el archivo en la carpeta estructurada pero carpeta Index 
		  if((!$bExists) && (file_exists(LETPHP_LETAPPS. $sType. LETPHP_DS. str_replace( '.', LETPHP_DS, $sClass). '.'. $mType)) )
		  {
			  $sFile = LETPHP_LETAPPS. $sType. LETPHP_DS. str_replace( '.', LETPHP_DS, $sClass). '.'. $mType;
			  $sObject = str_replace('.php', '', $mType). '_'. ($sClass); //'_'. str_replace('.php', '', $mType);
			  if($sType !== 'Models'){  $this->_sControllerView = $sClass; }
			  $bExists = true; 
		  }
		  

		  
		  if((!$bExists) && file_exists(LETPHP_LETAPPS. $sType. LETPHP_DS. str_replace( '.', LETPHP_DS, $sClass). LETPHP_DS. 'index.'. $mType))
		  {
			  $sFile = LETPHP_LETAPPS. $sType. LETPHP_DS. str_replace( '.', LETPHP_DS, $sClass). LETPHP_DS. 'index.'. $mType;
			  $sObject = str_replace('.php', '', $mType). '_'. ($sClass). '_Index'; 
			  if($sType !== 'Models'){  $this->_sControllerView = $sClass; }
			  $bExists = true;
			}*/
			
			
			// Buscamos en archivo por medio de los Modulos
			if(LetPHP::getConfig('main.app_core') != '')
			{
				
					if($sType == 'Models')
					{
						$aParts = explode('.', $sClass);
					  $this->_sApp = $aParts[0];
					  if(isset($aParts[1]))
					  {				 
						  $aParts = (array_slice($aParts, 1));
						  $sClassModel = implode('.', $aParts).'.'; 
					  }
					  else 
					  {
						  $sClassModel = 'index';
					  }
					}
					
					if($sType === 'Ajax')
			    {
				    $aParts = explode('.', $sClass);
				    $this->_sApp = $aParts[0];
					  if(isset($aParts[1]))
					  {				 
						  $aParts = (array_slice($aParts, 1));
						  $sSlass = implode('.', $aParts).'.'; 
					  }
					  else 
					  {
						  $sSlass = 'index';
					  } 
			    }
					
					$aParts = explode('.', $sClass);
					//d($aParts);
				  $sApp = $aParts[0];
					if(isset($aParts[1]))
					{				 
						$aParts = (array_slice($aParts, 1));
						$sClass = implode('.', $aParts); 
					}
					else 
					{
						$sClass = 'index';
					}
					//echo $this->_sApp;
					$sClassObject = str_replace('.', '_', $sClass);
					$sClass = str_replace('.', LETPHP_DS, $sClass);
					
					//echo '<br/>'. $sClass;
					//echo '<br><br>OPT1'. LETPHP_LETAPPS. $sApp. LETPHP_DS. $sType. LETPHP_DS. $sClass. '.'. $mSuffixType;
					//echo '<br><br>OPT2'. LETPHP_LETAPPS. $sApp. LETPHP_DS. $sType. LETPHP_DS. $sClass. LETPHP_DS. 'index.'. $mSuffixType;
					//echo LETPHP_LETAPPS. $sApp. LETPHP_DS. $sType. LETPHP_DS. $sClass. LETPHP_DS. 'index.'. $mSuffixType;

					//echo LETPHP_LETAPPS. $sApp. LETPHP_DS. $sType. LETPHP_DS. $sClass. '.'. $mSuffixType;
					if(file_exists(LETPHP_LETAPPS. $sApp. LETPHP_DS. $sType. LETPHP_DS. $sClass. '.'. $mSuffixType))
					{
						//LETPHP_DIR_PARENT;
						$sFile = LETPHP_LETAPPS. $sApp. LETPHP_DS. $sType. LETPHP_DS. $sClass. '.'. $mSuffixType;
						$sObject =  $sApp. '.'. $sType. '.'. $sClassObject. rtrim($mObjectClass, '_');
						//LETPHP_LETAPPS. $sApp. LETPHP_DS. $sType. LETPHP_DS. $sClass;
						$sObject = str_replace([LETPHP_DIR_PARENT, LETPHP_DS], ['', '.'], LETPHP_LETAPPS. $sApp. LETPHP_DS. $sType. LETPHP_DS. $sClass). rtrim($mObjectClass, '_');
						if($sType !== 'Models')
						{  
							$this->_sControllerView = $sApp. '.'. $sType. '.'. $sClass; 
						}
						$bExists = true;
					}
					else if(file_exists(LETPHP_LETAPPS. $sApp. LETPHP_DS. $sType. LETPHP_DS. $sClass. LETPHP_DS. 'index.'. $mSuffixType))
					{
						$sFile = LETPHP_LETAPPS. $sApp. LETPHP_DS. $sType. LETPHP_DS. $sClass. LETPHP_DS. 'index.'. $mSuffixType;
						$sObject = $sApp. $mObjectClass. $sClassObject. '_Index';
						if($sType !== 'Models')
						{  
							$this->_sControllerView = $sApp. '.'. $sType. '.'. $sClass; 
						}
						$bExists = true;
					}
			}
					
			$sHash = sha1($sClass. str_replace($aReplace, $aReplaceObject, $sType));
		  if(isset($this->_aControllers[$sHash]))
		  {  
			  $this->_aControllers[$sHash]->__construct(['sApp' => '', 'sDirController' => '', 'aParams' => $aParams]);
		  }
		  else
		  {			
			  if(($bExists))
			  {
				  require($sFile);
				  $sObject = str_replace('.', '\\', $sObject);
				  $this->_aControllers[$sHash] = LetPHP::getObject($sObject, ['sApp' => $this->_sApp, 'sDirController' => '', 'aParams' => $aParams]);
				  
				  $mReturn = 'empty';
				  if($sType !== 'Ajax' AND $sType !== 'Models' ){ $mReturn = $this->_aControllers[$sHash]->start(); }
				  if($sType === 'Fragments')
			    {				   
				    $sViewFragment = $this->_sControllerView; 
						LetPHP::getClass('view')->getViewApp($sViewFragment);
			    }
			    
				  $this->_aReturn[$sClass] = $mReturn;
			    
			  } 
		  }
	  return $this->_aControllers[$sHash];
	  			
		LETPHP_LETAPPS. $this->_sApp. LETPHP_DS. $sType. LETPHP_DS. $sClass. LETPHP_APP_SUFFIX_CONTROLLER;
		if(file_exists(LETPHP_LETAPPS. $this->_sApp. LETPHP_DS. $sType. LETPHP_DS. $sClass. LETPHP_APP_SUFFIX_CONTROLLER))
			{
				$sFile = LETPHP_LETAPPS. $this->_sApp. LETPHP_DS. $sType. LETPHP_DS. $sClass. LETPHP_APP_SUFFIX_CONTROLLER;
				$sObject = $this->_sApp. LETPHP_OBJECT_CONTROLLER. str_replace('.', '', $sClass);
				if($sType !== 'Models')
				{  
					$this->_sControllerView = $this->_sApp. '.'. str_replace('.', '', $sClass); 
				}
				$bExists = true;
			}
			
			//echo LETPHP_LETAPPS. $this->_sApp. LETPHP_DS. $sType. LETPHP_DS. str_replace('.', LETPHP_DS, $sClass). '.'. $mType;	

			if((!$bExists) && file_exists(LETPHP_LETAPPS. $this->_sApp. LETPHP_DS. $sType. LETPHP_DS. str_replace('.', LETPHP_DS, $sClass). '.'. $mType))
			{
				
				$sFile = LETPHP_LETAPPS. $this->_sApp. LETPHP_DS. $sType. LETPHP_DS. str_replace('.', LETPHP_DS, $sClass). '.'. $mType;
				if($sType !== 'Models')
				{  
					$this->_sControllerView = $this->_sApp. '.'. $sClass; 
				}
				$sObject = $this->_sApp. '_'. str_replace('.php', '', $mType). '_'. str_replace('.', '_', $sClass);
				$bExists = true;
				//echo 'No existe';
			}
			
			if((!$bExists) && (file_exists(LETPHP_LETAPPS. $this->_sApp. LETPHP_DS. $sType. LETPHP_DS. str_replace('.', LETPHP_DS, $sClass).  'index.'. $mType)))
			{
				$sFile = LETPHP_LETAPPS. $this->_sApp. LETPHP_DS. $sType. LETPHP_DS. str_replace('.', LETPHP_DS, $sClass). 'index.'. $mType;
				if($sType !== 'Models')
				{
					$this->_sControllerView = $this->_sApp. '.'. $sClass.'index';  
				}
				$sObject = $this->_sApp. '_'. str_replace('.php', '', $mType). '_'. $sClass. 'Index';
				$bExists = true;

			}
			
			if( (!$bExists) && (file_exists(LETPHP_LETAPPS. $this->_sApp. LETPHP_DS. $sType. LETPHP_DS. 'index.'. $mType)))
			{
				$sFile = LETPHP_LETAPPS. $this->_sApp. LETPHP_DS. $sType. LETPHP_DS. 'index.'. $mType;
				if($sType !== 'Models')
				{  
					$this->_sControllerView = $this->_sApp. '.index';
				}
				$sObject = $this->_sApp. '_'. str_replace('.php', '', $mType). '_Index';
				$bExists = true;
			}
			
		  $sHash = sha1($sClass. str_replace(['Controllers', 'Fragments', 'Models', 'Ajax'], ['controller', 'fragment', 'model', 'ajax'], $sType));
		  if(isset($this->_aControllers[$sHash]))
		  {  
			  $this->_aControllers[$sHash]->__construct(['sApp' => '', 'sDirController' => '', 'aParams' => $aParams]);
		  }
		  else
		  {			
			  if(($bExists))
			  {
				  require($sFile);
				  $this->_aControllers[$sHash] = LetPHP::getObject($sObject, ['sApp' => $this->_sApp, 'sDirController' => '', 'aParams' => $aParams]);
				  
				  $mReturn = 'empty';
				  if($sType !== 'Ajax' AND $sType !== 'Models' ){ $mReturn = $this->_aControllers[$sHash]->start(); }
				  if($sType === 'Fragments')
			    {				   
				    $sViewFragment = $this->_sControllerView; 
						LetPHP::getClass('view')->getViewApp($sViewFragment);
			    }
			    
				  $this->_aReturn[$sClass] = $mReturn;
			    
			  } 
		  }
	  
	  return $this->_aControllers[$sHash];
  }
  
  /** 
	  * Regresa el nombre de la aplicacion actual
	  * @return string Nombre de la aplicacion actual
	*/
  public function getAppName(): string
  {
	  return $this->_sApp;
  }
  
  /**
	 * Asignamos la vista del controlador cargado.
   * @return void
   */
  public function getControllerViewApp()
  {
	  /*if($this->_sController == '')
	  {
		  $this->_sController = 'index';
	  }*/
	  $sClass = $this->_sControllerView; //$this->_sApp . '.Controllers.' . $this->_sController;
	  //$sClass =  'Controllers.'. $this->_sApp;
	  if (isset($this->_aReturn[$sClass]) && $this->_aReturn[$sClass] === false)
		{
			return false;
		}
		
		// Obtenemos la Vista del Controlador
		LetPHP::getClass('letphp.view')->getViewApp($sClass);

		// Check if the component we have loaded has the clean() method
		/*if (is_object($this->_oController) && method_exists($this->_oController, 'clean'))
		{
			// This method is used to clean out any garbage we don't need later on in the script. In most cases Template assigns.
			$this->_oController->clean();
		}*/
		
	}
	
	/*public function getFragment(string $sFragment, array $aParams = [])
	{
		echo $sFragment;
	}*/
	
	/**
	 * Obtenemos un Model de la Aplicación
	 * @param string $sModel Nombre del Modelo
	 * @param array $aParams Valores por default
	*/
  public function getModel(string $sModel = '', array $aParams = [])
  {
	  
    if (isset($this->_aModels[$sModel]))
		{
			return $this->_aModels[$sModel];	
		}		
		
		$sFileModel = '';
		$sObject = '';
		$bExistsModel = false;
		
		### Buscamos por el metodo de Aplicaciones
		$aPartsModel = explode('.', $sModel);
		$sApp = $aPartsModel[0]; 
		//d($aPartsModel);
		$aPartsModel = (array_slice($aPartsModel, 1));
		$sModel = implode('.', $aPartsModel);
		$sModel = str_replace('.', LETPHP_DS, $sModel);
		if($sModel == '')
		{
			$sModel = $sApp;
		}
		
		//echo LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_MODELS. LETPHP_DS. 'index.'. LETPHP_APP_SUFFIX_MODEL;
		
		if((file_exists(LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_MODELS. LETPHP_DS. 'index.'. LETPHP_APP_SUFFIX_MODEL)))
		{
			//echo '<br/>OPT1';
			$sFileModel = LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_MODELS. LETPHP_DS. 'index.'. LETPHP_APP_SUFFIX_MODEL;
			$sObject = $sApp. LETPHP_OBJECT_MODEL. 'Index';	
			$bExistsModel = true;
		}
		else if(file_exists(LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_MODELS. LETPHP_DS. $sModel. '.'. LETPHP_APP_SUFFIX_MODEL))
		{
			//echo '<br/>OPT2';
			$sFileModel = LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_MODELS. LETPHP_DS. $sModel. '.'. LETPHP_APP_SUFFIX_MODEL;
			$sObject = $sApp. LETPHP_OBJECT_MODEL. str_replace(LETPHP_DS, '_', $sModel);
			$bExistsModel = true;
		}
		else if(file_exists(LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_MODELS. LETPHP_DS. $sModel. LETPHP_DS. 'index.'. LETPHP_APP_SUFFIX_MODEL))
		{
			//echo '<br>OPT3';
			$sFileModel = LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_MODELS. LETPHP_DS. $sModel. LETPHP_DS. 'index.'. LETPHP_APP_SUFFIX_MODEL;
			$sObject = $sApp. LETPHP_OBJECT_MODEL. str_replace(LETPHP_DS, '_', $sModel). '_Index';
			$bExistsModel = true;
		}
		else if(file_exists(LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_MODELS. LETPHP_DS. $sModel. LETPHP_DS. $sModel. '.'. LETPHP_APP_SUFFIX_MODEL))
		{
			//echo '<br>OPT4';
			$sFileModel = LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_MODELS. LETPHP_DS. $sModel. LETPHP_DS. $sModel. '.'. LETPHP_APP_SUFFIX_MODEL;
			$sObject = $sApp. LETPHP_OBJECT_MODEL. $sModel. '_'. $sModel;
			$bExistsModel = true;
		}
		
		
		if(!$bExistsModel)
		{
			//echo $sModel;
			//echo '<br/>'. LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_MODELS. $sModel; //'.'. LETPHP_APP_SUFFIX_MODEL;
		}
		
		
		/*echo '<br><br>'.LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_MODELS. $sModel. '.'. LETPHP_APP_SUFFIX_MODEL;
		if((!$bExistsModel) && (file_exists(LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_MODELS. $sModel. '.'. LETPHP_APP_SUFFIX_MODEL)))
		{
			$sFileModel = LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_MODELS. $sModel. '.'. LETPHP_APP_SUFFIX_MODEL;
			$sObject = $sApp. LETPHP_OBJECT_MODEL. str_replace(LETPHP_DS, '_', $sModel);	
			$bExistsModel = true; 
		}
		
		
		if((!$bExistsModel) && (file_exists(LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_MODELS. $sModel. LETPHP_DS. 'index.'. LETPHP_APP_SUFFIX_MODEL)))
		{
			
			echo 'existe';
			$sFileModel = LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_MODELS. $sModel. LETPHP_DS. 'index.'. LETPHP_APP_SUFFIX_MODEL;
			$sObject = $sApp. LETPHP_OBJECT_MODEL. $sModel;	
			$bExistsModel = true; 
		}
		
		//echo '<br><br/>'. LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_MODELS. $sModel. LETPHP_DS. 'index.'. LETPHP_APP_SUFFIX_MODEL;
		*/
		
		if($bExistsModel)
		{		
			require_once($sFileModel);
			$this->_aModels[$sModel] = LetPHP::getObject($sObject);		
			return $this->_aModels[$sModel]; 
		}
		
		//d($this->_aModels)
		
		//= LetPHP::getObject($sApp . '_Model_' . $sModel);		
		//if()
		/*if (preg_match('/\./', $sModel) && ($aParts = explode('.', $sModel)) && isset($aParts[1]))
		{
			$sApp = $aParts[0];
			$sModel = $aParts[1];			
		}
		else 
		{
			$sApp = $sModel;
			$sModel = $sModel;
    }
    
    
    /*
    $sFile = LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_MODELS. $sModel. '.'. LETPHP_APP_SUFFIX_MODEL;
		if (!file_exists($sFile))
		{	
			if (isset($aParts[2]))
			{
				$sFile = LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_MODELS. $sModel. LETPHP_DS.$aParts[2].'.'. LETPHP_APP_SUFFIX_MODEL;
				if (!file_exists($sFile))
				{
					if (isset($aParts[3]) && file_exists(LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_MODELS. $sModel. LETPHP_DS. $aParts[2]. LETPHP_DS. $aParts[3]. '.'. LETPHP_APP_SUFFIX_MODEL))
					{
						$sFile = LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_MODELS. $sModel. LETPHP_DS. $aParts[2]. LETPHP_DS. $aParts[3]. '.'.LETPHP_APP_SUFFIX_MODEL;				
						$sModel .= '_' . $aParts[2] . '_' . $aParts[3];
					}
					else 
					{		
						$sFile = LETPHP_LETAPPS. $sApp. LETPHP_LETAPPS_MODELS. $sModel. LETPHP_DS. $aParts[2].LETPHP_DS . $aParts[2] . '.'. LETPHP_APP_SUFFIX_MODEL;				
						$sModel .= '_' . $aParts[2] . '_' . $aParts[2];
					}
				}
				else 
				{
					$sModel .= '_' . $aParts[2];
				}
			}
			else 
			{
				//echo $sFile = LETPHP_LETAPPS. $sModel. '.'. LETPHP_APP_SUFFIX_MODEL;
				if(file_exists($sFile))
				{				
					$sModel .= '_'. 'Model';
					require_once($sFile);
					$this->_aModels[$sModel] = LetPHP::getObject($sModel);			
				}
				else
				{
					$sFile = LETPHP_LETAPPS. 'Models'. LETPHP_DS.  $sModel. '.'. LETPHP_APP_SUFFIX_MODEL;
					require_once($sFile);
					$sModel = 'Model_'. $sModel;
					$this->_aModels[$sModel] = LetPHP::getObject($sModel);			
				}
				return $this->_aModels[$sModel];
			}
    }	
    require_once($sFile);
		$this->_aModels[$sModel] = LetPHP::getObject($sApp . '_model_' . $sModel);	
		return $this->_aModels[$sModel];*/
  }
  
  public static function getInstance()
  {
	  return LetPHP::getClass('letphp.app');
  }

}

?>