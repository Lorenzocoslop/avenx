<?php

global $defaultPath, $predefinedPath;
if(!$predefinedPath){
    $predefinedPath = array(
        'gg'=> array('folder'=>'gg/', 'path' => 'gg/'),
        ''=> array('folder'=>'site/', 'path' => ''),
    );
}

require __DIR__.'/autoload.php';
require __DIR__.'/config.php';

$view['module'] = "404.php";
$view['page_class'] = 'inner';

$whoops = new \Whoops\Run();
if (strtolower(substr(getenv('APP_ENVIRONMENT'),0,4))!='prod') {
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
} else {
    $whoops->pushHandler(function($e){
        echo $e;
    });
}
$whoops->register();

$serverPath = str_replace('index.php','',$_SERVER['SCRIPT_NAME']);
$url = Utils::replace("#\?.+#i",'',Utils::replace("#^($serverPath)#i",'',$_SERVER['REQUEST_URI']));
$_SERVER['REQUEST_URI'] = htmlspecialchars(str_replace("'",'',$_SERVER['REQUEST_URI']));
$pUrl = explode('/',$url);
if(array_key_exists($pUrl[0], $predefinedPath)){
    define("__PATH__", $serverPath.$predefinedPath[$pUrl[0]]['path']);
    $folderPath = $predefinedPath[$pUrl[0]]['folder'];
    $pagePath = $predefinedPath[$pUrl[0]]['path'];
}else{
    define("__PATH__", $serverPath);
    $folderPath = $predefinedPath['']['folder'];
    $pagePath = $predefinedPath['']['path'];
}

define("__BASEPATH__", $serverPath);
$param = $pagePath != '' ? Utils::replace("#^{$pagePath}?#i",'', $url) : $url;

$request = new Request($param);
$conn = new Connection();
$Config = new Config();

require $cPath.$folderPath.'core.php';
require $vPath.$folderPath.'index.php';

$conn->closeConnection();