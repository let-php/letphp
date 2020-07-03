<?php
/** Class LetPHP_Router */
defined('LETPHP') or exit('NO EXISTE LETPHP');

/**
 * Esta clase nos ayuda a generar y administrar 
 * las rutas internas de nuestro sitio.
 * 
 * @copyright LetCode IO
 * @author Rodrigo Hernández Ortiz
 * @package LetPHP\Router
 * @version .1
 * 
 */
class LetPHP_Router 
{
	
	/**
	 * Lista de Códigos de Cabeceras.
	 * 
	 * @var array
	 */
	protected $_aHeaders =  [
       100 => "HTTP/1.1 100 Continue",
       101 => "HTTP/1.1 101 Switching Protocols",
       200 => "HTTP/1.1 200 OK",
       201 => "HTTP/1.1 201 Created",
       202 => "HTTP/1.1 202 Accepted",
       203 => "HTTP/1.1 203 Non-Authoritative Information",
       204 => "HTTP/1.1 204 No Content",
       205 => "HTTP/1.1 205 Reset Content",
       206 => "HTTP/1.1 206 Partial Content",
       300 => "HTTP/1.1 300 Multiple Choices",
       301 => "HTTP/1.1 301 Moved Permanently",
       302 => "HTTP/1.1 302 Found",
       303 => "HTTP/1.1 303 See Other",
       304 => "HTTP/1.1 304 Not Modified",
       305 => "HTTP/1.1 305 Use Proxy",
       307 => "HTTP/1.1 307 Temporary Redirect",
       400 => "HTTP/1.1 400 Bad Request",
       401 => "HTTP/1.1 401 Unauthorized",
       402 => "HTTP/1.1 402 Payment Required",
       403 => "HTTP/1.1 403 Forbidden",
       404 => "HTTP/1.1 404 Not Found",
       405 => "HTTP/1.1 405 Method Not Allowed",
       406 => "HTTP/1.1 406 Not Acceptable",
       407 => "HTTP/1.1 407 Proxy Authentication Required",
       408 => "HTTP/1.1 408 Request Time-out",
       409 => "HTTP/1.1 409 Conflict",
       410 => "HTTP/1.1 410 Gone",
       411 => "HTTP/1.1 411 Length Required",
       412 => "HTTP/1.1 412 Precondition Failed",
       413 => "HTTP/1.1 413 Request Entity Too Large",
       414 => "HTTP/1.1 414 Request-URI Too Large",
       415 => "HTTP/1.1 415 Unsupported Media Type",
       416 => "HTTP/1.1 416 Requested range not satisfiable",
       417 => "HTTP/1.1 417 Expectation Failed",
       500 => "HTTP/1.1 500 Internal Server Error",
       501 => "HTTP/1.1 501 Not Implemented",
       502 => "HTTP/1.1 502 Bad Gateway",
       503 => "HTTP/1.1 503 Service Unavailable",
       504 => "HTTP/1.1 504 Gateway Time-out"
	];
	
	/**
	 * @var array Almacenamos las variables $_POST/$_REQUEST/$_GET.
	 */
	private $_aRequests = [];
	
	
	/**
   * Cargamos las peticiones $_POST/$_REQUEST/$_GET.
	 */
	public function __construct()
	{
		$this->_setRequests();
	}
  
  /** 
	* Obtenemos el nombre del dominio de nuestro sitio.
	* @return string 
	*/
  public function getDomain(): string
  { 
	  return LetPHP::getConfig('main.path');
	  
  }

