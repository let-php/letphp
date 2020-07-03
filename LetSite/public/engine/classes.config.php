<?php

$aClasses = [
	"classes" => [
		"api" => \LetPHP_Api::class,
		"app" => \LetPHP_App::class,
		"auth" => \LetPHP_Auth::class,
		"auth.handler.session" => \LetPHP_Auth_Handler_Session::class,
		"cache" => \LetPHP_Cache::class,
		"config" => \LetPHP_Config::class,
		"database" => \LetPHP_Database::class,
		"database.handler.mysqli" => \LetPHP_Database_Handler_MySQLi::class,
		"filter.input" => \LetPHP_Filter_Input::class,
		"filter.output" => \LetPHP_Filter_Output::class,
		"http" => \LetPHP_Http::class,
		"javascript" => \LetPHP_Javascript::class,
		"paginator" => \LetPHP_Paginator::class,
		"router" => \LetPHP_Router::class,		
		"utils" => \LetPHP_Utils::class,
		"view" =>  \LetPHP_View::class,
		"view.bessie" => \LetPHP_View_Bessie::class
	]
];

return $aClasses;
	
?>