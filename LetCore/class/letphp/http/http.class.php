<?php
/** Class LetPHP_Http */
defined('LETPHP') or exit('NO EXISTE LETPHP');

/**
 * Creamos un metodo especial, para administrar 
 * y poder trabajar con la variable global $_REQUEST, 
 * todas las peticiones son analisadas y convertidas.
 * 
 * 
 * 
 * @copyright LetCode IO
 * @author Rodrigo Hernández Ortiz
 * @package LetPHP\Http
 * @version .1
 */
class LetPHP_Http
{

  /** 
   *	Arreglo donde guardamos todas las variables globales
   * @var array
   */
  private $_aParams = [];

  /** 
   *	Arreglo donde guardamos todas las solicitudes de nuestro sitio
   * @var array
   */
  private $_aRequests = [];


  /**
   * Arreglo Nombres
   * @var array
   */
  private $_aName = [];

  /**
   * Nombre 
   * @var string
   */
  private $_sName = '';


  /** 
   * Constructor de la clase , cargamos los valores 
   * de la variables Globales($_GET, $_POST, $_FILES)
   */
  public function __construct()
  {
    $this->_setRequests();
    $this->_aParams = $this->_trimData(array_merge($_GET, $_POST, $_FILES, $this->getRequests()));
  }

  /**
   * Comprobamos si existe el paramatro en Request.
   * @param string $sName
   * 
   * @return object
   */
  public function isRequest(string $sName = ''): object
  {
    if (isset($this->_aRequests[$sName])) {
      $this->_aName[$sName] = true;
      $this->_sName = $sName;
    }
    return $this;
  }

  /**
   * Obtenemos un parametro de la ruta.
   * @param string $sName nombre del parametro
   * @return string regresa un parametro de cadena
   */
  public function getParam(string $sName = ''): string
  {
    if ($sName != '') {
      return ((isset($this->_aParams[$sName])) ? $this->_aParams[$sName] : '');
    }
  }

  /**
   * Obtenemos un parametro de la ruta 
   * y lo convertimos en entero.
   * @param string $sName nombre del parametro
   * @return int Regresa un valor entero
   */
  public function getParamInt(string $sName = ''): int
  {
    return (int) ($this->getParam($sName));
  }

  /**
   * Obtenemos un parametro de la ruta 
   * y lo convertimos en arreglo.
   * @param string $sName nombre del parametro.
   * @return array Regresa un arreglo.
   */
  public function getParamArray(string $sName = ''): array
  {
    return (array) ((isset($this->_aParams[$sName])) ? $this->_aParams[$sName] : '');
  }

  /**
   * Cargamos los valores de las variables GLOBALES $_POST/$_GET/$_FILES.
   * @return array regresa un arreglo
   */
  public function getParams(): array
  {
    return (array) ($this->_aParams);
  }