	/** 
	*	Creamos rutas internas para nuestro sitio.
	* @param string $sRoute Nombre de la Ruta
	* @param array $aParams Parametros a pasar
  * @param bool $bFullRoute Ruta completa
  * @return string
	*/
  public function createRoute(string $sRoute= '', array $aParams= [], bool $bFullRoute= false):string
  {
	  
	  if(preg_match("/(http|https):\/\//i", $sRoute))
	  {
		  return $sRoute;
	  }
	  
	  if ($sRoute == 'current')
		{
			$sRoute = '';			
			foreach ($this->_aRequests as $sKey => $sValue)
			{
				if (substr($sKey, 0, 5) == 'param')
				{
					$sRoute .= urlencode($sValue) . '.';
				}
				else 
				{					
					$sRoute .= $sKey . '_' . urlencode($sValue) . '.';
				}
			}
		}
		
	  $sRoute = trim($sRoute, '.');		
		$sRoutes = '';
		
		$iRouteRewrite = LetPHP::getConfig('main.route_rewrite');
		switch($iRouteRewrite)
		{
			case 1:
				$aRouteParts = explode('.', $sRoute);				
				if ($bFullRoute)
				{
					$sRoutes .= LetPHP::getConfig('main.path');
				}
				$sRoutes .= LetPHP::getConfig('main.path');				
				$sRoutes .= $this->_createRoute($aRouteParts, $aParams);	
			break;
			
			case 2:
				$aRouteParts = explode('.', $sRoute);
				
				if ($bFullRoute)
				{
					$sRoutes .= LetPHP::getConfig('main.path');
				}				
				
				$sRoutes .= LetPHP::getConfig('main.path') . LETPHP_INDEX_FILE . '?' . LETPHP_GET_METHOD . '=/';
				$sRoutes .= $this->_createRoute($aRouteParts, $aParams);
			
			break;
		}
		
		
		// tiene soporte para SSL ?
		if (LetPHP::getConfig('main.site_secure') == 1)
		{
			$sRoutes = str_replace('http://', 'https://', $sRoutes);
		}
		elseif(LetPHP::getConfig('main.site_secure') == 2)
		{
			$sRoutes = str_replace('https://', 'http://', $sRoutes);
		}
		
		return $sRoutes;
	  
  }
  
  
  /**
	  * Tiene un funcionamiento similar a location de PHP, 
	  * con esta función podemos dirigirnos a cualquier controlador del sitio
	  * @param string $sRoute Ruta a donde nos vamos a mover.
	  * @param array $aParams parametros que vamos a pasar.
	  * @param string $sMessage Mensaje.
    * @param int $iHeader 
    * @return void
	*/
  public function goToPage(string $sRoute = '', array $aParams = [], string $sMessage = '', int $iHeader = null)
	{
		if ($sMessage != ''){ LetPHP::setMessage($sMessage); }
		$this->_send((preg_match("/(http|https):\/\//i", $sRoute) ?$sRoute :$this->createRoute($sRoute, $aParams)), $iHeader);
		exit;
  }
  
  
	/**
	 * Convierte una Ruta a Array.
	 *
	 * @param string $sRoute Ruta a convertir.
	 * @return array Arreglo convertido desde una Ruta.
	*/
	public function convertRouteToArray(string $sRoute)
	{
		$aParams = [];
		
		switch (LetPHP::getConfig('main.route_rewrite'))
		{
			case 1:			
				$aParts = explode(LetPHP::getConfig('main.path'), $sRoute);
				$aParams = $this->_convertRouteToArray($aParts[1]);				
				break;			
			case 2:				
			
				$aParts = explode(LETPHP_GET_METHOD . '=', $sRoute);
				$aParams = $this->_convertRouteToArray($aParts[1]);				
				break;
			case 3:
				preg_match("/^http:\/\/(.*?)\.(.*?)\/(.*?)$/i", $sUrl, $aMatches);
				$sRoute = $aMatches[1] . '/' . str_replace(LetPHP::getConfig('main.folder'), '', '/' . $aMatches[3]);
				$aParams = $this->_convertRouteToArray($sRoute);
				break;
		}	
		
		return $aParams;
	}
	
