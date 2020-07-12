<?php

error_reporting(E_ALL);

$memory = @ini_get('memory_limit');
$subString = substr($memory, -1);
$iString = (int) $memory;
switch ($subString) {
	case 'K':
		$iString = $iString/1000;
		break;
	case 'G':
		$iString = $iString*1000;
		break;
	default:
		# code...
		break;
}

if ($iString >= 64) {
	$bMemory = true;
} else {
	$bMemory = false;
}

$sDir = str_replace(LETPHP_DIR_PARENT, '', LETPHP_LETCORE_DIRS_CACHE. 'views'. LETPHP_DS);
$sCacheText = 'El archivo <b>"'.$sDir.'"</b> debe tener permisos de escritura.';

$aRequirements = [
	'PHP Version' => [version_compare(PHP_VERSION, '7.1', '>='), 'Le versión de PHP que tienes es ' . PHP_VERSION . '. LetPHP requiere la versión 7.1 o una versión mayor.'],
	//'PHP EXEC Function' => [function_exists('exec'), 'Habilita la función PHP "exec"'],
	//'PHP GD' => [(extension_loaded('gd') && function_exists('gd_info')), 'Missing PHP library GD'],
	//'PHP ZipArchive' => [(class_exists('ZipArchive')), 'Missing PHP ZipArchive'],
	'PHP CURL' => [(extension_loaded('curl') && function_exists('curl_init')), 'Debes de habilitar la Libreria CURL de PHP'],
	'PHP Multibyte String' => [function_exists('mb_strlen'), 'Debes habilitar la libreria Multibyte String de PHP'],
	//'PHP XML extension' => [extension_loaded('xml'), 'Missing PHP library XML'],
	'PHP memory_limit' => [($memory == '-1' ? true : $bMemory), 'El límite de memoria de su servidor es ' . $memory . '. LetPHP requiere 64MB o más.'],
	'Cache escritura' => [(is_writable(LETPHP_LETCORE_DIRS_CACHE. 'views'. LETPHP_DS)), $sCacheText]
			];
			
?>
<html>
	<head>
		<title>LetPHP » Requerimientos para LetPHP</title>
		<link rel="icon" href="./letphp.ico" /> 
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css" integrity="sha384-r4NyP46KrjDleawBgD5tp8Y7UzmLA05oM1iAEQ17CSuDqnUK2+k9luXQOfXJCJ4I" crossorigin="anonymous">
		<style>
			:root{
				--bg: #242121;
				--text-success: #8DBF8B;
				--text-danger: #E44652;
				--text-message: #FAF8F0;
				--text-title: #FF5976;
			}
			body{ background: var(--bg); }
			.let-navbar{
				background: var(--bg) !important;
				box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
			}
			.let-title{ color: var(--text-title); }
			.let-text-success{ color: var(--text-success); }
			.let-text-danger{ color: var(--text-danger); }
			.let-text-message{ color: var(--text-message); }
			.let-btn
			{ 
				background: var(--text-title) ; 
				color: var(--text-message);
				border:2px solid rgba(248, 77, 158, 1);
				box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
			}
			.card
			{ 
				background: var(--bg) !important;
				box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
			}
			
		</style>
	</head>
	<body>
	<nav class="navbar navbar-light fixed-top bg-light let-navbar">
		<div class="container">
			<a class="navbar-brand" href="https://letphp.run">
				<img src="./letphp.ico" width="30" height="30" class="d-inline-block align-top" alt="LetPHP" />
				<span class="font-weight-bold text-uppercase h3 ml-3 let-title" >LetPHP Framework</span>
			</a>
		</div>
	</nav>
	
	<div class="container" >
		<div class="row  justify-content-center align-self-center" style="height: 100vh">
			<div class="col-12 col-md-7 align-self-center">
				<article class="card border-0">
					<div class="card-body">

						<h5 class="text-uppercase font-weight-bold py-3 let-title">Requerimientos necesarios</h5>
						<table class="table table-borderless">
							<?php foreach ($aRequirements as $name => $values) 
								{
									$message = '<p class="let-text-danger h6 font-weight-bold">No Aprobado</p><p>' . $values[1] . '</p>';
									$class = 'danger';
									if ($values[0]) {
										$message = '<p class="let-text-success h6 font-weight-bolder">Aprobado</p>';
										$class = '';
									}
									echo '<tr class="' . $class . '">';
									echo '<td> <span class="font-weight-bold h6 let-text-message" > ' . $name . ' </span> </td><td><span class="h6 let-text-message" >' . $message . '</span></td>';
									echo '</tr>';
								}
							?>
							</table>
							
							
					</div>
					
					<div class="card-footer text-right">
						
							<a href="" class="btn let-btn border-0 rounded-0 text-uppercase" >Recargar</a>
					</div>
				</article>
				
			</div>
		</div>
	</div>
	</body>
</html>
