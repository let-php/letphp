<?php

class LetPHP_View_Bessie extends LetPHP_View
{

  public $sLeftCurlyBrace = "{";
  public $sRightCurlyBrace = "}";
  public $sReservedVarname = 'letphp';

  
  private $_sDbQstrRegexp = '"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"';
  private $_sSiQstrRegexp = '\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\'';
  private $_sVarBracketRegexp = '\[[\$|\#]?\w+\#?\]';
	private $_sSvarRegexp = '\%\w+\.\w+\%';
	private $_sFuncRegexp = '[a-zA-Z_]+';
  
  private $_aPhpBlocks = [];
  private $_aForeachElseStack = [];
	private $_aLiterals = [];
	
  public function __construct()
  {
    $this->_sQstrRegexp = '(?:' . $this->_sDbQstrRegexp . '|' . $this->_sSiQstrRegexp . ')';
		$this->_sDvarRegexp = '\$[a-zA-Z0-9_]{1,}(?:' . $this->_sVarBracketRegexp . ')*(?:\.\$?\w+(?:' . $this->_sVarBracketRegexp . ')*)*';
		$this->_sCvarRegexp = '\#[a-zA-Z0-9_]{1,}(?:' . $this->_sVarBracketRegexp . ')*(?:' . $this->_sVarBracketRegexp . ')*\#';
		$this->_sVarRegexp = '(?:(?:' . $this->_sDvarRegexp . '|' . $this->_sCvarRegexp . ')|' . $this->_sQstrRegexp . ')';
		$this->_sModRegexp = '(?:\|@?[0-9a-zA-Z_]+(?::(?>-?\w+|' . $this->_sDvarRegexp . '|' . $this->_sQstrRegexp .'))*)';		
  }

  public function render($sFile, $sContent, $bRemoveHeader = false)
  {
    $sData = $this->_convert($sContent, $bRemoveHeader);
		$sContent = '';
    $aLines = explode("\n", $sData);
    foreach ($aLines as $sLine)
		{
			if (preg_match("/<\?php(.*?)\?>/i", $sLine))
			{
				if (substr(trim($sLine), 0, 5) == '<?php')
				{
					$sContent .= trim($sLine) . "\n";
				}
				else
				{
					$sContent .= $sLine . "\n";
				}
			}
			else
			{
				$sContent .= $sLine . "\n";
			}
    }
    
    $sContent =  preg_replace("/defined\('LETPHP'\) or exit\('NO LETPHP!'\);/is", "", $sContent);
    $sContent = "<?php defined('LETPHP') or exit('NO EXISTE!'); ?>\n" . $sContent;
    if($oFile = @fopen($sFile, 'w+'))
    {
      fwrite($oFile, $sContent);
      fclose($oFile);
    }

  }

  private function _convert($sData, $bRemoveHeader = false)
  {
    $sLdq = preg_quote($this->sLeftCurlyBrace);
		$sRdq = preg_quote($this->sRightCurlyBrace);
		$aText = [];
		$sCompiledText = '';

    $sData = preg_replace("/\<\!letphp(.*?)\>/is", "", $sData);

    ## Agregar un Token de Seguridad para los Comentarios
    $sData = preg_replace_callback("/<form(.*?)>(.*?)<\/form>/is", array($this, '_convertForm'), $sData);
    
    ## Elimina los Comentarios
    $sData = preg_replace("/{$sLdq}\*(.*?)\*{$sRdq}/s", "", $sData);
    
    preg_match_all("!{$sLdq}\s*literal\s*{$sRdq}(.*?){$sLdq}\s*/literal\s*{$sRdq}!s", $sData, $aMatches);
    $this->_aLiterals = $aMatches[1];
    $sData = preg_replace("!{$sLdq}\s*literal\s*{$sRdq}(.*?){$sLdq}\s*/literal\s*{$sRdq}!s", stripslashes($sLdq . "literal" . $sRdq), $sData);
    
    ## Eliminamos la palabra reservada php 
		preg_match_all("!{$sLdq}\s*php\s*{$sRdq}(.*?){$sLdq}\s*/php\s*{$sRdq}!s", $sData, $aMatches);
    $this->_aPhpBlocks = $aMatches[1];
    $sData = preg_replace("!{$sLdq}\s*php\s*{$sRdq}(.*?){$sLdq}\s*/php\s*{$sRdq}!s", stripslashes($sLdq . "php" . $sRdq), $sData);

    $aText = preg_split("!{$sLdq}.*?{$sRdq}!s", $sData);
    
    preg_match_all("!{$sLdq}\s*(.*?)\s*{$sRdq}!s", $sData, $aMatches);
		$aTags = $aMatches[1];

		$aCompiledTags = array();
		$iCompiledTags = count($aTags);
		for ($i = 0, $iForMax = $iCompiledTags; $i < $iForMax; $i++)
		{
			$aCompiledTags[] = $this->_convertTag($aTags[$i]);
    }
    
    $iCountCompiledTags = count($aCompiledTags);
		for ($i = 0, $iForMax = $iCountCompiledTags; $i < $iForMax; $i++)
		{
			if ($aCompiledTags[$i] == '')
			{
				$aText[$i+1] = preg_replace('~^(\r\n|\r|\n)~', '', $aText[$i+1]);
			}
			$sCompiledText .= $aText[$i].$aCompiledTags[$i];
		}
    $sCompiledText .= $aText[$i];
    
    $sCompiledText = preg_replace('!\?>\n?<\?php!', '', $sCompiledText);

		$sCompiledText = '<?php /* Guardado: ' . date("F j, Y, g:i a", time()) . ' */ ?>' . "\n" . $sCompiledText;
    return $sCompiledText;
  }


