<?php
/** Class LetPHP_View */
defined('LETPHP') or exit('NO EXISTE LETPHP');

/**
 * Clase encargada para administrar la información para pasar a la vista, 
 * convierte todo el código HTML en PHP y lo guarda en caché.
 * Algunas características que contiene esta clase son :
 * 
 * - Pasa las variables hacia la vista
 * - Sobre escribe el titulo de la pagina
 * - Crea etiquetas HTML meta 
 * - Carga Archivos de Javascript
 * - Carga CSS
 * 
 * @copyright LetCode IO
 * @author Rodrigo Hernández Ortiz
 * @package LetPHP\View
 * @version .1
 */
class LetPHP_View
{
	
	/** @var array $_aVars Arreglo para guardar las variables que se ejecutan durante la clase */
	private $_aVars = [];

	/** @var array $_aMeta Arreglo para guardar Meta Datos.*/
	private $_aMeta = [];
	
	/** @var array $_aTitles Arreglo para guardar los valores de los titulos. */
	private $_aTitles = [];
	
	/** @var array $_aCss Arreglo para guardar los estilos css. */	
	private $_aCss = [];
	
	
	/** @var array $_aJs Arreglo para guardar los estilos css. */	
	private $_aJs = [];

	/** @var string $sDisplayView Vista Inicial del sitio. */
  public $sDisplayView = '';

	/** @var string $_sUI Nombre del tema de nuestro sitio. */
  private $_sUI = '';

  /**
   * Cargamos el tema por defecto de LetPHP y la vista.
   */
  public function __construct()
  {
    $this->_sUI = LetPHP::getConfig('main.site_theme');
    $this->sDisplayView = 'start';
    
  }
  
  
	  
	  /**
     * Sobre escribimos  el Titulo de la pestaña del browser.
	   * @param string $sTitle Valor del titulo 
	   * @return object
	   */
	  public function setTitle(string $sTitle = ''): object
	  {
		  $this->_aTitles[] = $sTitle;
		  return $this;
	  }
	  
	  /**
     * Obtenemos el titulo para la pestaña del browser.
	   * @return string $sData 
	   */
	  public function getTitle()
	  {
		  
		  $sData = LetPHP::getConfig('main.site_title'). ' ';
		  
		  foreach($this->_aTitles AS $iKey => $mTitle )
		  {
			  $sData .= LetPHP::getConfig('main.site_title_delimiter'). ' '. $mTitle. ' ';
		  }
		  
		  //$sData = rtrim($sData, LetPHP::getConfig('main.site_title_delimiter'));
		  return $sData;
	  }
  
	/**
   * Asignamos el valor del Meta Tag.
	 * @param mixed $aParams Arreglo de la etiqueta tag
	 * @param string $mValue el valor de la etiqueta tag, si el primer parametro es de valor string
	 * 
	 * @return object
	 */
	public function setMeta($aParams, $mValue = ''): object
	{
		
		if(!is_array($aParams))
		{
			$aParams = [$aParams => $mValue];
		}
		
		$this->_aMeta = $aParams;
		return $this;
	}
	
	/**
   * Obtenemos los valores de las meta tags.
	 * @return string
	 */
	public function getMeta()
	{
		$sMeta = "";
		foreach($this->_aMeta AS $iKey => $mMeta)
		{
			$sMeta .= "\t\t<meta name='".$iKey."' content='".$mMeta."' />";
		}
		$this->_aMeta = [];
		return $sMeta;
	}
  
  
  
  /**
   * Asignamos  valores Javascript para nuestro sitio
   * @param mixed $aParams Arreglo de los valores Javascript
   * @param string $mValue Valor del Javascript en caso de que el primer valor sea string
   * 
   * @return object
   */
  public function setJScript($aParams, string $mValue = ''):object
  {
	  if(!is_array($aParams))
	  {
		  $aParams = [$aParams => $mValue];
	  }
	  
	  foreach($aParams as $iKeyJs => $mValueJs )
		{
			if(strpos($iKeyJs, '.js'))
			{
				$mValueJs = str_replace('@', '_', $mValueJs);
				$aApp = explode('_', $mValueJs);	
				
				if((isset($aApp[0])) && ($aApp[0] === 'app'))
				{
					if((isset($aApp[1])) && ($aApp[1] != '' ))
					{
					 $this->_aJs[] =	LETPHP_DS. $aApp[1]. LETPHP_DS. 'Public/jscript/'. $iKeyJs;
					}
				}
				elseif($mValueJs === 'ui')
				{
					
					$this->_aJs[] = $this->_sUI. LETPHP_DS. 'public/jscript/'. $iKeyJs.'@ui';
				}
				elseif($mValueJs === 'site')
				{
					$this->_aJs[] = 'public/jscript/'. $iKeyJs.'@site';
				}

			}	
			elseif(strpos($mValueJs, 'script'))
			{
				$this->_aJs[] = $mValueJs;
				
			}
			else 
			{
				$this->_aJs[] = $mValueJs;
			}
		}
	  return $this;
  }
  
