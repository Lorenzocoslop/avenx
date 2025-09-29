<?php
$message = '';

if(!file_exists($corePath.'vendor/autoload.php')){
    $message = '<p class="text-danger">Execute o comando "composer install" dentro da pasta '.$corePath.' para instalar as dependências</p>';
}else{

    require $corePath.'vendor/autoload.php';

    function createPermission($name, $module){
        global $conn;
        $name = html_entity_decode($name, ENT_QUOTES, $GLOBALS['Charset']);
        $sql = "SELECT count(id) FROM permissoes WHERE modulo = '{$module}'";
        if($conn->prepareStatement($sql)->executeScalar() == 0){
            $sql = "INSERT INTO permissoes (nome, modulo, usr_cad,dt_cad,usr_ualt,dt_ualt) VALUES ('{$name}', '{$module}', 'auto', NOW(), 'auto', NOW())";
            $conn->prepareStatement($sql)->executeQuery();
        }
    }

    function getClassesDir($dirPath){
        $pasta = opendir($dirPath);
        while ($file = readdir($pasta)) {
            if (preg_match('/config\./i', $file)) {
                include $dirPath."/".$file;
                $modulo = str_replace(array('.php','config.'),'',$file);
                createPermission($Modules['nome'], $modulo);
            }
        }
    }

    if(file_exists($corePath.'install.lock')){
    $message = '<p class="alert alert-success">Instalação já realizada</p>';
    }elseif(isset($_GET['action']) && $_GET['action'] == 'execute'){
        if(count($_POST) == 0){
            $message = '<p class="alert alert-danger">Informe os dados para prosseguir com a instalação</p>';
        }else{
            //recebe as variaveis
            $env = '';
            foreach($_POST as $k => $v){
                $env .= "{$k}={$v}".PHP_EOL;
            }
            //cria o env
            file_put_contents($corePath.'.env', $env);
            
            //carrega as configs
            require $corePath.'/config.php';

            //cria as tabelas
            $conn = new Connection();
            $classes = [
                'Arquivo',
                'Banner',
                'Depoimento',
                'EmailAgendado',
                'Estatico',
                'Foto',
                'Lead',
                'Permissao',
                'PermissaoUsuario',
                'Publicacao',
                'Servico',
                'Usuario',
            ];

            foreach($classes as $classe){
                
                if(in_array('createTable', get_class_methods($classe))){
                    $sqlScript = explode(PHP_EOL,$classe::createTable());
                    $query = '';
                    foreach ($sqlScript as $line)   {
                    
                        $startWith = substr(trim($line), 0 ,2);
                        $endWith = substr(trim($line), -1 ,1);
                        
                        if (empty($line) || $startWith == '--' || $startWith == '/*' || $startWith == '//') {
                            continue;
                        }
                            
                        $query = $query . $line;
                        if ($endWith == ';') {
                            $conn->prepareStatement($query)->executeQuery();
                            $query= '';
                        }
                    }
                    
                }
            }
            
            getClassesDir(__DIR__.'/');

            $message = '<p class="alert alert-success">Instalação realizada com sucesso!</p>';
            file_put_contents($corePath.'install.lock', date('Y-m-d H:i:s'));
        }
    }else{
        $message = '
        <p class="lead mb-4">Informe os dados abaixo para prosseguir com a instalação.</p>
        <form class="d-grid gap-2 d-sm-flex justify-content-sm-center" method="post" action="?action=execute">
            <table class="table table-bordered">
            ';
            $envVars = parse_ini_file($corePath.'.env.example');
            foreach($envVars as $key => $value){
                $message .= '
                <tr>
                    <td>'.$key.'</td>
                    <td><input type="text" name="'.$key.'" class="form-control" value="'.$value.'"></td>
                </tr>';
            }
            $message .= '
                <tr>
                    <td colspan="2" class="text-center"><button type="submit" class="btn btn-primary btn-lg">Instalar</button></td>
                </tr>
            </table>
        </form>
        ';
    }
}
?>
<!doctype html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Instalação GG V4</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  </head>
  <body>
    <main>
        <div class="px-4 py-5 my-5 text-center">
            <img class="d-block mx-auto mb-4" src="<?=Utils::getDataImage(__DIR__.'/../../../img/brand.png')?>" alt="" width="150">
            <h1 class="display-5 fw-bold">Instalação GGV4</h1>
            <div class="col-lg-6 mx-auto">
            <?=$message?>
            </div>
        </div>
    </main>
    </body>
</html>
<?php

exit;