  /**
   * Crea Ruta permanentes para los articulos.
   * @param string $sRoute Nombre de la Ruta.
   * @param int $iItemId Id del Artículo.
   * @param string|null $sTitle Titulo del Artículo.
   * @param bool $bRedirect Redireccionar.
   * @param string $sMsg Mensaje.
   * @param array $aExtraRoutes Arreglo de Rutas adicionales.
   * 
   * @return string
   */
  public function permanentRoute(string $sRoute, int $iItemId, string $sTitle = null, bool $bRedirect = false, string $sMsg = '', array $aExtraRoutes = []): string
  {
	  if ($sMsg !== '')
		{
			LetPHP::setMessage($sMsg);
		}		
		
		$aExtra = [];
		$aExtra[] = $iItemId;
		if (!empty($sTitle))
		{
			if (preg_match('/\{phrase var\=(.*)\}/i', $sTitle, $aMatches) && isset($aMatches[1]))
			{
				$sTitle = str_replace(array("'", '"', '&#039;'), '', $aMatches[1]);
				// Phrase
				$sTitle = $sTitle;
			}
			
			$aExtra[] = $this->parseTitle($sTitle);
		}
		
		if (is_array($sRoute))
		{
			$iCount = 0;
			foreach ($sRoute as $mKey => $mValue)
			{
				$iCount++;
				if ($iCount === 1)
				{
					$sActualRoute = $mValue;
					
					continue;
				}
				
				if (is_numeric($mKey))
				{
					$aExtra[] = $mValue;
				}
				else 
				{
					if ($mKey == 'view')
					{
						$mValue = urlencode($mValue);
					}
					$aExtra[$mKey] = $mValue;	
				}
			}
			$sRoute = $sActualRoute;
		}
		
		if (is_array($aExtraRoutes) && count($aExtraRoutes))
		{
			$aExtra = array_merge($aExtra, $aExtraRoutes);	
		}
		
		$sRoute = LetPHP::getClass('letphp.router')->createRoute($sRoute, $aExtra);
		
		if ($bRedirect === true)
		{
			$this->_send($sRoute);	
		}
		
		return $sRoute;
	  
  }
  
  /**
   * Limpiamos el titulo de la ruta 
   * @param string $sTitle Titulo a limpiar.
   * 
   * @return string
   */
  public function parseTitle(string $sTitle): string
	{
		$sTitle = html_entity_decode($sTitle, null, 'UTF-8');
		$sTitle = strtr($sTitle, '`!"$%^&*()-+={}[]<>;:@#~,./?|'. "\r\n\t\\", '                             '. '    ');
		$sTitle = strtr($sTitle, array('"' => '', "'" => ''));
		$sTitle = preg_replace('/[ ]+/', '-', trim($sTitle));		
			
		$sTitle = strtolower($sTitle);
		if (function_exists('mb_strtolower'))
		{
			$sTitle = mb_strtolower($sTitle, 'UTF-8');
		}
		else 
		{
			$sTitle = strtolower($sTitle);	
		}
		
		if (function_exists('mb_substr'))
		{
			$sTitle = mb_substr($sTitle, 0, 60, 'UTF-8');			
		}
		else 
		{
			$sTitle = substr($sTitle, 0, 60);
		}
		
		return $sTitle;
	}

  
  /**
   * Obtenemos la ruta completa
   * @param bool $bNoPath 
   * 
   * @return string
   */
  public function getFullRoute(bool $bNoPath = false):string
  {
	  if($bNoPath)
	  {
		  return LetPHP::getClass('letphp.http')->getParam(LETPHP_GET_METHOD);
	  }
	  return $this->createRoute('current'); 
  }
  
  public static function getInstance()
  {
	  return LetPHP::getClass('letphp.router');
  }
  
	/**
   * Enviamos al Usuario a una nueva ruta
	 * @param string $sRoute
	 * @param int|null $iHeader
	 * 
	 * @return void
	 */
	private function _send( string $sRoute, int $iHeader = null )
	{
		// Limpiamos Buffer
		ob_clean();
		if ($iHeader !== null && isset($this->_aHeaders[$iHeader]))
		{
			header($this->_aHeaders[$iHeader]);
		}
		// Enviamos al usuario a una nueva ubicacion del sitio
		header('Location: '. $sRoute);
		
		exit;
	}

  
  /**
   * Contruimos una ruta por las reglas del servidor 
   * @param array $aRouteParts
   * @param array $aParams
   * 
   * @return string
   */
  public function _createRoute(array &$aRouteParts = [], array $aParams = [] ): string
  {
	  $sRoutes = '';
	  foreach ($aRouteParts as $iRouteKey => $sRoutePart) 
		{
			if (empty($sRoutePart))
			{
				continue;
			}
			$sRoutes .= str_replace('.', '', $sRoutePart) . '/';
		}	
		
		if ($aParams && is_array($aParams))
		{
			foreach ($aParams as $sKey => $sValue)
			{				
				if (is_null($sValue))
				{
					continue;
				}
				$sRoutes .= ((is_numeric($sKey)) ?str_replace('.', '', $sValue) :$sKey . '_' . str_replace('.', '', $sValue)) . '/'; 
			}
		}	

		return $sRoutes;
	  
  }
  