  /**
   * Obtenemos los valores de Javascript y Librerias Javascript externas
   * @return string
   */
  public function getJScript(): string
  {
	  $sDirJs = rtrim(LETPHP_LETAPPS, '/');
	  $sLink = "";
	  $oRouter = LetPHP::getClass('letphp.router');
	  // Insertamos configuraciones de LetPHP para ser Utilizadas en Javascript
	  $aLetPHPJavascript = [
			'sLtHome' => LetPHP::getConfig('main.path'),
			'sLtHostName' => LetPHP::getConfig('main.host'),
		  'sLtSiteTitle' => LetPHP::getConfig('main.site_title'),
		  'sLtRouteAjax' => $oRouter->getDomain(). 'LetSite'. LETPHP_DS. 'ajax.php',
		  'sLtGlobalTokenName' => LetPHP::getConfig('main.session_prefix'),
		  'sLtToken' => LetPHP::getConfig('main.token'),
		  'iLtAjaxRefresh' => 1,
	  ];
	  
	  $sLetPHPJavascript =  'const $LetPHP = {};'. "\n";
	  $sLetPHPJavascript .=  'const $aLetParams = {'. "\n";
	  $iCnt = 0;
	  foreach($aLetPHPJavascript as $mKey => $mValue)
	  {
		  $iCnt++;
		  
		  if($iCnt !== 1)
		  {
			  $sLetPHPJavascript .= ",";
		  }
		  
		  if(is_bool($mValue))
		  {
			  $sLetPHPJavascript .= "'{$mKey}': " . ($mValue ? 'true' : 'false');	
		  }
		  elseif(is_numeric($mValue))
		  {
		  	$sLetPHPJavascript .= "'{$mKey}': ". $mValue;
		  }
		  else
		  {
			  $sLetPHPJavascript .= "'{$mKey}': '" . str_replace("'", "\'", $mValue) . "'";	
		  }
	  }
	  $sLetPHPJavascript .= "};";
	  $sLink .= "\n\t\t<script>\n";
	  $sLink .= $sLetPHPJavascript;
	  $sLink .= "\t\t</script>";
	  
		foreach($this->_aJs as $iKey => $mValueJs)
	  {
		  
		  $sFileJs = $sDirJs. $mValueJs;
		  if(file_exists($sFileJs))
		  {
			  $sLink .= "\t\t<script src='".str_replace(LETPHP_DIR_PARENT, LetPHP::getConfig('main.path'), $sFileJs)."' ></script>"; 
		  }
		  else
		  {
			  if(strpos($sFileJs, 'script'))
			  {
				 	if(strpos($mValueJs, '@ui'))
				 	{
					 	
					 	$sFileJs = str_replace('@ui', '', $mValueJs);
					 	$sFileJs = LETPHP_LETSITE_UI. $sFileJs;	
					 	if(file_exists($sFileJs))
					 	{
						 	$sLink .= "\t\t<script src='".str_replace(LETPHP_DIR_PARENT, LetPHP::getConfig('main.path'), $sFileJs)."' ></script>"; 
					 	}
				 	}
				 	elseif(strpos($mValueJs, '@site'))
				 	{
					 	$sFileJs = str_replace('@site', '', $mValueJs);
					 	$sFileJs = LETPHP_LETSITE. $sFileJs;	
					 	if(file_exists($sFileJs))
					 	{
						 	$sLink .= "\t\t<script src='".str_replace(LETPHP_DIR_PARENT, LetPHP::getConfig('main.path'), $sFileJs)."' ></script>"; 
					 	}
					 	
				 	}
				 	else
				 	{
				  	$sLink .= "\t\t". $mValueJs;	 	
				 	}
				
			  }
			  else
			  {
				  $sLink .= "\t\t<script src='". str_replace( LETPHP_DIR_PARENT, LetPHP::getConfig('main.path'), $sDirJs). LETPHP_DS. $mValueJs."'></script>";
			  }
		  } 
	  }
	  
	  
	  $this->_aJs[] = null;
	  return $sLink;
  }
  
  
  /**
   * Asignamos valores CSS internos o externos
   * @param mixed $aParams Arreglo para guardar los archivos CSS
   * @param string $mValue Valor del CSS, en caso de que el 1er parametro sea string
   * 
   * @return object
   */
  public function setCss($aParams, string $mValue = ''): object
  {
	  
	  if(!is_array($aParams))
	  {
		  $aParams = [$aParams => $mValue];
	  }
	  
		foreach($aParams as $iKeyCss => $mValueCss )
		{
			if(strpos($iKeyCss, '.css'))
			{	
				$mValueCss = str_replace('@', '_', $mValueCss);
				$aApp = explode('_', $mValueCss);	

				if((isset($aApp[0])) && ($aApp[0] === 'app'))
				{
					if((isset($aApp[1])) && ($aApp[1] != '' ))
					{
						if($this->_sUI != '')
						{
					 		$this->_aCss[] =	LETPHP_DS. $aApp[1]. LETPHP_DS. 'Public/css/'. $this->_sUI. LETPHP_DS. $iKeyCss;	
						}
						else
						{
							$this->_aCss[] =	LETPHP_DS. $aApp[1]. LETPHP_DS. 'Public/css/'. $iKeyCss;	
						}
					}
				}
				elseif($mValueCss == 'site')
				{
					
					$this->_aCss[] = '/public/css/'. $iKeyCss.'@site';
				}
				elseif($mValueCss == 'ui')
				{
					$this->_aCss[] = $this->_sUI. LETPHP_DS. 'public/css/'. $iKeyCss.'_ui';
				}
			}
			elseif(strpos($mValueCss, 'link'))
			{
				$this->_aCss[] = $mValueCss;
			}
			else 
			{
				$this->_aCss[] = $mValueCss;
			}
		}
		return $this;
  }
  
