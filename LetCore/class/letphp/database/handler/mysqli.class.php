<?php

class LetPHP_Database_Handler_MySQLi extends LetPHP_Database_DML
{
	
	protected $_oConnect = null;
	
	/*
	 * Array de funciones de MySQLi
	*/
  protected $_aFunctionsSql =  [
    'mysqli_query' => 'mysqli_query',
		'mysqli_connect' => 'mysqli_connect',
		'mysqli_pconnect' => 'mysqli_pconnect',
		'mysqli_select_db' => 'mysqli_select_db',
		'mysqli_num_rows' => 'mysqli_num_rows',
		'mysqli_fetch_array' => 'mysqli_fetch_array',
		'mysqli_real_escape_string' => 'mysqli_real_escape_string',
		'mysqli_insert_id' => 'mysqli_insert_id',
		'mysqli_fetch_assoc' => 'mysqli_fetch_assoc',
		'mysqli_free_result' => 'mysqli_free_result',
		'mysqli_error' => 'mysqli_error',
		'mysqli_affected_rows' => 'mysqli_affected_rows',
		'mysqli_get_server_info' => 'mysqli_get_server_info',
		'mysqli_close' => 'mysqli_close'
	];
	
	
	/*
		* Hacemos la conexion a la Base de Datos
		* @param string $sHost Nombre del Host
		* @param string $sUser Nombre del Usuario de la Base de Datos
		* @param string $sPassword Contraseña del Usuario de la Base de Datos 
		* @param string $sDatabase Nombre de la Base de Datos
		* @param mixed $sPort Puerto del Servidor de la Base de datos
		* @param bool $bPersisten Indicamos si la conexión es persistente
	*/
	public function connect(string $sHost, string $sUser, string $sPassword, string $sDatabase,  $sPort = false, bool $bPersitent = false)
	{
		
		## Conexion a la Base de Datos
		$this->_oConnect = $this->_connectDatabase($sHost, $sUser, $sPassword, $sPort, $bPersitent);
		if(!@(($this->_aFunctionsSql['mysqli_select_db'] === 'mysqli_select_db') 
			? $this->_aFunctionsSql['mysqli_select_db']($this->_oConnect, $sDatabase)
			: $this->_aFunctionsSql['mysqli_select_db']($sDatabase, $this->_oConnect)
		)){
			return exit('Hay un error en la conexión de la Base de Datos!!');
		}
		
		return true;	
	}
	
	/*
		* Hacemos una consulta al Servidor SQL
		* @param string $sSql Consulta SQL
		* @param object $hLink Link de la Conexión
	*/
  public function query(string $sSql, &$hLink = '')
  {
	  if(!$hLink)
		{
			$hLink =& $this->_oConnect;
		}
		$hRes = @($this->_aFunctionsSql['mysqli_query'] == 'mysqli_query' 
			? $this->_aFunctionsSql['mysqli_query']($hLink, $sSql) 
			: $this->_aFunctionsSql['mysqli_query']($sSql, $hLink));

		if (!$hRes)
		{
			exit('Mal!!');
		}   
	  return $hRes;
  }
  
  
	/**
	 * Regresa la Version de MySQL 
	 *
	 * @return string
	 */
	public function getServerInfoMySQL()
	{
		return @$this->_aFunctionsSql['mysqli_get_server_info']($this->_oConnect);	
	}
	
	
	/** 
		* Obtenemos el ID de la consulta INSERT ejecutada 
		* @return int id
		*/
	public function getLastId()
	{
			return @$this->_aFunctionsSql['mysqli_insert_id']($this->_oConnect);
	}
	
	
	/**
		* Liberamos Memoria de la consulta ejecutada.
		* @return void
		*/
	public function freeResult()
	{
		if(is_resource($this->rQuery))
		{
			@$this->_aFunctionsSql['mysqli_free_result']($this->rQuery);
		}
	}
	

	public function escape($mParam)
  {
		if(is_array($mParam)){ return array_map(array(&$this, 'escape'), $mParam); }
		if (get_magic_quotes_gpc()){ $mParam = stripslashes($mParam); }
    $mParam = @($this->_aFunctionsSql['mysqli_real_escape_string'] == 'mysqli_real_escape_string' ? $this->_aFunctionsSql['mysqli_real_escape_string']($this->_oConnect, $mParam) : $this->_aFunctionsSql['mysqli_real_escape_string']($mParam));
    return $mParam;
  }  
  
  
  public function join(string $sTable, string $sAlias, $mParam = null):object
  {
	  $this->_join('JOIN', $sTable, $sAlias, $mParam);
	  return $this;
  }
  
  
  
  
	/**
		* Cerramos la conexión SQL
		*
		* @return bool TRUE si es exitosa, FALSE si ha fallado
		*/
	public function closeConnection()
	{
		return @$this->_aFunctionsSql['mysqli_close']($this->_oConnect);
  }
  
  /**
	 * Conectamos a la Base de Datos
	 * @param string $sHost Hosting o IP
	 * @param string $sUser Usuario de la Base de Datos
	 * @param string $sPass Contraseña de la Base de Datos
	 */
	private function _connectDatabase(string $sHost, string $sUser, string $sPassword, $sPort = false, bool $bPersitent = false)
	{
		if($sPort)
		{
			$sHost = $sHost.':'.$sPort;
		}

		if( $oConnect = (($bPersitent) 
			? @$this->_aFunctionsSql['mysqli_pconnect']($sHost, $sUser, $sPassword) 
			: @$this->_aFunctionsSql['mysqli_connect']($sHost, $sUser, $sPassword) 
		))
		{
			return $oConnect;
		}
		return false;
	}
	
	
	/**
	 * Obtenemos una fila de la Consulta a la Base de Datos 
	 */
	protected function _getSingular($sQuery, $bAssoc, &$hLink)
	{
		$oRes = $this->query($sQuery, $hLink); 
		@$aRes = $this->_aFunctionsSql['mysqli_fetch_array']($oRes, (($bAssoc)? MYSQLI_ASSOC : MYSQLI_NUM ));
		return (($aRes) ? $aRes : []);
	}


	/**
	 * Obtenemos un conjunto de filas 
	 */
	protected function _getCollections($sQuery, $bAssoc = true, &$hLink)
  {
		$aRows = [];
		$bAssoc = ($bAssoc ? MYSQLI_ASSOC : MYSQLI_NUM);
		$this->rQuery = $this->query($sQuery, $hLink);
    while($aRow = $this->_aFunctionsSql['mysqli_fetch_array']($this->rQuery, $bAssoc)){ $aRows[] = $aRow; }
		return $aRows;
  }
	
	
}	
	
?>