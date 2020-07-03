<?php
/** Class LetPHP_Database */
defined('LETPHP') or exit('NO LETPHP!');
LetPHP::getFileClass('letphp.database.interface');

/**
 * Clase para administrar las funciones para manipular
 * los datos, que se encuentran guardados en nuestra Base de Datos
 *  - select
 *  - insert
 *  - update
 *  - delete
 * 
 * @copyright LetCode IO
 * @author Rodrigo Hernández Ortiz
 * @package LetPHP\Database
 * @version .1
 * 
 */
abstract class LetPHP_Database_DML implements LetPHP_Database_Interface
{
	
	const SELECT_COUNT = 2;
	
	/** @var int $_bCount  */
	private $_bCount = false;
	
  /** @var array $_aQuery Arreglo que guarda las consultas a la Base de Datos */
  protected $_aQuery = [];

  /**
   * Guarda los valores para las funciones.
   * @var array $_aData
   */
  private $_aData = [];

  /**
   * Se guarda la información que vamos a SELECCIONAR.
   * @param string $sSelect Selección a la Base de Datos.
   * @return object
   */
  public function select(string $sSelect): object
  {
    if (!isset($this->_aQuery['select'])) {
      $this->_aQuery['select'] = 'SELECT ';
    }
    $this->_aQuery['select'] .= $sSelect;
    return $this;
  }

  /** 
		* Seleccionamos la Tabla de la Base de Datos
		* @param string $sTable Nombre de la Tabla
		* @param string $sAlias (Opcional) Alias de la Tabla
		* @return object LetPHP_Database
	*/
  public function table(string $sTable, string $sAlias = ''): object
  {
    $this->_aQuery['table'] = 'FROM ' . $sTable . ($sAlias ? ' AS '. $sAlias : '');
    return $this;
  }


  /**
		* Condiciones para la consulta SQL
		* @param mixed $aConditions Condiciones de la Consulta SQL
		* 
		* @return object LetPHP_Database
	*/
  public function cond($aConditions): object
  {
    $this->_aQuery['cond'] = '';

    if (is_array($aConditions) && count($aConditions)) {
      foreach ($aConditions as $sValue) {
        $this->_aQuery['cond'] .= $sValue . ' ';
      }
      $this->_aQuery['cond'] = "WHERE " . trim(preg_replace("/^(AND|OR)(.*?)/i", "", trim($this->_aQuery['cond'])));
    } else {
      if (!empty($aConditions)) {
        $this->_aQuery['cond'] .= 'WHERE ' . $aConditions;
      }
    }
    return $this;
  }


  /**
   *  Ordena la consulta SQL, deacuerdo 
   *  al parametro que enviemos.
   * @param mixed $sOrder Como ordenar los datos de la consulta.
   * 
   * @return object LetPHP_Database
   */
  public function order(string $sOrder): object
  {
    if (!empty($sOrder)) {
      $this->_aQuery['order'] = 'ORDER BY ' . $sOrder;
    }
    return $this;
  }

  /**
   * Limita los Resultados que se mostraran en la
   * consulta SQL, tambien con esta función puedes
   * crear un páginado.
   * 
   * 
   * @param mixed $iPage Numero de la página que actualmente se esta utilizando.
   * @param null $sLimit Limite de resultados que se mostraran por consulta.
   * @param null $iCnt cuantas filas hay en la consulta.
   * @param bool $bReturn 
   * @param bool $bCorrectMax 
   * 
   * @return object LetPHP_Database
   */
  public function limit($iPage, $sLimit = null, $iCnt = null, $bReturn = false, $bCorrectMax = false): object
  {
    if ($sLimit === null && $iCnt === null && $iPage !== null) {
      $this->_aQuery['limit'] = 'LIMIT ' . $iPage;
      return $this;
    }

    if ($bCorrectMax == true) {
    } else {
      $this->_aQuery['limit'] = ($sLimit ? 'LIMIT ' . $sLimit : '') . ($sLimit != null && $iPage > 0 ? ' OFFSET ' . (($iPage - 1) * ($sLimit)) : '');
    }

    if ($bReturn === true) {
      return $this->_aQuery['limit'];
    }
    return $this;
  }
  
  /**
	  * Usamos el grupo de acuerdo a la consulta.
	  * @param string $sGroup Nombre del Grupo.
	  * @return object
	  */
  public function groupBy(string $sGroup): object
  {
	  $this->_aQuery['group'] = 'GROUP BY '. $sGroup;
	  return $this; 
  }
  