  /**
   * Cargamos los valores de CSS interno y externo
   * @return string
   */
  public function getCss():string
  {
	  $sDirCss = rtrim(LETPHP_LETAPPS, '/');
	  $sLink = "";
	  
	  
	  foreach($this->_aCss as $iKey => $mValueCss)
	  {
		  $sFileCss = $sDirCss.$mValueCss;
		  if(file_exists($sFileCss))
		  {
			  // Si existe el archivo y el tema
			  $sLink .= "\t\t<link rel='stylesheet' href='".str_replace( LETPHP_DIR_PARENT, LetPHP::getConfig('main.path'), $sFileCss)."' />"; 
		  }
		  else
		  {
			  // Si no existe el archivo , revisamos en el tema por default
			  $sFileCss = str_replace($this->_sUI, 'letphp', $sFileCss);
			  
			  if(file_exists($sFileCss))
			  {				 
				  $sLink .= "\t\t<link rel='stylesheet' href='".str_replace( LETPHP_DIR_PARENT, LetPHP::getConfig('main.path'), $sFileCss)."' />";  
			  }
			  elseif(strpos($mValueCss, '@site'))
				{
					$sFileCss = str_replace('@site', '', $mValueCss);
					$sFileCss = LETPHP_LETSITE. ltrim($sFileCss, LETPHP_DS);	
					if(file_exists($sFileCss))
					{
						$sLink .= "\t\t<link rel='stylesheet' href='".str_replace(LETPHP_DIR_PARENT, LetPHP::getConfig('main.path'), $sFileCss)."' />"; 
					}
					 	
				}
			  elseif(strpos($mValueCss, '_ui'))
			  {
				  $mValueCss = str_replace('_ui', '', $mValueCss);
				  $sFileCss = LETPHP_LETSITE_UI. $mValueCss;
				  if(file_exists($sFileCss))
				  {
					  $sLink .= "\t\t<link rel='stylesheet' href='".str_replace(LETPHP_DIR_PARENT, LetPHP::getConfig('main.path'), $sFileCss)."' />";
				  }
			  }
			  else
			  {
				  // Agregamos la Tag Link con sus respectivos atributos
				  if(strpos($mValueCss, 'link'))
				  {
				  	$sLink .= "\t\t".$mValueCss; 
				  }
				  else
				  {
					  $sFileCss = $sDirCss. LETPHP_DS. $mValueCss;
					  if(file_exists($sFileCss))
					  {
						  $sFileCss = str_replace(LETPHP_DIR_PARENT, LetPHP::getConfig('main.path'), $sDirCss). LETPHP_DS.$mValueCss;
						  $sLink .= "\t\t<link rel='stylesheet' href='".$sFileCss."' />";
					  }
					 	//echo $mValueCss; 
					  //$mValueCss = str_replace($this->_sUI. '', '/', $mValueCss);
	//				  $sLink = "\t\t<link rel='stylesheet' href='".LetPHP::getConfig('main.path'). 'LetApps'. $mValueCss."' />";
				  }
				  
			  }
			  
			  
			  
		  }
	  }
	  
	  $this->_aCss[] = null;
	  return $sLink;
  }
  
	/**
   * Asignamos valores a la vista
	 * @param array $mParams Variable mixta, puede ser un Array/String para pasar a la vista.
	 * @param string $sValue Valor de la variable, en caso de que el 1er argumento sea string
	 * 
	 * @return object
	 */
	public function setValues($mParams, string $sValue = ''): object
	{
		if (!is_array($mParams)){ $mParams = array($mParams => $sValue); }
		foreach ($mParams as $sVar => $sValue){ $this->_aVars[$sVar] = $sValue; }
		return $this;
	}
	
	/**
   * Obtenemos una variable, cargada en el metodo setValues()
	 * @param string $sVar Nombre de la variable que vamos a cargar
	 * 
	 * @return mixed
	 */
	public function getValues(string $sVar = '')
	{
		return ((isset($this->_aVars[$sVar]))? $this->_aVars[$sVar] : '');
	}
	
  /**
   * Obtenemos el archivo de la vista de la app
   * @param string $sViewApp Nombre de la vista para la app
   * @param bool $bCode 
   * 
   * @return string
   */
  public function getViewApp(string $sViewApp = '', bool $bCode = false)
  {
		$sViewApp = $this->getViewAppFile($sViewApp);
		$this->_getViewFromCache($sViewApp);
		
    if($bCode)
    {
	    $sCode = ob_get_contents();
	    ob_clean();
	    return $sCode;
    }
	}
  