  private function _convertForm($aMatches)
	{
		$sForm = $aMatches[1];
		$sData = $aMatches[2];
		$sForm = '<form' . stripslashes($sForm) . ">";
		if (strpos($sData, '{token}') === false)
		{
      $sForm .= "\n" . '<?php echo \'<div><input type="hidden" name="letphp[token]" value="\'. sha1(LetPHP::getConfig(\'main.token\')) . \'" /></div>\'; ?>';
    }
    
		$sForm .= stripslashes($sData) . "\n";
		$sForm .= '</form>' . "\n";


		return $sForm;
  }

  private function _convertTag($sTag)
	{
		@preg_match_all('/(?:(' . $this->_sVarRegexp . '|' . $this->_sSvarRegexp . '|\/?' . $this->_sFuncRegexp . ')(' . $this->_sModRegexp . '*)(?:\s*[,\.]\s*)?)(?:\s+(.*))?/xs', $sTag, $aMatches);

		if ($aMatches[1][0][0] == '$' || $aMatches[1][0][0] == "'" || $aMatches[1][0][0] == '"')
		{
			return "<?php echo " . $this->_convertVars($aMatches[1], $aMatches[2]) . "; ?>";
		}

		$sTagCommand = $aMatches[1][0];
		$sTagModifiers = !empty($aMatches[2][0]) ? $aMatches[2][0] : null;
		$sTagArguments = !empty($aMatches[3][0]) ? $aMatches[3][0] : null;
		return $this->_convertFunction($sTagCommand, $sTagModifiers, $sTagArguments);
  }


  private function _convertVars($aVariables, $aModifiers)
	{
		$sResult = "";
		foreach($aVariables as $mKey => $mValue)
		{
			if (empty($aModifiers[$mKey]))
			{
				$sResult .= $this->_convertVar(trim($aVariables[$mKey])).'.';
			}
			else
			{
				$sResult .= $this->_convertModifier($this->_convertVar(trim($aVariables[$mKey])), $aModifiers[$mKey]).'.';
			}
		}
		return substr($sResult, 0, -1);
  }


  private function _convertVar($sVariable)
	{
		if ($sVariable[0] == "\$")
		{
			return $this->_renderVar($sVariable);
		}
		else
		{
			return $sVariable;
		}
  }
  

