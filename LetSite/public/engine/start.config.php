<?php

namespace 
{
	$_classes = [];
	
	class LetPHP_Classes_Container
	{
		public function __construct()
		{
			if(file_exists(LETPHP_LETSITE_ENGINE. 'classes.'. LETPHP_CONFIG_SUFFIX))
			{
				$this->_classes = require(LETPHP_LETSITE_ENGINE. 'classes.'. LETPHP_CONFIG_SUFFIX);
			}
		}
		
		public function getClass(string $sType, string $sItem = null)
		{
			if(!isset($this->_classes[$sType])){ return null; }
			if(!$sItem){ return $this->_classes[$sType]; }
			if(!isset($this->_classes[$sType][$sItem])){ return null; }
			return $this->_classes[$sType][$sItem];
		}
	}	
	
	class LetPHP_Class_Container
	{
		
		private $_classes = [];
		const TYPE = 'classes';
		public function getClass(string $sClass)
		{
			$sClass = str_replace('letphp.', '', str_replace('_', '.', strtolower($sClass)));
			return (isset($this->_classes[$sClass])? $this->_classes[$sClass] : $this->_classes[$sClass] = $this->buildClass($sClass));
		} 
		
		public function buildClass(string $sClass)
		{
			$mObject = LetPHP::getClassCore(self::TYPE, $sClass);
			$class = null;
			if(is_string($mObject))
			{
				return new $mObject;
			}
			
		}
	}
}	
	
?>