  /**
   * Asignamos la vista de la carpeta LetApps
   * @param string $sViewApp Nombre del archivo de la vista.
   * 
   * @return string
   */
  public function getViewAppFile(string $sViewApp): string
  {
	  $sFileName = str_replace('.', LETPHP_DS, $sViewApp);
	  $bExists = false;
	 
	 //echo '<br><br>'. LETPHP_LETAPPS_CONTROLLERS. LETPHP_LETAPPS_FRAGMENTS;
	 //echo '<br><br><br>'. $sFileName;
	 /*echo '<br>'. LETPHP_LETAPPS. str_replace(LETPHP_DS.'index', '', $sFileName). '.'. LETPHP_APP_SUFFIX_VIEW;
	 echo '<br/>'. LETPHP_LETAPPS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW;
	 echo '<br/>'. LETPHP_LETAPPS. $sFileName. LETPHP_DS. 'index.'. LETPHP_APP_SUFFIX_VIEW;
	 echo '<br/>'. LETPHP_LETAPPS. $sFileName. LETPHP_DS. 'index.'. LETPHP_APP_SUFFIX_VIEW;
	 echo '<br>'. LETPHP_LETAPPS. LETPHP_LETAPPS_VIEWS. str_replace([LETPHP_LETAPPS_CONTROLLERS.'/', LETPHP_LETAPPS_FRAGMENTS.'/'], '', $sFileName). '.'. LETPHP_APP_SUFFIX_VIEW;
	 echo '<br>'. LETPHP_LETAPPS. LETPHP_LETAPPS_VIEWS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW;
	 echo '<br>'. LETPHP_LETAPPS. LETPHP_LETAPPS_VIEWS. str_replace('/index', '', $sFileName). '.'.LETPHP_APP_SUFFIX_VIEW;
	 echo '<br>'. LETPHP_LETAPPS. LETPHP_LETAPPS_VIEWS. $sFileName. LETPHP_DS. 'index'. '.'. LETPHP_APP_SUFFIX_VIEW;
	 
	 echo '<br>'. LETPHP_LETAPPS. LETPHP_LETAPPS_VIEWS. $this->_sUI. LETPHP_DS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW;
	 echo '<br>'. LETPHP_LETAPPS. LETPHP_LETAPPS_VIEWS. $this->_sUI. LETPHP_DS. str_replace('/index', '', $sFileName). '.'.LETPHP_APP_SUFFIX_VIEW;
	 echo '<br>'. LETPHP_LETAPPS. LETPHP_LETAPPS_VIEWS. $this->_sUI. LETPHP_DS. $sFileName. LETPHP_DS. 'index'. '.'. LETPHP_APP_SUFFIX_VIEW;*/
	 
	  // Existe Archivo con estructura básica y Controladores
	  //echo '<br>'. LETPHP_LETAPPS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW;
	  if(file_exists(LETPHP_LETAPPS. str_replace(LETPHP_DS.'index', '', $sFileName). '.'. LETPHP_APP_SUFFIX_VIEW))
	  {
		  $sFileView = LETPHP_LETAPPS. str_replace(LETPHP_DS.'index', '', $sFileName). '.'. LETPHP_APP_SUFFIX_VIEW;
		  $bExists = true;
	  }
	  elseif(file_exists( LETPHP_LETAPPS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW))
	  {
		 	$sFileView =  LETPHP_LETAPPS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW;
		  $bExists = true;
	  }
	  elseif(file_exists(LETPHP_LETAPPS. $sFileName. LETPHP_DS. 'index.'. LETPHP_APP_SUFFIX_VIEW))
	  {
		  $sFileView = LETPHP_LETAPPS. $sFileName. LETPHP_DS. 'index.'. LETPHP_APP_SUFFIX_VIEW;
		  $bExists = true;
	  }
	  elseif(file_exists(LETPHP_LETAPPS. LETPHP_LETAPPS_VIEWS. str_replace([LETPHP_LETAPPS_CONTROLLERS.'/', LETPHP_LETAPPS_FRAGMENTS.'/'], '', $sFileName). '.'. LETPHP_APP_SUFFIX_VIEW))
	  {
		  $sFileView = LETPHP_LETAPPS. LETPHP_LETAPPS_VIEWS. str_replace([LETPHP_LETAPPS_CONTROLLERS.'/', LETPHP_LETAPPS_FRAGMENTS.'/'], '', $sFileName). '.'. LETPHP_APP_SUFFIX_VIEW;
		  $bExists = true;
	  }
	  elseif(file_exists(LETPHP_LETAPPS. LETPHP_LETAPPS_VIEWS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW))
	  {
		  $sFileView = LETPHP_LETAPPS. LETPHP_LETAPPS_VIEWS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW;
		  $bExists = true;
	  }
	  elseif(file_exists(LETPHP_LETAPPS. LETPHP_LETAPPS_VIEWS. str_replace('/index', '', $sFileName). '.'.LETPHP_APP_SUFFIX_VIEW))
	  {
		  $sFileView = LETPHP_LETAPPS. LETPHP_LETAPPS_VIEWS. str_replace('/index', '', $sFileName). '.'.LETPHP_APP_SUFFIX_VIEW;
		  $bExists = true;
		}
		elseif(file_exists(LETPHP_LETAPPS. LETPHP_LETAPPS_VIEWS. $sFileName. LETPHP_DS. 'index'. '.'. LETPHP_APP_SUFFIX_VIEW))
		{
			$sFileView = LETPHP_LETAPPS. LETPHP_LETAPPS_VIEWS. $sFileName. LETPHP_DS. 'index'. '.'. LETPHP_APP_SUFFIX_VIEW;
			$bExists = true;
		}
		elseif(file_exists(LETPHP_LETAPPS. LETPHP_LETAPPS_VIEWS. $this->_sUI. LETPHP_DS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW))
		{
			$sFileView = LETPHP_LETAPPS. LETPHP_LETAPPS_VIEWS. $this->_sUI. LETPHP_DS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW;
			$bExists = true;
		}
		elseif(file_exists(LETPHP_LETAPPS. LETPHP_LETAPPS_VIEWS. $this->_sUI. LETPHP_DS. str_replace('/index', '', $sFileName). '.'.LETPHP_APP_SUFFIX_VIEW))
		{
			$sFileView = LETPHP_LETAPPS. LETPHP_LETAPPS_VIEWS. $this->_sUI. LETPHP_DS. str_replace('/index', '', $sFileName). '.'.LETPHP_APP_SUFFIX_VIEW;
			$bExists = true;
		}
		elseif(file_exists(LETPHP_LETAPPS. LETPHP_LETAPPS_VIEWS. $this->_sUI. LETPHP_DS. $sFileName. LETPHP_DS. 'index'. '.'. LETPHP_APP_SUFFIX_VIEW))
		{
			$sFileView = LETPHP_LETAPPS. LETPHP_LETAPPS_VIEWS. $this->_sUI. LETPHP_DS. $sFileName. LETPHP_DS. 'index'. '.'. LETPHP_APP_SUFFIX_VIEW;
			$bExists = true;
		}
		
	  //LETPHP_LETAPPS. $sFileName. LETPHP_DS. 'index.'. LETPHP_APP_SUFFIX_VIEW;
	  if(!$bExists &&  file_exists(LETPHP_LETAPPS. $sFileName. LETPHP_DS. 'index.'. LETPHP_APP_SUFFIX_VIEW))
	  {
		  $sFile = LETPHP_LETAPPS. $sFileName. LETPHP_DS. 'index.'. LETPHP_APP_SUFFIX_VIEW;
			$bExists = true;
	  }
	  
	  // Buscamos en la carpeta Views
	  if((!$bExists) && file_exists(LETPHP_LETAPPS. LETPHP_LETAPPS_VIEWS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW))
	  {
		  $sFile = LETPHP_LETAPPS. LETPHP_LETAPPS_VIEWS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW;
			$bExists = true;
		}
		
		// Buscamos en la carpeta Views por defecto Index
		if((!$bExists) && (file_exists(LETPHP_LETAPPS. LETPHP_LETAPPS_VIEWS. $sFileName. LETPHP_DS. 'index.'. LETPHP_APP_SUFFIX_VIEW)))
		{
			$sFile = LETPHP_LETAPPS. LETPHP_LETAPPS_VIEWS. $sFileName. LETPHP_DS. 'index.'. LETPHP_APP_SUFFIX_VIEW;
			$bExists = true;
		}
		
	  // Buscamos la vista dentro de la carpeta de Controllers
	  if((!$bExists) && file_exists(LETPHP_LETAPPS. LETPHP_LETAPPS_CONTROLLERS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW))
	  {
		  $sFile = LETPHP_LETAPPS. LETPHP_LETAPPS_CONTROLLERS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW;
		  $bExists = true;
	  }
	  
	  // Buscamos la vista dentro de la carpeta de Controllers por defecto Index
	  if((!$bExists) && file_exists(LETPHP_LETAPPS. LETPHP_LETAPPS_CONTROLLERS. $sFileName. LETPHP_DS. 'index.'. LETPHP_APP_SUFFIX_VIEW))
	  {
		  $sFile = LETPHP_LETAPPS. LETPHP_LETAPPS_CONTROLLERS. $sFileName. LETPHP_DS. 'index.'. LETPHP_APP_SUFFIX_VIEW;
		  $bExists = true;
	  }
	  
	  // Buscamos la vista dentro de la carpeta de Fragments
	  if((!$bExists) && (file_exists(LETPHP_LETAPPS. LETPHP_LETAPPS_FRAGMENTS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW)))
	  {
		  $sFile = LETPHP_LETAPPS. LETPHP_LETAPPS_FRAGMENTS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW;
		  $bExists = true;
	  }
	  
	  // Buscamos la vista dentro de la carpeta de Fragments por defecto Index
	  if((!$bExists) && (file_exists(LETPHP_LETAPPS. LETPHP_LETAPPS_FRAGMENTS. $sFileName. LETPHP_DS. 'index.'. LETPHP_APP_SUFFIX_VIEW)))
	  {
		  $sFile = LETPHP_LETAPPS. LETPHP_LETAPPS_FRAGMENTS. $sFileName. LETPHP_DS. 'index.'. LETPHP_APP_SUFFIX_VIEW;
		  $bExists = true;
	  }
	  
	  // Busqueda por Modulos
	  if(LetPHP::getConfig('main.app_core') !== '')
	  {
		  
		  $aPartsViews = explode(LETPHP_DS, $sFileName);
		  $sApp = $aPartsViews[0];
		  $sType = $aPartsViews[1];
		  $aPartsViews = (array_slice($aPartsViews, 1));
		  $aPartsViews = (array_slice($aPartsViews, 1));
		  $sFileName = implode('.', $aPartsViews);
		  $sFileName = str_replace('.', LETPHP_DS, $sFileName);
		  
		  /*
		  // Buscamos en Carpeta de Controllers
		  echo '<br>'. LETPHP_LETAPPS. $sApp. LETPHP_DS. $sType. LETPHP_DS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW;
		  echo '<br>'. LETPHP_LETAPPS. $sApp. LETPHP_DS. $sType. LETPHP_DS. $sFileName. LETPHP_DS. 'index'. '.'. LETPHP_APP_SUFFIX_VIEW;
		  echo '<br>'. LETPHP_LETAPPS. $sApp. LETPHP_DS. $sType. LETPHP_DS. $this->_sUI. LETPHP_DS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW;
		  echo '<br>'. LETPHP_LETAPPS. $sApp. LETPHP_DS. $sType. LETPHP_DS. $this->_sUI. LETPHP_DS. $sFileName. LETPHP_DS. 'index'. '.'. LETPHP_APP_SUFFIX_VIEW;
		  echo '<br><br>------------------';
		  // Buscamos en la Carpeta Views
		  echo '<br>'. LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_VIEWS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW;
		  echo '<br>'. LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_VIEWS. $sFileName. LETPHP_DS. 'index.'. LETPHP_APP_SUFFIX_VIEW;
		  echo '<br>'. LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_VIEWS. $this->_sUI. LETPHP_DS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW;
		  echo '<br>'. LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_VIEWS. $this->_sUI. LETPHP_DS. $sFileName. LETPHP_DS. 'index.'. LETPHP_APP_SUFFIX_VIEW;
		  
		  echo '<br><br>------------------';
		  // Buscamos dentro de la carpeta Views con su respectivo tipo
		  echo '<br>'. LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_VIEWS. $sType. LETPHP_DS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW;
		  echo '<br>'. LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_VIEWS. $sType. LETPHP_DS. $sFileName. LETPHP_DS. 'index.'. LETPHP_APP_SUFFIX_VIEW;
		  
		  echo '<br>'. LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_VIEWS. $this->_sUI. LETPHP_DS. $sType. LETPHP_DS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW;
		  echo '<br>'. LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_VIEWS. $this->_sUI. LETPHP_DS. $sType. LETPHP_DS. $sFileName. LETPHP_DS. 'index.'. LETPHP_APP_SUFFIX_VIEW;*/
		  
		  // Buscar por tema y nombre de archivo
		  // Existe echo '<br>'.LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_VIEWS. $this->_sUI. LETPHP_DS. $sType. LETPHP_DS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW;
		  //echo '<br>'.LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_VIEWS. $this->_sUI. LETPHP_DS. $sType. LETPHP_DS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW;
		  //echo '<br>'.LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_VIEWS. $sType. LETPHP_DS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW;
		  if(file_exists(LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_VIEWS. $this->_sUI. LETPHP_DS. $sType. LETPHP_DS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW))
		  {
			  $sFileView = LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_VIEWS. $this->_sUI. LETPHP_DS. $sType. LETPHP_DS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW;
			  $bExists = true;
		  }
		  else if(file_exists(LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_VIEWS. $sType. LETPHP_DS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW))
		  {
			  $sFileView = LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_VIEWS. $sType. LETPHP_DS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW;
			  $bExists = true;
		  }
		  else
		  {
			  $sFileView = LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_VIEWS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW;
			  $bExists = true;
		  }
		  
		  /*if(file_exists(LETPHP_LETAPPS. $sApp. LETPHP_DS. $sType. LETPHP_DS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW))
		  {
			  // Bien
			  $sFileView = LETPHP_LETAPPS. $sApp. LETPHP_DS. $sType. LETPHP_DS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW;
			  $bExists = true;
		  }
		  elseif(file_exists(LETPHP_LETAPPS. $sApp. LETPHP_DS. $sType. LETPHP_DS. $sFileName. LETPHP_DS. 'index'. '.'. LETPHP_APP_SUFFIX_VIEW))
		  {
			  // Bien 
			 	$sFileView = LETPHP_LETAPPS. $sApp. LETPHP_DS. $sType. LETPHP_DS. $sFileName. LETPHP_DS. 'index'. '.'. LETPHP_APP_SUFFIX_VIEW;
			  $bExists = true; 
		  }
		  elseif(file_exists(LETPHP_LETAPPS. $sApp. LETPHP_DS. $sType. LETPHP_DS. $this->_sUI. LETPHP_DS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW))
		  {
			  // Bien
			  $sFileView = LETPHP_LETAPPS. $sApp. LETPHP_DS. $sType. LETPHP_DS. $this->_sUI. LETPHP_DS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW;
			  $bExists = true;
		  }
		  elseif(file_exists(LETPHP_LETAPPS. $sApp. LETPHP_DS. $sType. LETPHP_DS. $this->_sUI. LETPHP_DS. $sFileName. LETPHP_DS. 'index'. '.'. LETPHP_APP_SUFFIX_VIEW))
		  {
			  // Bien
			  $sFileView = LETPHP_LETAPPS. $sApp. LETPHP_DS. $sType. LETPHP_DS. $this->_sUI. LETPHP_DS. $sFileName. LETPHP_DS. 'index'. '.'. LETPHP_APP_SUFFIX_VIEW;
			  $bExists = true;
		  }
		  // Buscamos en la Carpeta Views
		  elseif(file_exists(LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_VIEWS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW))
		  {
			  // Bien
			  $sFileView = LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_VIEWS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW;
			  $bExists = true;
		  }
		  elseif(file_exists(LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_VIEWS. $sFileName. LETPHP_DS. 'index.'. LETPHP_APP_SUFFIX_VIEW))
		  {
			  // Bien
			  $sFileView = LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_VIEWS. $sFileName. LETPHP_DS. 'index.'. LETPHP_APP_SUFFIX_VIEW;
			  $bExists = true;
		  }
		  elseif(file_exists(LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_VIEWS. $this->_sUI. LETPHP_DS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW))
		  {
			  // Bien
			  $sFileView = LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_VIEWS. $this->_sUI. LETPHP_DS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW;
			  $bExists = true;
		  }
		  elseif(file_exists(LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_VIEWS. $this->_sUI. LETPHP_DS. $sFileName. LETPHP_DS. 'index.'. LETPHP_APP_SUFFIX_VIEW))
		  {
			  // Bien
			  $sFileView = LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_VIEWS. $this->_sUI. LETPHP_DS. $sFileName. LETPHP_DS. 'index.'. LETPHP_APP_SUFFIX_VIEW;
			  $bExists = true;
		  }
		  // Buscamos dentro de la carpeta Views con su respectivo tipo
		  elseif(file_exists(LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_VIEWS. $sType. LETPHP_DS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW))
		  {
			  // Bien
			  $sFileView = LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_VIEWS. $sType. LETPHP_DS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW;
			  $bExists = true;
		  }
		  elseif(file_exists(LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_VIEWS. $sType. LETPHP_DS. $sFileName. LETPHP_DS. 'index.'. LETPHP_APP_SUFFIX_VIEW))
		  {
			  // Bien
			  $sFileView = LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_VIEWS. $sType. LETPHP_DS. $sFileName. LETPHP_DS. 'index.'. LETPHP_APP_SUFFIX_VIEW;
			  $bExists = true;
			}
			elseif(file_exists(LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_VIEWS. $this->_sUI. LETPHP_DS. $sType. LETPHP_DS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW))
			{
				// Bien
				$sFileView = LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_VIEWS. $this->_sUI. LETPHP_DS. $sType. LETPHP_DS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW;
				$bExists = true;
			}
			elseif(file_exists(LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_VIEWS. $this->_sUI. LETPHP_DS. $sType. LETPHP_DS. $sFileName. LETPHP_DS. 'index.'. LETPHP_APP_SUFFIX_VIEW))
			{
				// Bien
				$sFileView = LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_VIEWS. $this->_sUI. LETPHP_DS. $sType. LETPHP_DS. $sFileName. LETPHP_DS. 'index.'. LETPHP_APP_SUFFIX_VIEW;
				$bExists = true;
			}
			else 
			{
				//404.html
				$bExists = false;
			}
			//echo '<br>'. LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_VIEWS. $this->_sUI. LETPHP_DS. $sType. LETPHP_DS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW;
		  //echo '<br><br>'. LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_VIEWS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW;
		  	*/	  
		  if($bExists && file_exists($sFileView))
		  {
			  return $sFileView;
		  }
		  
		  
		  
		  exit;
		  $aParts = explode(LETPHP_DS, $sFileName);
		  $sApp = $aParts[0];
		  $aParts = (array_slice($aParts, 1));
		  $sFileName = implode('.', $aParts);
		  $sFileName = str_replace('.', LETPHP_DS, $sFileName);
		  
		  //echo '<br><br>'. $sFileName;
		  
		  if((!$bExists) && (file_exists(LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_VIEWS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW)))
		  {
			  $sFile = LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_VIEWS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW;
			  $bExists = true;
		  }
		  
		  if( (!$bExists) && (file_exists(LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_CONTROLLERS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW)))
		  {
			  $sFile = LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_CONTROLLERS. $sFileName. '.'. LETPHP_APP_SUFFIX_VIEW;
			  $bExists = true;
		  }
		  
	  }
	  
	  
	  
	  if($bExists && file_exists($sFileView))
	  {
		  return $sFileView;
	  }
	  
	  exit;
	  
	  //echo $sViewApp;
    $aParts = explode('.', $sViewApp);
    $sApp = $aParts[0];
    unset($aParts[0]);		
		$sName = implode('.', $aParts);		
		$sName = str_replace('.', LETPHP_DS, $sName);

    $bExists = false;		
    
    $sFile = '';
    // Comprobamos que exista el archivo con el Tema por default
    if(file_exists( LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_VIEWS. $this->_sUI. LETPHP_DS. $sName.'.'. LETPHP_APP_SUFFIX_VIEW))
    {
      $sFile = LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_VIEWS. $this->_sUI. LETPHP_DS. $sName.'.'. LETPHP_APP_SUFFIX_VIEW;
			$bExists = true;
    }
    if(!$bExists && file_exists(LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_VIEWS. $sName. '.'. LETPHP_APP_SUFFIX_VIEW))
    {
	    
	    $sFile = LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_VIEWS. $sName. '.'. LETPHP_APP_SUFFIX_VIEW;
	    $bExists = true;
    }
    
    
    //echo $sFile = LETPHP_LETAPPS. $sApp. $sName. '.'. LETPHP_APP_SUFFIX_VIEW;
    if(!$bExists && file_exists(LETPHP_LETAPPS. $sApp. $sName. '.'. LETPHP_APP_SUFFIX_VIEW) )
    {
			$sFile = LETPHP_LETAPPS. $sApp. $sName. '.'. LETPHP_APP_SUFFIX_VIEW ;
			$bExists = true;
    }
    
    if(!$bExists && file_exists(LETPHP_LETAPPS. $sApp. '.'. LETPHP_APP_SUFFIX_VIEW))
    {
	    $sFile = LETPHP_LETAPPS. $sApp. '.'. LETPHP_APP_SUFFIX_VIEW;
	    $bExists = true;
    }
    
    
    if(!isset($sFile) && $bExists)
    {
	    $sFile = LETPHP_LETAPPS. $sApp. LETPHP_DS. LETPHP_LETAPPS_VIEWS. 'letphp'. LETPHP_DS. $sName. '.'. LETPHP_APP_SUFFIX_VIEW;
    }
    
    return $sFile;
  }