  /**
	  * Almacena la consulta HAVING SQL.
	  * @param string $sHaving 
	  * @return object
	  */
  public function having(string $sHaving): object
  {
	  $this->_aQuery['having'] = 'HAVING '. $sHaving;
	  return $this;
  }

  /**
   * Se ejecuta la consulta SQL final, junto 
   * con los métodos que hemos reunido.
   * 
   * @param null $sResult Comando para ejecutar la consulta SQL, su se deja vacio, regresa el SQL en texto.
   * @param array $aParams 
   * 
   * @return array|int|string
   */
  public function run(string $sResult = null, $aParams = [])
  {
	  if(($sResult === 'singular' OR $sResult === 'count') && (!isset($this->_aQuery['limit']) OR empty($this->_aQuery['limit']) ))
	  {
		  $this->_aQuery['limit'] = ' LIMIT 1 ';
	  }
	  
    $sQuery = '';
    $sQuery .= (isset($this->_aQuery['select']) ? $this->_aQuery['select'] . "\n" : '');
    $sQuery .= (isset($this->_aQuery['table']) ? $this->_aQuery['table'] . "\n" : '');
    
    // Setencias JOIN
    $sQuery .= (isset($this->_aQuery['join'])? $this->_aQuery['join']. "\n": '' );
    
    // Sentencia SQL WHERE
    $sQuery .= (isset($this->_aQuery['cond']) ? $this->_aQuery['cond'] . "\n" : '');
    
    // Sentencia GROUP BY
    $sQuery .= (isset($this->_aQuery['group']) ? $this->_aQuery['group']. "\n": '');
    
    // Sentencia HAVING BY
    $sQuery .= (isset($this->_aQuery['having']) ? $this->_aQuery['having']. "\n": '');
    
    if ($this->_bCount != self::SELECT_COUNT) 
    {	  	
	    // Sentencia para ORDER
	    $sQuery .= (isset($this->_aQuery['order']) ? $this->_aQuery['order'] . "\n" : '');    
	    // Sentencia  LIMIT
	    $sQuery .= (isset($this->_aQuery['limit']) ? $this->_aQuery['limit'] . "\n" : '');
	  }
    
    $sQuery .= PHP_EOL;
    
		if ($this->_bCount == self::SELECT_COUNT) 
		{
			$this->_bCount = false;
		}
		
		//Habilitamos el guardado en cache y llamamos a la Clase.
		$bSaveCache = false;
		if((isset($aParams['cache'])) && (is_bool($aParams['cache'])) && ($aParams['cache']) )
		{
			$bSaveCache = true;
			$oCache = LetPHP::getClass('letphp.cache');
		}
		
		if($bSaveCache)
		{
			$mCacheId = $oCache->setCache($aParams['cache_name']);
			if($aRows = $oCache->getCache($mCacheId))
			{
				return $aRows;
			}
		}
		
		
    $this->_aQuery = [];
    $sResult = strtolower($sResult);
    switch ($sResult) {
	    case 'singular':
        $aRows = $this->getSingular($sQuery);
        break;
      case 'collections':
        $aRows = $this->getCollections($sQuery);
        break;
			case 'count': 
				$aRows = $this->getField($sQuery);
				break;
      default:
        return $sQuery;
    }
    
    // Guardamos el Cache
    if($bSaveCache)
    {
	    $oCache->saveCache($mCacheId , $aRows);
    }
    // Liberamos Memoria
    if((isset($aParams['free_result'])) && (is_bool($aParams['free_result'])) && ($aParams['free_result']) )
    {
	    $this->freeResult();
    }
    
    return $aRows;
  }
  
  
  /**
	  * Contar
	  * @return int
	  */
  public function count()
  {
	  $this->_bCount = self::SELECT_COUNT;
	  $this->_aQuery['select'] = "SELECT COUNT(*) AS iTotal";
	  return intval($this->run('count'));
  }
  
  
  /**
	  * Obtenemos los resultados 
	  * en varias filas.
	  * @return array
	  */
  public function collections(): array
  {
	  return $this->run('collections');
  }
  
  /**
	  * Obtenemos solo una fila del resultado.
	  * @return array
	  *
	 	*/
	public function singular(): array
	{
		return $this->run('singular');
	}
	 	

	/**
	* Obtenemos el nombre de una tabla 
	* de la base de datos.
	* @param string $sTable Nombre de la Tabla.
	* @return string  
	*/
	public function getTable(string $sTable):string
	{
		return $sTable;
	}
	
	
	public function getField(string $sQuery)
	{
		return $this->_getField($sQuery, $this->_oConnect);
	}
	