  /** 
   * Obtenemos el IP de los Visitantes del Sitio.
   * @param bool $bReturnNum regresa el valor númerico de la IP .
   * @return string regresa un arreglo.
   */
  public function getIp(bool $bReturnNum = false): string
  {

    if (PHP_SAPI == 'cli') {
      return 0;
    }

    $sAltIP = $_SERVER['REMOTE_ADDR'];

    if (isset($_SERVER['HTTP_CLIENT_IP'])) {
      $sAltIP = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) and preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $aMatches)) {
      foreach ($aMatches[0] as $sIP) {
        if (!preg_match("#^(10|172\.16|192\.168)\.#", $sIP)) {
          $sAltIP = $sIP;
          break;
        }
      }
    } elseif (isset($_SERVER['HTTP_FROM'])) {
      $sAltIP = $_SERVER['HTTP_FROM'];
    }

    if ($bReturnNum === true) {
      $sAltIP = str_replace('.', '', $sAltIP);
    }

    return $sAltIP;
  }



  /**
   * Obtenemos las Solicitudes de los parametros del Sitio.
   * @return array 
   */
  public function getRequests(): array
  {
    return $this->_aRequests;
  }


  /**
   * Obtenemos el ID Unico de la sesión del navegador
   * @return string Una cadena con hash sha1.
   */
  public function getHashUnique(): string
  {
    return sha1(((isset($_SERVER['HTTP_USER_AGENT']))) ? $_SERVER['HTTP_USER_AGENT'] . LetPHP::getConfig('main.token') : null);
  }


  /**
   * Enviamos una peticion a otro servidor, 
   * normalmente lo hacemos atraves de CURL.
   * @param string $sRoute Ruta del servidor externo.
   * @param array $aParams Parametros a enviar.
   * @param string $sMethod POST/GET
   * @param null $sUserAgent 
   * @param null $aCookies Array de Cookies a pasar.
   * @param bool $bFollow 
   * 
   * @return mixed Regresa false si la conexión falla, una cadena si la conexión fue exitosa.
   */
  public function sendRequestCURL(string $sRoute, array $aParams = [], string $sMethod = 'POST', array $aHeaders = [], $sUserAgent = null, $aCookies = null, $bFollow = false)
  {
    $aHost = parse_url($sRoute);
    $sPost = '';
    foreach ($aParams as $sKey => $sValue) {
	    //if($sKey == 'currency'){ echo urldecode('&'.  $sKey) ; }
	    //echo '<br>&' . $sKey . '=' . $sValue;
      $sPost .= '&' . $sKey . '=' . $sValue;
    }
    
    
    // Curl
    if (extension_loaded('curl') && function_exists('curl_init')) {
      $hCurl = curl_init();
			//echo 'Hola'. (($sMethod == 'GET' && !empty($sPost)) ? $sRoute . '?' . ltrim($sPost, '&') : $sRoute);
      curl_setopt($hCurl, CURLOPT_URL, (($sMethod == 'GET' && !empty($sPost)) ? $sRoute . '?' . ltrim($sPost, '&') : $sRoute));
      curl_setopt($hCurl, CURLOPT_HEADER, false);
      curl_setopt($hCurl, CURLOPT_FOLLOWLOCATION, $bFollow);
      curl_setopt($hCurl, CURLOPT_RETURNTRANSFER, true);

			
			curl_setopt($hCurl, CURLOPT_HTTPHEADER, $aHeaders);
			
      // Testing this out at the moment...
      curl_setopt($hCurl, CURLOPT_SSL_VERIFYPEER, false);

      // Run if this is a POST request method
      if ($sMethod != 'GET') {
        curl_setopt($hCurl, CURLOPT_POST, true);
        curl_setopt($hCurl, CURLOPT_POSTFIELDS, $sPost);
      }

      // Add the browser agent
      curl_setopt($hCurl, CURLOPT_USERAGENT, ($sUserAgent === null ? "" . LetPHP::BROWSER_AGENT . " (" . LetPHP::getVersion() . ")" : $sUserAgent));

      // Check if we need to set some cookies
      if ($aCookies !== null) {
        $sLine = "\n";
        // Loop thru all the cookies we currently have set
        foreach ($aCookies as $sKey => $sValue) {
          // Make sure we don't see the session ID or the browser will crash
          if ($sKey == 'PHPSESSID') {
            continue;
          }

          // Add the cookies
          $sLine .= '' . $sKey . '=' . $sValue . '; ';
        }
        // Trim the cookie
        $sLine = trim(rtrim($sLine, ';'));

        // Set the cookie
        curl_setopt($hCurl, CURLOPT_COOKIE, $sLine);
      }

      // Run the exec
      $sData = curl_exec($hCurl);

      // Close the curl connection
      curl_close($hCurl);
			
      // Return whatever we can from the curl request
      return trim($sData);
    }

    // file_get_contents()

    if ($sMethod == 'GET' && ini_get('allow_url_fopen') && function_exists('file_get_contents')) {
			
      $sData = file_get_contents($sRoute . "?" . ltrim($sPost, '&'));
      return trim($sData);
    }

    // fsockopen
    if (!isset($sData)) {
      $hConnection = fsockopen($aHost['host'], 80, $errno, $errstr, 30);

      if (!$hConnection) {
        return false;
      } else {
        if ($sMethod == 'GET') {
          $sRoute = $sRoute . '?' . ltrim($sPost, '&');
        }

        $sSend = "{$sMethod} {$sRoute}  HTTP/1.1\r\n";
        $sSend .= "Host: {$aHost['host']}\r\n";
        $sSend .= "User-Agent: " . LETPHP::BROWSER_AGENT . " (" . LetPHP::getVersion() . ")\r\n";
        $sSend .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $sSend .= "Content-Length: " . strlen($sPost) . "\r\n";
        $sSend .= "Connection: close\r\n\r\n";
        $sSend .= $sPost;
        fwrite($hConnection, $sSend);
        $sData = '';
        while (!feof($hConnection)) {
          $sData .= fgets($hConnection, 128);
        }

        $aResponse = preg_split("/\r\n\r\n/", $sData);
        $sHeader = $aResponse[0];
        $sData = $aResponse[1];

        if (!(strpos($sHeader, "Transfer-Encoding: chunked") === false)) {
          $aAux = split("\r\n", $sData);
          for ($i = 0; $i < count($aAux); $i++) {
            if ($i == 0 || ($i % 2 == 0)) {
              $aAux[$i] = '';
            }
            $sData = implode("", $aAux);
          }
        }

        return chop($sData);
      }
    }

    return false;
  }


	public static function getInstance()
	{
		return LetPHP::getClass('letphp.http');
	}

  /**
  * Elimina los parametros, si la configuracion 
  * get_magic_quotes_gpc esta habilitada.
	* @param array $mParam request params.
	* @return mixed string|array
	*/
  private function _trimData($mParam)
  {
    if (is_array($mParam)) {
      return array_map(array(&$this, '_trimData'), $mParam);
    }

    if (@get_magic_quotes_gpc()) {
      $mParam = stripslashes($mParam);
    }

    $mParam = trim($mParam);

    return $mParam;
  }

  /**
   * Cargamos las variables globales
   * @return void
   */
  public function _setRequests()
  {

    if (!isset($_GET[LETPHP_GET_METHOD])) {
      return '';
    }

    $sDefaultModule = LETPHP_APP_CORE; //LetPHP::getConfig('main.app_core');
    $sRequest = $_GET[LETPHP_GET_METHOD];
    $sRequest = trim($sRequest, '/');
    $aRequest = explode("/", $sRequest);
    
    $iCnt = 0;
    foreach ($aRequest as $sVar) {
      $sVar = trim($sVar);
      if (!empty($sVar)) {
      }
    }

    $aRequest = explode("/", $sRequest);
    preg_match('/([a-z0-9]+_[a-z0-9]+)/i', $sRequest, $aParams);
    $sRequest = str_replace($aParams, '', $sRequest);
    
    
    $sRequest = trim($sRequest, '/');

    foreach ($aRequest as $sVar) {
      $sVar = trim($sVar);

      if (!empty($sVar)) {
        $iCnt++;
        $bPass = true;
        if ($iCnt == 1 && !preg_match("/^frame_(.*)$/", $sVar)) {
          $bPass = false;
        }

        if ($bPass && preg_match('/\_/', $sVar)) {
          $aPart = explode('_', $sVar);
          if (isset($aPart[0])) {
            if (count($aPart) > 2) {
              $this->_aRequests[$aPart[0]] = (substr_replace($sVar, '', 0, (strlen($aPart[0]) + 1)));
            } else {
              $this->_aRequests[$aPart[0]] = (isset($aPart[1]) ? $aPart[1] : '');
            }
          }
        } else {
          if (($iCnt == 1) && ($sDefaultModule != LETPHP_APP_CORE)) {
            $this->_aParams['param1'] = strtolower($sDefaultModule);
            $this->_aParams['param2'] = $sVar;
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
}
