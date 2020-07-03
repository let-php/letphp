<?php
/**
 * @author			Rodrigo Ortiz
 * @package 		Letphp
 * @version 		$Id: start.view.php 2020-04-10 Rodrigo Hernández Ortiz 
 */
defined('LETPHP') or exit('NO EXISTE!'); 

?>
<!DOCTYPE html>
<html lang="es">  
<head>
  <meta charset="UTF-8">
  {meta}
  <title>{site_title}</title>
  <link rel="icon" href="./letphp.ico" /> 
  {css}
</head>
<body>
	<nav >
		<a href="{$sRoutePath}">
			<img src="{$sLogo}" class="nav--logo" />
		</a>
	</nav>
  {content}   
	{js}
</body>
</html>