  /**
   * 
   * Obtenemos una fila del resultado.
   * @param mixed $sQuery
   * @param bool $bAssoc
   * 
   * @return mixed bool|array
   */
  public function getSingular($sQuery, $bAssoc = true)
  {
    return $this->_getSingular($sQuery, $bAssoc, $this->_oConnect);
  }

  /**
   * Obtenemos un conjunto de filas 
   * @param mixed $sQuery
   * @param bool $bAssoc
   * 
   * @return mixed bool|array
   */
  public function getCollections($sQuery, $bAssoc = true)
  {
    return $this->_getCollections($sQuery, $bAssoc, $this->_oConnect);
  }


  /**
   * Funciona para insertar información
   * a la Base de datos.
   * 
   * @param string $sTable Nombre de la tabla.
   * @param array $aValues Valores a insertar.
   * @param bool $bEscape 
   * @param bool $bReturnQuery Regresamos el SQL en String.
   * 
   * @return mixed string|int
   */
  public function insert(string $sTable, array $aValues = [], bool $bEscape = true, bool $bReturnQuery = false)
  {
    if (!$aValues) 
    {
      $aValues = $this->_aData;
    }

    $sValues = '';
    foreach ($aValues as $mValue) {
      if (is_null($mValue)) {
        $sValues .= "NULL, ";
      } else {
        $sValues .= "'" . ($bEscape ? $this->escape($mValue) : $mValue) . "', ";
      }
    }
    $sValues = rtrim(trim($sValues), ',');

    if ($this->_aData) {
      $this->_aData = array();
    }

    $sSql = $this->_insert($sTable, implode(', ', array_keys($aValues)), $sValues);

    if ($hRes = $this->query($sSql)) {
      if ($bReturnQuery) {
        return $sSql;
      }
      return $this->getLastId();
    }
    return 0;
  }
  
  /**
	  * Insertamos multiples registro a la base de datos.
	  * @param string $sTable Nombre de la Tabla.
	  * @param array $aFields Campos de la Tabla.
	  * @param array $aValues Valores de los campos a insertar.
	  * @return int Regresa el último id.
	  */
  public function insertMultipleRecords(string $sTable, array $aFields, array $aValues):int
  {
	  $sQuery = "INSERT INTO {$sTable} (". implode(', ', array_values($aFields)) .") ";
	  $sQuery .= " VALUES\n";
	  foreach($aValues as $aValue)
	  {
		  $sQuery .= "\n(";
		  foreach($aValue as $mVal)
		  {
			  if(is_null($mVal))
			  {
				  $sQuery .= "NULL, ";
			  }
			  else 
			  {
			  	$sQuery .= "'". $this->escape($mVal). "', "; 
			  }
		  }
		  $sQuery = rtrim(trim($sQuery), ',');
		  $sQuery .= "),";
	  } 
	  
	  $sQuery = rtrim($sQuery, ',');
	  if($oConnect = $this->query($sQuery))
	  {
		  return $this->getLastId();
	  }
	  return 0;
  }


  /**
   * Actualizamos la información de la
   * Base de datos.
   * 
   * @param mixed $sTable Nombre de la tabla.
   * @param array $aValues Valores a actualizar.
   * @param null $sCond Condición para actualizar.
   * @param bool $bEscape Regresa la consulta en String.
   * 
   * @return mixed bool|string
   */
  public function update($sTable, $aValues = array(), $sCond = null, $bEscape = true)
  {
    if (!is_array($aValues) && count($this->_aData)) {
      $sCond = $aValues;
      $aValues = $this->_aData;
      $this->_aData = [];
    }

    $sSets = '';
    foreach ($aValues as $sCol => $sValue) {
      $sCmd = "=";
      if (is_array($sValue)) {
        $sCmd = $sValue[0];
        $sValue = $sValue[1];
      }

      $sSets .= "{$sCol} {$sCmd} " . (is_null($sValue) ? 'NULL' : ($bEscape ? "'" . $this->escape($sValue) . "'" : $sValue)) . ", ";
    }
    $sSets[strlen($sSets) - 2] = '  ';
    return $this->query($this->_update($sTable, $sSets, $sCond));
  }

