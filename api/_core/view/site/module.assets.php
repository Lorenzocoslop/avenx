<?php
header('Content-Type: application/javascript');

$action = str_replace('.js', '', $request->get('action'));

echo '
const __PATH__ = `'.__PATH__.'`;
const __BASEPATH__ = `'.__BASEPATH__.'`;
';

$file = __DIR__.'/assets/'.$action.'.php';
if(file_exists($file)){
	include $file;
}

exit;