  private function _renderVar($sVariable)
	{
		$sResult = '';
		$sVariable = substr($sVariable, 1);

		preg_match_all('!(?:^\w+)|(?:' . $this->_sVarBracketRegexp . ')|\.\$?\w+|\S+!', $sVariable, $aMatches);
		$aVariables = $aMatches[0];
		$sVarName = array_shift($aVariables);

		if ($sVarName == $this->sReservedVarname) 
		{
			if ($aVariables[0][0] == '[' || $aVariables[0][0] == '.')
			{
				$aFind = array("[", "]", ".");
				switch(strtoupper(str_replace($aFind, "", $aVariables[0])))
				{
					case 'GET':
						$sResult = "\$_GET";
						break;
					case 'POST':
						$sResult = "\$_POST";
						break;
					case 'COOKIE':
						$sResult = "\$_COOKIE";
						break;
					case 'ENV':
						$sResult = "\$_ENV";
						break;
					case 'SERVER':
						$sResult = "\$_SERVER";
						break;
					case 'SESSION':
						$sResult = "\$_SESSION";
						break;
					default:
						echo $sVar = str_replace($aFind, "", $aVariables[0]);
						$sResult = "\$this->_aLetphpVars['$sVar']";
						break;
				}
				array_shift($aVariables);
			}
			else
			{
				##Error::trigger('$' . $sVarName.implode('', $aVariables) . ' is an invalid  reference', E_USER_ERROR);
			}
		}
		else
		{
			$sResult = "\$this->_aVars['$sVarName']";
		}

		foreach ($aVariables as $sVar)
		{
			if ($sVar[0] == '[')
			{
				$sVar = substr($sVar, 1, -1);
				if (is_numeric($sVar))
				{
					$sResult .= "[$sVar]";
				}
				elseif ($sVar[0] == '$')
				{
					$sResult .= "[" . $this->_convertVar($sVar) . "]";
				}
				else
				{
					$parts = explode('.', $sVar);
					$section = $parts[0];
					$section_prop = isset($parts[1]) ? $parts[1] : 'index';
					$sResult .= "[\$this->_aSections['$section']['$section_prop']]";
				}
			}
			elseif ($sVar[0] == '.')
			{
   				$sResult .= "['" . substr($sVar, 1) . "']";
			}
			elseif (substr($sVar,0,2) == '->')
			{
				##Error::trigger('Call to object members is not allowed', E_USER_ERROR);
			}
			else
			{
				##Error::trigger('$' . $sVarName.implode('', $aVariables) . ' is an invalid reference', E_USER_ERROR);
			}
		}
		return $sResult;
  }
  


	public function _convertModifier($sVariable, $sModifiers)
	{
		$aMods = [];
		$aArgs = [];
		$aMods = explode('|', $sModifiers);
		unset($aMods[0]);
		foreach ($aMods as $sMod)
		{
			$aArgs = array();
			if (strpos($sMod, ':'))
			{
				$aParts = explode(':', $sMod);
				$iCnt = 0;
				foreach ($aParts as $iKey => $sPart)
				{
					if ($iKey == 0){ continue; }
					if ($iKey > 1){ $iCnt++; }
					$aArgs[$iCnt] = $this->_convertVar($sPart);
				}
				$sMod = $aParts[0];
			
			}
		}


	}




