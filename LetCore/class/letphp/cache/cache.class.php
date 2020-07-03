<?php
/** Class LetPHP_Cache */
defined('LETPHP')	or exit('NO EXISTE LETPHP');

/**
 * Con esta clase administramos el cache 
 * que se va guardando en todo tú sitio
 * ya sea SQL, Archivos JS/CSS y Plantillas.
 * 
 * 
 * 
 * @copyright LetCode IO
 * @author Rodrigo Hernández Ortiz
 * @package LetPHP\Cache
 * @version .1
 */
class LetPHP_Cache
{
	
	/** 
		* Arreglo de los archivos que se han guardado en cache
		* @var array $_aNameCache
	*/
	private $_aNameCache = [];
	
	/** 
		* Nombre del Archivo que guarda cache
		* @var string $_sNameCache
	*/
	private $_sNameCache = '';
	
	
	/**
	 * Nombre del como se guardara el cache.
	 * @param string $sName Nombre del Cache.
	 * 
	 * @return string
	 */
	public function setCache(string $sName = ''):string
	{
		$sId = $sName;
		$this->_aNameCache[$sId] = $sName;	 
		$this->_sNameCache = $sName;
		return $sId;
	}
	
	/**
	 * Obtenemos el cache guardado, atraves de su
	 * nombre que se ha guardado con la function setCache()
	 * 
	 * @param string $sId
	 * 
	 * @return mixed
	 */
	public function getCache(string $sId = '')
	{
		if(!$this->isCached($sId))
		{
			return false;
		}
		require($this->_getNameFile($this->_aNameCache[$sId]));
		
		## si no existe el contenido
		if(!isset($aContent))
		{
			return false;
		}
		
		if(!is_array($aContent) && empty($aContent))
		{
			return true;
		}
		
		if (is_array($aContent) && !count($aContent))
		{
			return true;
		}
		return $aContent;
	}
	
	/**
	 * Guardamos el cache.
	 * @param mixed $sId Nombre del cache
	 * @param mixed $mContent Contenido que se guardará.
	 * 
	 * @return void
	 */
	public function saveCache($sId, $mContent)
	{
		$sContent = '<?php defined(\'LETPHP\') or exit(\'NO EXISTE LETPHP\') ?>'. "\n";
		$sContent .= '<?php $aContent = '.var_export($mContent, true).' ?>';
		$this->_getNameFile($this->_aNameCache[$sId]);
		if($oOpen = @fopen($this->_getNameFile($this->_aNameCache[$sId]), 'w+'))
		{
			fwrite($oOpen, $sContent);
			fclose($oOpen);
		}
	}
	
	/**
	 * Comprobamos si existe el cache y se actualiza
	 * la información del cache. 
	 * 
	 * @param string $sId  Archivo unico, con el cual se ha guardado.
	 * @param int $iTime Minutos para almacena el cache
	 * 
	 * @return bool
	 */
	public function isCached(string $sId = '', int $iTime = 0):bool
	{
		if((isset($this->_aNameCache[$sId])) && (file_exists($this->_getNameFile($this->_aNameCache[$sId]))))
		{
			if($iTime && (LETPHP_TIME - $iTime * 60 ) > (filemtime($this->_getNameFile($this->_aNameCache[$sId]))) )
			{
				$this->removeCache($this->_aNameCache[$sId]);
				return false;
			}
			return true;
		}
		
		return false;
	}
	
	/**
	 * Eliminamos el cache guardado
	 * @param string $sName Nombre del archivo a eliminar.
	 * 
	 * @return void
	 */
	public function removeCache(string $sName = '')
	{
		
		if($sName == '')
		{
			foreach($this->getFilesCache() as $aFile)
			{
				if(file_exists(LETPHP_LETCORE_DIRS_CACHE. $aFile['name']))
				{
					if(is_dir(LETPHP_LETCORE_DIRS_CACHE. $aFile['name']))
					{
						$this->delete_directory(LETPHP_LETCORE_DIRS_CACHE. $aFile['name']);
					}
					else 
					{
						unlink(LETPHP_LETCORE_DIRS_CACHE. $aFile['name']);
					}
				}
			}
		}
		
		$sName = $this->_getNameFile($sName);
		if(file_exists($sName))
		{					
			@unlink($sName);
		}
	}
	
	/**
	 * Obtenemos los archivos almacenados
	 * en cache.
	 * @return mixed
	 */
	public function getFilesCache()
	{
		static $aFiles = [];
		
		if($aFiles)
		{
			return $aFiles;
		}
		
		if($oDirCache = @opendir(LETPHP_LETCORE_DIRS_CACHE))
		{
			while($sFile = readdir($oDirCache))
			{
				
				if ($sFile == '.' 
					|| $sFile == '..' 
					|| $sFile == '.svn'
					|| $sFile == '.htaccess'
					|| $sFile == 'index.html'
					|| $sFile == 'debug.php'					
				)
				{
					continue;
				}	
				
				
				$aFiles[] = array(
					'id' => sha1($sFile),
					'name' => $sFile,
					'size' => filesize(LETPHP_LETCORE_DIRS_CACHE . $sFile),
					'date' => filemtime((LETPHP_LETCORE_DIRS_CACHE . $sFile)),
					'type' => 'File'
				);	
				
			}
			
			closedir($oDirCache);
			return $aFiles;
		}
		return [];
	}
	
	
	/**
	 * Eliminamos el directorio
	 * 
	 * @param mixed $dir Directorio a Eliminar.
	 * 
	 * @return void
	 */
	public function delete_directory($dir)
	{
        if(is_dir($dir)) 
        {
        	if($dh = opendir($dir)) 
        	{
            	while(($file = readdir($dh)) !== false) 
            	{
                	if($file != '.' && $file != '..') 
                	{
                    	if(is_dir($dir . '/' . $file)) 
                    	{
                        	$this->delete_directory($dir . '/' . $file);
						} 
						else
						{
                        	unlink($dir . '/' . $file);
                         }
                	}
				}
        	}
        	closedir($dh);
        	@rmdir($dir);
        }
	}
	
	/**
		*
		*
		*/
	public static function getInstance()
	{
		return LetPHP::getClass('letphp.cache');
	}
	
	/**
	 * Ruta del archivo, guardado en cache.
	 * @param string $sFile Nombre del Archivo.
	 * 
	 * @return string
	 */
	private function _getNameFile(string $sFile = ''):string
	{
	 	return LETPHP_LETCORE_DIRS_CACHE. $sFile. '.'. LETPHP_CACHE_SUFFIX;
	}

	
}