  /**
   * Asignamos el nombre de la Vista general de nuestro sitio
   * @param string $sViewName Nombre de la vista
   * 
   * @return object
   */
  public function setView(string $sViewName = ''): object
  {
    $this->sDisplayView = $sViewName;
    return $this;
  }

  /**
   * Cargamos la vista global de nuestro Sitio.
   * @param string $sView Nombre de la vista
   * @param bool $bCode
   * 
   * @return string
   */
  public function getView(string $sView = '', bool $bCode = false)
  {
	  $this->_getViewFromCache($this->getViewFile($sView));
    
    if($bCode)
    {
	    $sCode = ob_get_contents();
	    ob_clean();
	    return $sCode;
    }
  }

  /**
   * Obtenemos el nombre del archivo para la vista y comprobamos si existe.
   * @param string $sViewFile Nombre del Archivo de la vista
   * 
   * @return string
   */
  public function getViewFile(string $sViewFile = ''): string
  {
    $sView = str_replace('.', LETPHP_DS, $sViewFile);
    $sFile = LETPHP_LETSITE_UI. $this->_sUI. LETPHP_DS. 'views'. LETPHP_DS. $sView.'.'.LETPHP_APP_SUFFIX_VIEW;
    
    if(file_exists($sFile))
    {
      return $sFile;
    }
    return LETPHP_LETSITE_UI. 'views'. LETPHP_DS. $sView. '.' .LETPHP_APP_SUFFIX_VIEW;
  }
  
