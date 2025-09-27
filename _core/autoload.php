<?php

$corePath   = __DIR__.'/';
$cPath      = __DIR__.'/controller/';
$mPath      = __DIR__.'/model/';
$vPath      = __DIR__.'/view/';
$systemPath = __DIR__.'/../';

spl_autoload_register(function ($class)
{
	$class = strtolower($class).'.class.php';
	$directorys = array('basics/','dao/', 'integracoes/','',);
	foreach($directorys as $directory){
	        $file = __DIR__."/model/{$directory}{$class}";
	        if(file_exists($file)){
	        	require_once($file);
	            return;
	        }
	}
    return;
});

if(!file_exists(__DIR__.'/vendor/autoload.php') || !file_exists(__DIR__.'/.env')){
    require $cPath.'gg/module.install.php';
    exit;
}

require __DIR__.'/vendor/autoload.php';