	private function _convertFunction($sMethod, $sModifiers, $sArguments)
	{
		switch($sMethod)
		{

			case 'leftCurly': 
				return $this->sLeftCurlyBrace;
			break;

			case 'left_curly': 
				return $this->sLeftCurlyBrace;
			break;
			case 'left': 
				return $this->sLeftCurlyBrace;
			break;

			case 'rightCurly': 
				return $this->sRightCurlyBrace;
			break;
			case 'curly_right': 
				return $this->sRightCurlyBrace;
			break;
			case 'right': 
				return $this->sRightCurlyBrace;
			break;
			case 'title':
			case 'site_title':
			case 'sitetitle':
				return '<?php echo LetPHP::getClass(\'letphp.view\')->getTitle(); ?>';
				break;
			case 'name':
			case 'site_name': 
			case 'sitename':
				return LetPHP::getConfig('main.site_name');
			break;
			case 'route': 
				$aParams = $this->_convertParams($sArguments);
				if(!isset($aParams['link']))
				{
					return '';
				}
				$sLink = $aParams['link'];
				unset($aParams['link']);
				$sArray = '';
				if (count($aParams))
				{
					$sArray = ', array(';
					foreach ($aParams as $sKey => $sValue)
					{
						$sArray .= '\'' . $sKey . '\' => ' . $sValue . ',';
					}
					$sArray = rtrim($sArray, ',') . ')';
				}
				
				return '<?php echo LetPHP::getClass(\'letphp.router\')->createRoute('.$sLink.$sArray.') ?>';
			break;

			case 'message':   
				return '<?php  echo $this->getView(\'message\'); ?>';
				break;

      case 'content':
      case 'sitecontent':
			case 'site_content':
				$sContent = '<?php LetPHP::getClass(\'letphp.app\')->getControllerViewApp(); ?>';
				return $sContent;
			break;
			case 'meta':
				return '<?php echo LetPHP::getClass(\'letphp.view\')->getMeta(); ?>';
				break;
			case 'css':
			case 'loadcss': 
				return '<?php echo LetPHP::getClass(\'letphp.view\')->getCss(); ?>';
				break;
			
			case 'js':
			case 'loadjs':
				return '<?php echo LetPHP::getClass(\'letphp.view\')->getJScript(); ?>';
				break;
			case 'for':
				$sArguments = preg_replace_callback("/\\$([A-Za-z0-9]+)/is", function ($matches) {
					return $this->_convertVar($matches[0]);
				}, $sArguments);
				
				return '<?php for (' . $sArguments . '): ?>';
				break;
			case '/for':
				return "<?php endfor; ?>";
				break;
			case 'each': 
				$aParams = $this->_convertParams($sArguments);

				if (!isset($aParams['values']))
				{
					return '';
				}
				
				if (!isset($aParams['value']) && !isset($aParams['item']))
				{
					return '';
				}
				if (isset($aParams['value']))
				{
					$aParams['value'] = $this->_removeQuote($aParams['value']);
				}
				elseif (isset($aParams['item']))
				{
					$aParams['value'] = $this->_removeQuote($aParams['item']);
				}

				(isset($aParams['key']) ? $aParams['key'] = "\$this->_aVars['".$this->_removeQuote($aParams['key'])."'] => " : $aParams['key'] = '');

				$bIteration = (isset($aParams['name']) ? true : false);

				$sResult = '<?php if (count((array)' . $aParams['values'] . ')): ?>' . "\n";
				if ($bIteration)
				{
					$sResult .= '<?php $this->_aLetphpVars[\'iteration\'][\'' . $aParams['name'] . '\'] = 0; ?>' . "\n";
				}
				$sResult .= '<?php foreach ((array) ' . $aParams['values'] . ' as ' . $aParams['key'] . '$this->_aVars[\'' . $aParams['value'] . '\']): ?>';
				if ($bIteration)
				{
					$sResult .= '<?php $this->_aLetphpVars[\'iteration\'][\'' . $aParams['name'] . '\']++; ?>' . "\n";
				}

				return $sResult;
				break;
			case 'eachelse':
				$this->_aForeachElseStack[count($this->_aForeachElseStack)-1] = true;
				return "<?php endforeach; else: ?>";
				break;

			case '/each':
				if (array_pop($this->_aForeachElseStack))
				{
					return "<?php endif; ?>";
				}
				else
				{
					return "<?php endforeach; endif; ?>";
				}
				break;
			case 'if':
				return $this->_renderIf($sArguments);
				break;
			case 'else':
				return "<?php else: ?>";
				break;
			case 'elseif':
				return $this->_renderIf($sArguments, true);
				break;
			case '/if':
				return "<?php endif; ?>";
				break;
				
			case 'itemscope':
				$aParams = $this->_convertParams($sArguments);
				$sType = $this->_removeQuote($aParams['type']);
				return '<article itemscope itemtype="http://schema.org/'.$sType.'" >';
				break;
			
			case '/itemscope':
				return '</article>';
				break;
			case 'literal':
				@list (,$sLiteral) = each($this->_aLiterals);
				return "<?php echo '" . str_replace("'", "\'", $sLiteral) . "'; ?>\n";
				break;
			
			case 'fragment':
				$aParams = $this->_convertParams($sArguments);
				$sFragment = $aParams['route'];
				unset($aParams['route']);
				$sArray = '';
				foreach($aParams as $sKey => $sValue)
				{
					if(substr($sValue, 0, 1) != '$' && $sValue !== 'true' && $sValue !== 'false')
					{
						$sValue = '\''.$this->_removeQuote($sValue).'\'';
					}
					$sArray .= '\''.$sKey.'\' => '.$sValue.',';
				}
				return '<?php LetPHP::getFragment('.$sFragment.', ['.rtrim($sArray, ',').']); ?>';
				break;
			case 'view': 
				$aParams = $this->_convertParams($sArguments);
				$sFile = $this->_removeQuote($aParams['file']); 
				return '<?php 
					LetPHP::getClass(\'letphp.view\')->getBuiltViewApp(\''.$sFile.'\'); 
				?>';
				break;
			case 'Config':
			case 'config': 
				$aParams = $this->_convertParams($sArguments);
				$sName = $this->_removeQuote($aParams['name']);
				return '<?php echo Config(\''.$sName.'\') ?>';
				break;
			case 'paginator': 
				$sReturn = '<?php LetPHP::getClass(\'letphp.view\')->getView(\'paginator\'); ?>';
				return $sReturn;
				break;
			case 'time':
				return '<?php echo LETPHP_TIME; ?>';
				break;
			case 'date':
				$aParams = $this->_convertParams($sArguments);
				if(isset($aParams['time']) AND ($aParams['time'] != '') )
				{
					return '<?php echo date( '.$aParams['format'].', '.$aParams['time'].' ); ?>';
				}
				else 
				{
					return '<?php echo date( '.$aParams['format'].', LETPHP_TIME); ?>';
				}
				break;
			default: 
				// existe fragment App
				$sFragment = $sMethod;
				if($sFragment != '')
				{
					$aParams = $this->_convertParams($sArguments);
					$sArray = '';
					foreach($aParams as $sKey => $sValue)
					{
						if(substr($sValue, 0, 1) != '$' && $sValue !== 'true' && $sValue !== 'false')
						{
							$sValue = '\''.$this->_removeQuote($sValue).'\'';
						}
						$sArray .= '\''.$sKey.'\' => '.$sValue.',';
					} 
					$sFragment = strtolower(str_replace('_', '.', $sFragment));
					return '<?php LetPHP::getFragment("'.$sFragment.'", ['.rtrim($sArray, ',').']); ?>'; 
				}

		}
		

  }

	private function _renderIf($sArguments, $bElseif = false, $bWhile = false)
	{
		$aAllowed = ['defined', 'is_array', 'isset', 'empty', 'count', '='];
		
		$sResult = "";
		$aArgs = array();
		$aArgStack	= array();

		preg_match_all('/(?>(' . $this->_sVarRegexp . '|\/?' . $this->_sSvarRegexp . '|\/?' . $this->_sFuncRegexp . ')(?:' . $this->_sModRegexp . '*)?|\-?0[xX][0-9a-fA-F]+|\-?\d+(?:\.\d+)?|\.\d+|!==|===|==|!=|<>|<<|>>|<=|>=|\&\&|\|\||\(|\)|,|\!|\^|=|\&|\~|<|>|\%|\+|\-|\/|\*|\@|\b\w+\b|\S+)/x', $sArguments, $aMatches);
		$aArgs = $aMatches[0];
		
		$iCountArgs = count($aArgs);
		for ($i = 0, $iForMax = $iCountArgs; $i < $iForMax; $i++)
		{
			$sArg = &$aArgs[$i];
			switch (strtolower($sArg))
			{
				case '!':
				case '%':
				case '!==':
				case '==':
				case '===':
				case '>':
				case '<':
				case '!=':
				case '<>':
				case '<<':
				case '>>':
				case '<=':
				case '>=':
				case '&&':
				case '||':
				case '^':
				case '&':
				case '~':
				case ')':
				case ',':
				case '+':
				case '-':
				case '*':
				case '/':
				case '@':
					break;
				case 'eq':
					$sArg = '==';
					break;
				case 'ne':
				case 'neq':
					$sArg = '!=';
					break;
				case 'lt':
					$sArg = '<';
					break;
				case 'le':
				case 'lte':
					$sArg = '<=';
					break;
				case 'gt':
					$sArg = '>';
					break;
				case 'ge':
				case 'gte':
					$sArg = '>=';
					break;
				case 'and':
					$sArg = '&&';
					break;
				case 'or':
					$sArg = '||';
					break;
				case 'not':
					$sArg = '!';
					break;
				case 'mod':
					$sArg = '%';
					break;
				case '(':
					array_push($aArgStack, $i);
					break;
				
				default:
					preg_match('/(?:(' . $this->_sVarRegexp . '|' . $this->_sSvarRegexp . '|' . $this->_sFuncRegexp . ')(' . $this->_sModRegexp . '*)(?:\s*[,\.]\s*)?)(?:\s+(.*))?/xs', $sArg, $aMatches);

					if (isset($aMatches[0][0]) && ($aMatches[0][0] == '$' || $aMatches[0][0] == "'" || $aMatches[0][0] == '"'))
					{
						$sArg = $this->_convertVars([$aMatches[1]], [$aMatches[2]]);
					}
					
					
					break;
			}
		}
		
		if($bWhile)
		{
			return implode(' ', $aArgs);
		}
		else
		{
			if ($bElseif)
			{
				return '<?php elseif ('.implode(' ', $aArgs).'): ?>';
			}
			else
			{
				return '<?php if ('.implode(' ', $aArgs).'): ?>';
			}
		}

		return $sResult;
	}


	private function _convertParams($sArguments)
	{
		$aResult	= array();
		preg_match_all('/(?:' . $this->_sQstrRegexp . ' | (?>[^"\'=\s]+))+|[=]/x', $sArguments, $aMatches);

		$iState = 0;
		foreach($aMatches[0] as $mValue)
		{
			switch($iState)
			{
				case 0:
					if (is_string($mValue))
					{
						$sName = $mValue;
						$iState = 1;
					}
					else
					{
						/// Error
						echo 'Parametro Invalido';
					}
					break;
				case 1:
					if ($mValue == '=')
					{
						$iState = 2;
					}
					else
					{

						/// Error
						//echo " Expecting '=' After '{$sLastValue}' ";
						 
					}
					break;
				case 2:
					if ($mValue != '=')
					{
						if(!preg_match_all('/(?:(' . $this->_sVarRegexp . '|' . $this->_sSvarRegexp . ')(' . $this->_sModRegexp . '*))(?:\s+(.*))?/xs', $mValue, $aVariables))
						{
							$aResult[$sName] = $mValue;
						}
						else
						{
							$aResult[$sName] = $this->_convertVars($aVariables[1], $aVariables[2]);
						}
						$iState = 0;
					}
					else
					{

						/// Error
						echo "'=' cannot be an attribute value";
					}
					break;
			}
			$sLastValue = $mValue;
		}

		if($iState != 0)
		{
			if($iState == 1)
			{
				// Error
				echo "expecting '=' after attribute name '{$sLastValue}'";
			}
			else
			{
				// Error
				echo "missing attribute value";
			}
		}
		return $aResult;
  }
  

	private function _removeQuote($string)
	{
		if (($string[0] == "'" || $string[0] == '"') && $string[strlen($string)-1] == $string[0])
		{
			return substr($string, 1, -1);
		}
		else
		{
			return $string;
		}
	}


  private function _getToken()
  {
    return sha1(LetPHP::getConfig('main.token'));
  }
  
  
  
	/**
	 * Compile custom function into the template it is loaded in.
	 *
	 * @param string $sFunction Name of the function.
	 * @param string $sModifiers Modifier to load.
	 * @param string $sArguments Arguments of the function.
	 * @param string $sResult Converted string of the PHP function.
	 * @return bool TRUE function converted, FALSE if it didn't convert.
	 */
	private function _renderFunctionLetPHP($sFunction, $sModifiers, $sArguments, &$sResult)
	{
		
		//echo 'SS', $sFunction. $sModifiers. $sArguments. 'No';

		//if ($sFunction = $this->_plugin($sFunction, "function"))
		//{
			
			
			$aArgs = $this->_convertParams($sArguments);
			
			foreach($aArgs as $mKey => $mValue)
			{
				if (is_bool($mValue))
				{
					$mValue = $mValue ? 'true' : 'false';
				}
				if (is_null($mValue))
				{
					$mValue = 'null';
				}
				$aArgs[$mKey] = "'$mKey' => $mValue";
			}
			
			$sResult = '<?php echo ';
			if (!empty($sModifiers))
			{
				$sResult .= $this->_convertModifier($sFunction . '(array(' . implode(',', (array)$aArgs) . '), $this)', $sModifiers) . '; ';
			}
			else
			{
				$sResult .= $sFunction . '(array(' . implode(',', (array)$aArgs) . '), $this);';
			}
			$sResult .= '?>';

			return true;
		//}
		//else
		//{
			//return false;
		//}
	}
  
}

?>