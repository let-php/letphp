
<div class="paginator" >
	<div class="paginator--results">
		<span class="paginator--results-text">
			{$aPaginator.iTotalResults} Resultados
		</span>
	</div>
	<div class="paginator--indice" >
		{$aPaginator.iPageCurrent}
		/
		{$aPaginator.iTotalPages}
	</div>
	{if $aPaginator.iTotalPages > 1}
	<div class="paginator--items">
		{if $aPaginator.iPageCurrent > 1}
			<a href="{$aPaginator.aRoutes.sRoutePrev}" aria-label="Previo" >&#5130;</a>
		{/if}
		{if $aPaginator.bShowBtnNext}				
		<a href="{$aPaginator.aRoutes.sRouteNext}" aria-label="Siguiente" >&#5125;</a>
		{/if}
	</div>
	{/if}
</div>

 
 {* 
<div class="row  py-2">
	<div class="col align-self-center text-left">
		<span class="text-muted font-weight-light h5 px-2">{$aPaginator.iTotalResults} Resultados encontrados.</span>
	</div>
	
	{if $aPaginator.iTotalPages > 1}
	<div class="col">
		<nav>
			
			<ul class="pagination" >
				{if $aPaginator.iPageCurrent > 3}
				<li class="page-item">
					<a class="page-link bg-dark" href="{$aPaginator.aRoutes.sRouteFirst}" aria-label="Previous">
						<i class="fa fa-angle-double-left text-white font-weight-bold"></i>
					</a>
				</li>
				{/if}
				{if $aPaginator.iPageCurrent > 1}
				<li class="page-item">
					<a class="page-link" href="{$aPaginator.aRoutes.sRoutePrev}" aria-label="Previous">
						<i class="fa fa-angle-left"></i>
					</a>
				</li>
				{/if}
				
				{each values=$aPaginator.aPageNumbers value=aPages}
					<li class="page-item" >
						<a class="page-link {if $aPages.bActive}bg-dark text-white{/if}" href="{$aPages.sRoute}">
							{$aPages.iPage}
						</a>
					</li>	
				{/each}
				
				
				{if $aPaginator.bShowBtnNext}				
			    <li class="page-item">
			      <a class="page-link" href="{$aPaginator.aRoutes.sRouteNext}" aria-label="Next">
			        <i class="fa fa-angle-right"></i>
			      </a>
			    </li>
				{/if}
				
				{if $aPaginator.bShowPageLast}				
			  
		    <li class="page-item">
		      <a class="page-link bg-dark" href="{$aPaginator.aRoutes.sRouteLast}" aria-label="Next">
		        <i class="fa fa-angle-double-right text-white font-weight-bold"></i>
		      </a>
		    </li>
		    {/if}
			</ul>
		</nav>
	</div>
	{/if}
	
	
	<div class="col align-self-center text-right">
		<span class="text-dark h5" >{$aPaginator.iPageCurrent}</span>
		<span class="text-dark h4"  >/</span>
		<span class="text-dark h3">{$aPaginator.iTotalPages}</span>

	</div>
	
	
</div>

*}