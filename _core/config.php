<?php

global 
    $EnderecoSite, $Dominio, $DBHost, $DBUser, $DBPassWord, $DBName, $AppName, 
    $DBDriver, $Sessao, $Prefix, $EmailHost, $EmailPort, $EmailSMTPSecure,
    $EmailSMTPAuth, $EmailUsername, $EmailPassword, $EmailTipo, $Config, $Estados, $NmSN,
    $Modules, $DBCONN, $DBPREFIX, $DBPort, $xmlConfig, $objSession, $Language,
    $DBFPHost, $DBFPUser, $DBFPPassWord, $DBFPName, $Charset, $QtdRegistros, $DBCharset,
    $EmailGCID, $EmailGCSecret, $HashNonce, $corePath;

//busca os dados do .env
$_ENV = parse_ini_file($corePath.'.env');
if(count($_ENV)) foreach ($_ENV as $key => $value) putenv("$key=$value");

$DBCONN             = null;
$AppName            = getenv('APP_NAME');
$Dominio            = getenv('APP_DOMAIN');
$EnderecoSite       = "https://".$Dominio;
$Charset            = getenv('APP_CHARSET');

$DBDriver           = getenv('DB_CONNECTION');
$DBCharset          = getenv('DB_CHARSET');
$DBHost             = getenv('DB_HOST');
$DBPort             = getenv('DB_PORT');
$DBName             = getenv('DB_DATABASE');
$DBUser             = getenv('DB_USERNAME');
$DBPassWord         = getenv('DB_PASSWORD');
$Prefix             = getenv('DB_PREFIX');

$EmailTipo          = getenv('MAIL_DRIVER');
$EmailHost          = getenv('MAIL_HOST');
$EmailPort          = getenv('MAIL_PORT');
$EmailSMTPAuth      = getenv('MAIL_AUTH')=='1';
$EmailSMTPSecure    = getenv('MAIL_ENCRYPTION');
$EmailUsername      = getenv('MAIL_USERNAME');
$EmailPassword      = getenv('MAIL_PASSWORD');
$EmailGCID          = getenv('MAIL_GOOGLE_CLIENT_ID');
$EmailGCSecret      = getenv('MAIL_GOOGLE_CLIENT_SECRET');

$Language           = getenv('LANG');

$DBPREFIX           = '';

$Sessao             = md5("@FP@".__DIR__);
$Modules            = array();

$QtdRegistros = array(25, 50, 100);

$NmSN = array(
    1 => 'Sim',
    2 => 'N&atilde;o',
);

$NmSexo = array(
    1 => 'Masculino',
    2 => 'Feminino',
);

$Estados = array(
    'AC' => 'Acre',
    'AL' => 'Alagoas',
    'AP' => 'Amap&aacute;',
    'AM' => 'Amazonas',
    'BA' => 'Bahia',
    'CE' => 'Cear&aacute;',
    'DF' => 'Distrito Federal',
    'ES' => 'Esp&iacute;rito Santo',
    'GO' => 'Goi&aacute;s',
    'MA' => 'Maranh&atilde;o',
    'MT' => 'Mato Grosso',
    'MS' => 'Mato Grosso do Sul',
    'MG' => 'Minas Gerais',
    'PA' => 'Par&aacute;',
    'PB' => 'Para&iacute;ba',
    'PR' => 'Paran&aacute;',
    'PE' => 'Pernambuco',
    'PI' => 'Piau&iacute;',
    'RJ' => 'Rio de Janeiro',
    'RN' => 'Rio Grande do Norte',
    'RS' => 'Rio Grande do Sul',
    'RO' => 'Rond&ocirc;nia',
    'RR' => 'Roraima',
    'SC' => 'Santa Catarina',
    'SP' => 'S&atilde;o Paulo',
    'SE' => 'Sergipe',
    'TO' => 'Tocantins',
);

session_name( md5(__DIR__) );
$HashNonce = bin2hex(openssl_random_pseudo_bytes(32));
//$sessionExpiration = 300;

$sessParams = session_get_cookie_params();
//$sessParams['lifetime'] = $sessionExpiration; 
$sessParams['secure'] = true;
$sessParams['httponly'] = true;
$sessParams['samesite'] = 'lax';
session_set_cookie_params($sessParams);

//ini_set('session.gc_maxlifetime', $sessionExpiration);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('America/Sao_Paulo');
header('Content-type: text/html; charset='.$Charset);
header('Expect-CT: enforce, max-age=86400, report-uri="'.$EnderecoSite.'/report-ct"');

session_start();

$csp = "Content-Security-Policy: default-src 'none'; object-src 'none'; img-src 'self' https: data:; frame-ancestors 'none'; base-uri 'self'; form-action 'self'; media-src 'self'; connect-src 'self' https://maps.googleapis.com https://maps.gstatic.com; ";
$logged = isset($_SERVER['HTTP_HOST']) && isset($_SESSION[$GLOBALS['Sessao']]) && $_SESSION[$GLOBALS['Sessao']]['autorizado'];
if ($logged || (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == 'localhost')) {
    $csp .= "script-src 'self' https: 'unsafe-inline'; style-src 'self' https: 'unsafe-inline'; font-src 'self' https:";
} else {
    $csp .= "script-src 'self' 'nonce-{$HashNonce}'; style-src 'self' https://fonts.googleapis.com https://cdn.jsdelivr.net; font-src 'self' https://cdn.jsdelivr.net https://fonts.gstatic.com";
}

header($csp);