  /**
   * Cargamos los metodos $_GET
   * @return void
   */
  public function _setRequests()
  {

    if( !isset($_GET[LETPHP_GET_METHOD]))
    {
      return '';
    }

    $sDefaultModule = LETPHP_APP_CORE;
    $sRequest = $_GET[LETPHP_GET_METHOD];
		$sRequest = trim($sRequest, '/');
    $aRequest = explode("/", $sRequest);
    $iCnt = 0;
		foreach($aRequest as $sVar)
		{
			$sVar = trim($sVar);
			if (!empty($sVar)){ }
    }
    
    $aRequest = explode("/", $sRequest);
    preg_match('/([a-z0-9]+_[a-z0-9]+)/i', $sRequest, $aParams);
		
		$sRequest = str_replace($aParams, '', $sRequest);
    $sRequest = trim($sRequest, '/');
    
    foreach($aRequest as $sVar)
		{
			$sVar = trim($sVar);
			
			if (!empty($sVar))
			{
				$iCnt++;		
				$bPass = true;
				if ($iCnt == 1 && !preg_match("/^frame_(.*)$/", $sVar))
				{
					$bPass = false;	
				}
	
				if ($bPass && preg_match('/\_/', $sVar))
				{
					$aPart = explode('_', $sVar);
					if (isset($aPart[0]))
					{
						if (count($aPart) > 2)
						{
							$this->_aRequests[$aPart[0]] = (substr_replace($sVar, '', 0, (strlen($aPart[0]) + 1)));
						}
						else 
						{							
							$this->_aRequests[$aPart[0]] = (isset($aPart[1]) ? $aPart[1] : '');
						}
					}
        }
        else
				{					
					if (($iCnt == 1) && ($sDefaultModule != LETPHP_APP_CORE) )
					{
						$this->_aParams['var1'] = strtolower($sDefaultModule);
						$this->_aParams['var2'] = $sVar;
						$iCnt++;
						continue;
					}					
					
					$sVar = rawurldecode($sVar);
					$sVar = rawurlencode($sVar);
					$this->_aRequests['param' . $iCnt] = $sVar;
				}
			}
		}
	}
	
	
	/**
	 * Convierte una Ruta a Arreglo 
	 *
	 * @param string $sRoute URL string.
	 * @return array Arreglo de Requests
	 */
	private function _convertRouteToArray(string $sRoute)
	{
		$aParams = array();		
		$aSubParams = explode('/', $sRoute);
		$iCount = 0;
		foreach ($aSubParams as $sSubParam)
		{
			if (empty($sSubParam))
			{
				continue;
			}					
					
			if (substr($sSubParam, 0, 1) == '#')
			{
				continue;
			}
					
			$iCount++;
					
			if (strpos($sSubParam, '_'))
			{
				$aPart = explode('_', $sSubParam);
				if (isset($aPart[0]))
				{
					if (count($aPart) > 2)
					{
						$aParams[$aPart[0]] = (substr_replace($sSubParam, '', 0, (strlen($aPart[0]) + 1)));
					}
					else 
					{
						$aParams[$aPart[0]] = (isset($aPart[1]) ? $aPart[1] : '');
					}
				}
			}
			else 
			{
				$aParams['param' . $iCount] = $sSubParam;
			}
		}		
		return $aParams;
	}
  
}

?>