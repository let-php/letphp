<?php
/** Class LetPHP_Filte_Output */
defined('LETPHP') or exit('NO EXISTE LETPHP');

/**
 * Limpia el texto que se ha agregado a la 
 * Base de datos, estos datos guardardos, ya han 
 * pasado por el proceso de limpia con la clase
 * LetPHP_Filter_Input.
 * 
 * @copyright LetCode IO
 * @author Rodrigo Hernández Ortiz
 * @package LetPHP\Filter\Output
 * @version .1
 */
class LetPHP_Filter_Output
{
	
  /**
	 * Metodo para limpiar caracteres HTML
   * @param string $sTxt
   * 
   * @return string
   */
  public function htmlspecialchars(string $sTxt):string
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
	 * Convertimos una cadena de texto a UTF-8.
	 * @param string $sTxt Texto a convertir
	 * 
	 * @return string
	 */
	public function convert(string $sTxt):string
	{ 
		return html_entity_decode($sTxt, null, 'UTF-8');
	}
	
	/**
	 * Limpiamos una URL. 
	 * @param mixed $sTxt
	 * 
	 * @return string
	 */
	public function convertUrls(string $sTxt):string
	{

		$sTxt = preg_replace("#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>", $sTxt);
		$sTxt = trim($sTxt);

		return $sTxt;
	}
	
	/**
		*
		*
		*/
	public static function getInstance()
	{
		return LetPHP::getClass('letphp.filter.output');
	}
  

}

?>