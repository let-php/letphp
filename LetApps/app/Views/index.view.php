<div class="let--container">
	<div class="let--logo">
		<img src="./letphp.ico"/>
	</div>
	<div class="let--welcome">
		<h1>Gracias por ser parte de <span class="let--site-name" >{site_name}</span></h1>
	</div>
	
	<div class="let--menu">
		<ul>
			{each values=$aItems value=aItem}			
				<li class="let--menu-item" >
					<a href="{$aItem.route}" target="_blank" >{$aItem.text}</a>
				</li>
			{/each}
		</ul>
	</div>
</div>