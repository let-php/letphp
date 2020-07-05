<?php
    
namespace LetApps\App\Controllers;

class Index_Controller
{
	public function start()
	{
		$oView = View();
		
		$aItems = [
			['key' => 'letphp', 'route' => 'https://letphp.run' , 'text' => 'LetPHP'],
			['key' => 'docs', 'route' => 'https://docs.letphp.run' , 'text' => 'Documentación'],
			['key' => 'videos', 'route' => 'https://videos.letphp.run' , 'text' => 'Videos'],
			['key' => 'blog', 'route' => 'https://blog.letphp.run' , 'text' => 'Blog'],
			['key' => 'expo', 'route' =>  'https://expo.letphp.run', 'text' => 'Proyectos'],
		];
		$oView->setCss(['letphp.css' => 'app_app'])->setValues(['aItems' => $aItems]);
	}
}
    
?>