  /**
   * Almacenamos en cache el archivo
   * @param mixed $sViewFile Nombre del archivo
   * 
   * @return void
   */
  public function getBuiltViewApp(string $sViewFile = '')
  {
	  $sCachedName = 'fragment_'. $sViewFile. '.'. LETPHP_APP_SUFFIX_VIEW;
	  if(!$this->_isCached($sCachedName))
	  {
		  $sViewFile = str_replace(['controllers', 'fragments'], ['Controllers', 'Fragments'], $sViewFile);
		  $mContent = LetPHP::getClass('letphp.view')->getViewAppFile($sViewFile);
		  
		  if(is_array($mContent))
		  {
			  $mContent = $mContent[0];
		  }
		  else
		  {
			  $mContent = file_get_contents($mContent);
		  }
		  
		  $oViewBessie = LetPHP::getClass('letphp.view.bessie');
		  $sLoadCacheFile = $this->_getFileNameFromCache($sCachedName);
		  $oViewBessie->render($this->_getFileNameFromCache($sCachedName), $mContent, true); 
	  }
	  require($this->_getFileNameFromCache($sCachedName));
  }

	public static function getInstance()
	{
		return LetPHP::getClass('letphp.view');
	}

  /**
   * Incluimos la Vista si existe en el cache
   * @param string $sViewFile
   * 
   * @return void
   */
  private function _getViewFromCache(string $sViewFile)
  {
	  //echo '<br>file -> '. $sViewFile;
    // Verificamos si esta en cache el archivo
    if(!$this->_isCached($sViewFile))
    {
      $oView = LetPHP::getClass('letphp.view.bessie'); 
      $sCode = ((file_exists($sViewFile)) ?file_get_contents($sViewFile)  :null );
      $sLoadCacheFile = $this->_getFileNameFromCache($sViewFile);
      $oView->render($sLoadCacheFile, $sCode);
    }
    $this->_includeView($sViewFile);
  }

