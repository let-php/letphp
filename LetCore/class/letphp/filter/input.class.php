<?php 
/** Class LetPHP_Filter_Input */
defined('LETPHP') or exit('NO EXISTE LETPHP');

/**
 * Esta clase filtra y analiza todos los datos 
 * enviado desde los formularios HTML llenados 
 * por los usuarios finales.
 * 
 * 
 * @copyright LetCode IO
 * @author Rodrigo Hernández Ortiz
 * @package LetPHP\Filter\Input
 * @version .1
 * 
 */
class LetPHP_Filter_Input
{
	
	/**
   * Limpia y recorta una cadena.
	 * @param string $sTxt Texto a limpiar.
	 * @param int $iShorten Numero de caracteres a mostrar.
	 * 
	 * @return string
	 */
	public function cleanStr(string $sTxt , int $iShorten = 0 ):string
	{
		$sTxt = $this->htmlspecialchars($sTxt);
		$sTxt = $this->_utf8ToUnicode($sTxt);
		$sTxt = str_replace('\\', '&#92;', $sTxt);
		
		if ($iShorten > 0 )
		{			
			$sTxt = $this->_shorten($sTxt, $iShorten);
		}	
		return $sTxt;
	}
	
	/**
   * Limpiar caracteres especiales de HTML
	 * @param mixed $sTxt cadena a limpiar.
	 * 
	 * @return string
	 */
	public function htmlspecialchars($sTxt):string
	{
		$sTxt = preg_replace('/&(?!(#[0-9]+|[a-z]+);)/si', '&amp;', $sTxt);
		$sTxt = str_replace(array(
			'"',
			"'",
			'<',
			'>'
		),
		array(
			'&quot;',
			'&#039;',
			'&lt;',
			'&gt;'
		), $sTxt);		
		
		return $sTxt;
	}

	
	/**
	 * Convierte el titulo y lo limpia de caracteres.
	 * @param string $sTitle titulo a convertir.
	 * 
	 * @return string
	 */
	public function parseTitle(string $sTitle ):string
	{
		$sParseTitle = trim( strip_tags( $sTitle ) );
		$sParseTitle = $this->_utf8ToUnicode($sParseTitle, true);		
		$sParseTitle = preg_replace("/ +/", "-", $sParseTitle);		
		$sParseTitle = rawurlencode($sParseTitle);		
		$sParseTitle = str_replace(array('"', "'"), '', $sParseTitle);
		$sParseTitle = str_replace(' ', '-', $sParseTitle);
		$sParseTitle = str_replace(array('-----', '----', '---', '--'), '-', $sParseTitle);
		$sParseTitle = rtrim($sParseTitle, '-');
		$sParseTitle = ltrim($sParseTitle, '-');
		
		if (empty($sParseTitle))
		{
			$sParseTitle = LETPHP_TIME;
		}
		
		$sParseTitle= strtolower($sParseTitle);

		return $sParseTitle;
		
	}
	
	/**
	 * Convierte un cadenas de texto que contienen 
	 * caracteres no-latin a UNICODE
	 * @param mixed $sTxt cadena a convertir.
	 * 
	 * @return string
	 */
	public function convertStr(string $sTxt ):string
	{
		return $this->_utf8ToUnicode( $sTxt );
	}
	
	/**
		*
		*
		*/
	public static function getInstance()
	{
		return LetPHP::getClass('letphp.filter.input');
	}
	
	 /**
		* Converte UTF8 a Unicode
	  * @param mixed $str
	  * @param bool $bForUrl
	  * 
	  * @return string
	  */
	 private function _utf8ToUnicode($str, $bForUrl = false)
    {
        $unicode = array();
        $values = array();
        $lookingFor = 1;
        if(defined('LETCODE_UNICODE_JSON') && LETCODE_UNICODE_JSON === true)
        {
            $aUnicodes = preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
            foreach($aUnicodes as $char)
            {
                $thisValue = ord($char);
                if ($thisValue < 128)
                {
                    $unicode[] = $thisValue;
                }
                else
                {
                    $unicode[] = hexdec(trim(trim(json_encode($char), '"'), '\u'));
                }
            }
        }
        else
        {
            for ($i = 0; $i < strlen( $str ); $i++ )
            {
                $thisValue = ord( $str[ $i ] );

                if ( $thisValue < 128 )
                {
                    $unicode[] = $thisValue;
                }
                else
                {
                    if ( count( $values ) == 0 ) $lookingFor = ( $thisValue < 224 ) ? 2 : 3;

                    $values[] = $thisValue;

                    if ( count( $values ) == $lookingFor ) 
                    {
                        $number = ( $lookingFor == 3 ) ?
                            ( ( $values[0] % 16 ) * 4096 ) + ( ( $values[1] % 64 ) * 64 ) + ( $values[2] % 64 ):
                            ( ( $values[0] % 32 ) * 64 ) + ( $values[1] % 64 );

                        $unicode[] = $number;
                        $values = array();
                        $lookingFor = 1;
                    }
                }
            }
        }
        return $this->_unicodeToEntitiesPreservingAscii($unicode, $bForUrl);
    }
    
    
    /**
		 * Este metodo es usado en la funciones _utf8ToUnicode.
     * @param mixed $unicode
     * @param bool $bForUrl
     * 
     * @return string
     */
    private function _unicodeToEntitiesPreservingAscii($unicode, $bForUrl = false)
    {
        $entities = '';
        foreach( $unicode as $value )
        {
        	if ($bForUrl === true)
        	{
        		if ($value == 42 || $value > 127)
        		{
							$sCacheValue = '';
        			$entities .= (preg_match('/[^a-zA-Z]+/', $sCacheValue) ? '-' . $value : $sCacheValue);   			
        		}
        		else 
        		{
        			$entities .= (preg_match('/[^0-9a-zA-Z]+/', chr($value)) ? ' ' : chr($value));
        		}        		
        	}
        	else 
        	{
        		$entities .= ($value == 42 ? '&#' . $value . ';' : ( $value > 127 ) ? '&#' . $value . ';' : chr($value));
        	}
        }
		$entities = str_replace("'", '&#039;', $entities);
			
			//echo '<br/>', $entities;
		
        return $entities;
    }
    
    
    /**
		 * Limita una cadena de texto
     * @param mixed $sTxt
     * @param mixed $iLetters
     * 
     * @return string
     */
    private function _shorten($sTxt,$iLetters)
    {
	    if (!preg_match('/(&#[0-9]+;)/', $sTxt))
		{
			return substr($sTxt, 0, $iLetters);
		}
		$sOut = '';
		$iOutLen = 0;
		$iPos = 0; 
		$iTxtLen = strlen($sTxt);
		for ($iPos; $iPos < $iTxtLen && $iOutLen <= $iLetters; $iPos++)
		{
			if ($sTxt[$iPos] == '&')
			{
				$iEnd = strpos($sTxt, ';', $iPos) + 1;
				$sTemp = substr($sTxt, $iPos, $iEnd - $iPos);
				if (preg_match('/(&#[0-9]+;)/', $sTemp))
				{
					$sTmp = $sOut;
					$sOut .= $sTemp; // add the entity altogether
					if (strlen($sOut) > $iLetters)
					{
						return $sTmp;
					}
					$iOutLen++; // increment the length of the returning string
					$iPos = $iEnd-1; // move the pointer to skip the entity in the next run
					continue;
				}
			}
			$sOut .= $sTxt[$iPos];
			$iOutLen++;
		}
		return $sOut;	    
    }
    

}
