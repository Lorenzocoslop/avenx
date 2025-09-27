<?php
global $corePath,$defaultPath;
$defaultPath = __DIR__."/";
$corePath = $defaultPath.'_core/';

define("__BASEPATH__", "/");
define("__PATH__", "/");

require $corePath.'autoload.php';
require $corePath.'config.php';

//Cron Jobs
include $corePath.'crons/send-mail.php';

exit('Cron executado com sucesso.');
