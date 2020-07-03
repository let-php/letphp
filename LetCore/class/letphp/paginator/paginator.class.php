<?php
/** Class LetPHP_Paginator */
defined('LETPHP') or exit('NO EXISTE LETPHP');


/**
	* Construimos un paginador con la
	* ayuda de esta clase.
	* 
	*
	*	
	*/
class LetPHP_Paginator
{
	
	/**
		*
		*/
	private $_oRouter = null;
	
	
	/**
		*
		*/
	private $_sRoute = '';
	
	
	/**
		**
		*/
	private $_sRequest = '';
	
	/*
		* Pagina Actual
	*/
	private $_iPageCurrent = 0;
	
	/*
		* Total de Items a mostrar
	*/
	private $_iTotalItemsPage = 0;
	
	/*
		* Total de paginas
	*/
	private $_iTotalPages = 0;
	
	/*
		* Primera Página
	*/
	private $_iRecordStar = 0;
	
	/*
		* Última Página
	*/
	private $_iRecordEnd = 0;
	
	private $_iQuantity = 0;
	
	private $_iTotal = 0;
	
	private $_aPagesNumbers = [];
	
	
	public function __construct()
	{
		$this->_oRouter = LetPHP::getClass('letphp.router');
		$this->_sRoute = LetPHP::getClass('letphp.app')->getAppName();
		$this->_sRequest = 'page';
	}
	
	
	/**
		* Cambiamos la variable get del browser
		* @param string $sValue Nombre del nueva variable.
		* @return object LetPHP_Paginator
		*/
	public function request(string $sValue):object
	{
		$this->_sRequest = $sValue;
		return $this;
	}
	
	/**
		* Asignamos una ruta
		* @return object LetPHP_Paginator
		*/
	public function route(string $sRoute ): object
	{
		$this->_sRoute = $sRoute;
		return $this;
	}
	
	
	public function page(int $iPage): object
	{
		$this->_iPageCurrent = intval($iPage);
		return $this;
	}
	
	public function quantity(int $iQuantity):object
	{
		$this->_iQuantity = intval($iQuantity);
		return $this;
	}
	
	/**
		* Indicamos el Total
		* @param int $iTotal 
		*/
	public function total(int $iTotal): object
	{
		$this->_iTotal = intval($iTotal);
		return $this;
	}
	
	
	