  /**
   * Incluimos la vista 
   * @param string $sViewFile 
   * 
   * @return void
   */
  private function _includeView(string $sViewFile = '')
  {
    $sViewFile = $this->_getFileNameFromCache($sViewFile);
    require($sViewFile);
  }

  /**
   * Comprobamos si el archivo ya esta en cache 
   * @param mixed $sViewFile
   * 
   * @return bool
   */
  private function _isCached($sViewFile)
  {
    // ¿ Existe el archivo guardado en cache ?
    if(!file_exists($this->_getFileNameFromCache($sViewFile)))
    {
      return false;
    }
    
    if(file_exists($sViewFile))
    {
      $iTime = filemtime($sViewFile);
      if (($iTime + 60) > LETPHP_TIME){  return false; }		 
    }
    return true; 
    
  }

  /** 
   * Obtenemos la ruta completa del archivo en cache 
   * @param string $sViewFile Nombre del Archivo
   * @return string Regresa la ruta del Archivo
  */
  public function _getFileNameFromCache(string $sViewFile = ''): string
  {
	  
    if(!is_dir(LETPHP_LETCORE_DIRS_CACHE. 'views'. LETPHP_DS))
    {
      mkdir(LETPHP_LETCORE_DIRS_CACHE. 'views'. LETPHP_DS );
      chmod(LETPHP_LETCORE_DIRS_CACHE. 'views'. LETPHP_DS, 0777);
    }
		//LETPHP_LETCORE_DIRS_CACHE. 'views'. LETPHP_DS. str_replace([LETPHP_LETSITE_UI, LETPHP_LETAPPS, LETPHP_DS], ['', '', '_'], $sViewFile); 
    return LETPHP_LETCORE_DIRS_CACHE. 'views'. LETPHP_DS. str_replace([LETPHP_LETSITE_UI, LETPHP_LETAPPS, LETPHP_DS], ['', '', '_'], $sViewFile); 
  }


}

?>