  /**
   * Eliminamos información de la Base de datos.
   * @param mixed $sTable Nombre de la tabla.
   * @param mixed $sQuery Consulta para eliminar.
   * @param null $iLimit Limite.
   * 
   * @return bool
   */
  public function delete($sTable, $sQuery, $iLimit = null): bool
  {
    if ($iLimit !== null) {
      $sQuery .= ' LIMIT ' . (int) $iLimit;
    }
    return $this->query("DELETE FROM {$sTable} WHERE " . $sQuery);
  }

	
	public function innerJoin(string $sTable, string $sAlias, $mParam = null):object
	{
		$this->_join('INNER JOIN', $sTable, $sAlias, $mParam);
		return $this;
	}
	
	
	public function leftJoin(string $sTable, string $sAlias, $mParam = null):object
	{
		$this->_join('LEFT JOIN', $sTable, $sAlias, $mParam);
		return $this;
	}
	
	public function rightJoin(string $sTable, string $sAlias, $mParam = null):object
	{
		$this->_join('RIGHT JOIN', $sTable, $sAlias, $mParam);
		return $this;
	}

	/**
		*
		* @return object LetPHP_Database
		*/
  public function join(string $sTable, string $sAlias, $mParam = null):object
  {
	  $this->_join('JOIN', $sTable, $sAlias, $mParam);
	  return $this;
  }

  /**
   * Ejecutamos la funcion LetPHP_Database::insert()
   * @param mixed $sTable Nombre de la tabla.
   * @param mixed $sFields Valores a insertar.
   * @param mixed $sValues Valores.
   * 
   * @return string
   */
  protected function _insert(string $sTable, string $sFields, string $sValues):string
  {
    return 'INSERT INTO ' . $sTable . ' ' .
      '        (' . $sFields . ')' .
      ' VALUES (' . $sValues . ')';
  }

  /**
   * Ejecutamos la funcion LetPHP_Database::update()
   * @param mixed $sTable Nombre de la tabla.
   * @param mixed $sSets Valores a actualizar.
   * @param mixed $sCond Valores.
   * 
   * @return string
   */
  protected function _update(string $sTable, string $sSets, string $sCond):string
  {
    return 'UPDATE ' . $sTable . ' SET ' . $sSets . ' WHERE ' . $sCond;
  }
  
  public function _join(string $sType, string $sTable, string $sAlias, $mParam = null)
  {
		if (!isset($this->_aQuery['join'])) {
			$this->_aQuery['join'] = '';
		}
		
		$this->_aQuery['join'] .= $sType . " " . $this->getTable($sTable) . " AS " . $sAlias;
		if (is_array($mParam)) 
		{
			$this->_aQuery['join'] .= "\n\tON(";
			$sJoins = '';
			foreach ($mParam as $sKey => $sValue) 
			{
				if (is_string($sKey)) 
				{
					$sJoins .= $this->_cond($sKey, $sValue);
					continue;
				}
				$sJoins .= $sValue . " ";
			}
			$this->_aQuery['join'] .= preg_replace("/^(AND|OR)(.*?)/i", "", trim($sJoins));
		} 
		else 
		{
			if (preg_match("/(AND|OR|=|LIKE)/", $mParam)) 
			{
				$this->_aQuery['join'] .= "\n\tON({$mParam}";
			}
			else 
			{
				exit('Ya no se permite usar "USING()" en consultas SQL.');
			}
		}
		$this->_aQuery['join'] = preg_replace("/^(AND|OR)(.*?)/i", "", trim($this->_aQuery['join'])) . ")\n";
  }
  
  public function _cond($sKey, $mValue)
  {
	  if (is_array($mValue)) 
	  {
			$sCond = 'AND ' . $sKey . '';
			$sKey = array_keys($mValue)[0];
			$sValue = array_values($mValue)[0];
			$sKey = strtolower($sKey);
			switch ($sKey) 
			{
				case '=':
					$sCond .= ' = ' . $sValue . ' ';
					break;
				case 'in':
					$sCond .= ' IN(' . $mValue[$sKey] . ')';
					break;
				case 'like':
					$sCond .= ' LIKE \'' . $sValue . '\' ';
					break;
			}
			return $sCond;

		}
		
		$sCond = 'AND ' . $sKey . ' = \'' . $this->escape($mValue) . '\' ';

		return $sCond;
  } 
  
  /**
     * Returns one field from a row
     *
     * @param string $sSql SQL query
     * @param resource $hLink SQL resource
     * @return mixed field value
     */
	private function _getField($sQuery, &$hLink)
  {
	  $sRes = '';
		$aRow = $this->getSingular($sQuery, false, $hLink);
		if ($aRow) 
		{
			$sRes = $aRow[0];
		}
		return $sRes;
	}
}