	/**
		* Construimos el paginador
		*
		*/
	public function buildPaginator()
	{	
		if(($this->_iTotal > 0) && ($this->_iPageCurrent > 1))
		{
			$this->_iRecordStart = 1 + ($this->_iQuantity * ($this->_iPageCurrent - 1) );
			$this->_iRecordEnd = ($this->_iRecordStart + $this->_iQuantity ) - 1;
			if($this->_iTotal < $this->_iRecordEnd){ $this->_iRecordEnd = $this->_iTotal; }
		}
		
		if(($this->_iTotal > 0) && ($this->_iPageCurrent <= 1) )
		{
			$this->_iRecordStart = 1;
			$this->_iRecordEnd = ($this->_iRecordStart + $this->_iTotal ) - 1;
			if($this->_iTotal < $this->_iRecordEnd ){ $this->_iRecordEnd = $this->_iTotal; }
		}
		
		if($this->_iTotal > 0)
		{
			$aPaginator['iTotalResults'] = $this->_iTotal;
			$aPaginator['sPhraseResultsFound'] = $this->_iTotal. ' Resultados.';
		}
		
		$this->_iTotalPages = intval($this->_iTotal/$this->_iQuantity) + 1;
		if((intval($this->_iTotal / $this->_iQuantity)) == ($this->_iTotal/ $this->_iQuantity))
		{
			$this->_iTotalPages = $this->_iTotalPages - 1;
		}
		
		$aPageNumbers = [];
		if(($this->_iPageCurrent <= 3) || ($this->_iTotalPages <= 5) )
		{
			
		}
		if(($this->_iPageCurrent > 3) && ($this->_iTotalPages > 5))
		{
			$aPageNumbers[0]['iPage'] = $this->_iPageCurrent -2;
			$aPageNumbers[0]['sRoute'] = $aPageNumbers[0]['iPage'];
			
			$aPageNumbers[1]['iPage'] = $this->_iPageCurrent -1;
			$aPageNumbers[1]['sRoute'] = $aPageNumbers[1]['iPage'];
			
			
			$aPageNumbers[2]['iPage'] = $this->_iPageCurrent;
			$aPageNumbers[2]['sRoute'] = $aPageNumbers[2]['iPage'];
			
			if($this->_iTotalPages >= $this->_iPageCurrent + 1 )
			{
				$aPageNumbers[3]['iPage'] = $this->_iPageCurrent + 1;
				$aPageNumbers[3]['sRoute'] = $aPageNumbers[3]['iPage'];
			}
			if($this->_iTotalPages >= $this->_iPageCurrent + 2 )
			{
				$aPageNumbers[4]['iPage'] = $this->_iPageCurrent + 2;
				$aPageNumbers[4]['sRoute'] = $aPageNumbers[4]['iPage'];
			}
		}
		else
		{
			if($this->_iTotalPages >= 3)
			{
				$aPageNumbers[0]['iPage'] = 1;
				$aPageNumbers[0]['sRoute'] = 1;
			}
			
			if($this->_iTotalPages >= 3)
			{
				$aPageNumbers[1]['iPage'] = 2;
				$aPageNumbers[1]['sRoute'] = 2;
			}
			
			if($this->_iTotalPages >= 3)
			{
				$aPageNumbers[2]['iPage'] = 3;
				$aPageNumbers[2]['sRoute'] = 3;
			}
			
			if($this->_iTotalPages >= 4)
			{
				$aPageNumbers[3]['iPage'] = 4;
				$aPageNumbers[3]['sRoute'] = 4;
			}
			if($this->_iTotalPages >= 5)
			{
				$aPageNumbers[4]['iPage'] = 5;
				$aPageNumbers[4]['sRoute'] = 5;
			}	
		}
		$aPaginator['aPageNumbers'] = $aPageNumbers;
		
		
		if($this->_iTotalPages > 0 && $this->_iTotal > 0 && $this->_iPageCurrent > 0)
		{
			//$aPaginator['']
			$aPaginator['sPhraseRange'] = 'Pagina '. $this->_iPageCurrent. ' de '. $this->_iTotalPages;
		}
		
		$aPaginator['bShowBtnNext'] = false;
		if($this->_iTotal > ($this->_iPageCurrent * $this->_iQuantity))
		{
			$aPaginator['bShowBtnNext'] = true;
		}
		
		$aPaginator['bShowPageLast'] = false;
		if($this->_iTotalPages > ($this->_iPageCurrent + 2 ))
		{
			$aPaginator['bShowPageLast'] = true;
		}
		$aPaginator['iPageCurrent'] = $this->_iPageCurrent;
		$aPaginator['iTotalPages'] = $this->_iTotalPages;
		
		
		// Get Routes
		
		foreach($aPageNumbers AS $iKey => $aPage)
		{
			$aPaginator['aPageNumbers'][$iKey]['sRoute'] = $this->_oRouter->createRoute($this->_sRoute, [$this->_sRequest => $aPage['sRoute']]);
			if($aPage['iPage'] == $this->_iPageCurrent)
			{  
				$aPaginator['aPageNumbers'][$iKey]['bActive'] = true;
			}
		}
		$aPaginator['aRoutes'] = $this->_getRoutes(); 
		
		LetPHP::getClass('letphp.view')
		->setValues([
			'aPaginator' => $aPaginator
		]);
		
	}
	
	public function buildLimitAndOffset($iPage, $iSize, $iCount)
	{
		if(isset($iSize))
		{
			if(isset($iPage) && $iPage == 0)
			{ 
				
				$iPage = 1; 
			}
			$aLimit['iPage'] = ($iPage*$iSize) - $iSize;
		}
		else 
		{
			$aLimit[ 'iPage' ] = $iPage;
		}
		$aLimit[ 'iSize' ] = $iSize;
		$aLimit[ 'iCount' ] = $iCount;
		return $aLimit;
	}
	
	public static function getInstance()
	{
		return LetPHP::getClass('letphp.paginator');
	}
	
	public function _getRoutes(): array
	{
		
		$aRoutes['sRouteFirst'] = $this->_oRouter->createRoute($this->_sRoute, [$this->_sRequest =>1]);
		$aRoutes['sRoutePrev'] = $this->_oRouter->createRoute($this->_sRoute, [$this->_sRequest => $this->_iPageCurrent - 1 ]);
		$aRoutes['sRouteNext'] = $this->_oRouter->createRoute($this->_sRoute, [$this->_sRequest => $this->_iPageCurrent + 1]);
		$aRoutes['sRouteLast'] = $this->_oRouter->createRoute($this->_sRoute, [$this->_sRequest => $this->_iTotalPages]);
		
		return $aRoutes;
	}
	
}	
	
?>