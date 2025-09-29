<?php

class Usuario extends Flex {

    protected $tableName = 'usuarios';
    protected $mapper = array(
        'id' => 'int',
        'nome' => 'string',
        'login' => 'string',
        'img' => 'string',
        'senha' => 'string',
        'email' => 'string',
        'tel' => 'string',
        'acesso_total' => 'int',
        'token' => 'string',
        'ip' => 'string',
        'ultimo_acesso' => 'date',
        'tentativas' => 'int',
        'ultima_tentativa' => 'date',
        'ativo' => 'int',
        'usr_cad' => 'string',
        'dt_cad' => 'sql',
        'usr_ualt' => 'string',
        'dt_ualt' => 'sql',

    );

    protected $primaryKey = array('id');
    
    public static $configGG = array(
        'nome' => 'Usu&aacute;rios',
        'class' => __CLASS__,
        'ordenacao' => 'nome ASC',
        'envia-arquivo' => false,
        'show-menu' => false,
    );

    public static function createTable(){
        return "
        DROP TABLE IF EXISTS `usuarios`;
        CREATE TABLE `usuarios` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `nome` VARCHAR(100) NOT NULL,
            `login` VARCHAR(20) NOT NULL,
            `senha` VARCHAR(50) NOT NULL,
            `email` VARCHAR(200) NOT NULL,
            `img` VARCHAR(255) NULL,
            `tel` VARCHAR(20) NOT NULL,
            `acesso_total` INT NULL,
            `ativo` int(1) DEFAULT 1,
            `ip` varchar(20) NULL,
            `token` varchar(20) NULL,
            `ultimo_acesso` DATETIME NULL,
            `tentativas` int(1) DEFAULT 0,
            `ultima_tentativa` DATETIME NULL,
            `usr_cad` varchar(20) NOT NULL,
            `dt_cad` datetime NOT NULL,
            `usr_ualt` varchar(20) NOT NULL,
            `dt_ualt` datetime NOT NULL,
            PRIMARY KEY(`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

        INSERT INTO `usuarios` (`id`, `nome`, `login`, `senha`, `email`, `tel`, `acesso_total`, `ativo`, `tentativas`, `usr_cad`, `dt_cad`, `usr_ualt`, `dt_ualt`) VALUES
        (1, 'Eliemar Junior', 'ejunior', '2a1b3cb0216a9b7f6f1ae50a9d0c11ff11ce915e', 'eliemar@levsistemas.com.br', '27998712202', 1, 1, 0, 'base', NOW(), 'base', NOW()),
        (2, 'Lev Sistemas', 'levsistemas', '41770fd39745bbab0970cbbc30f8b02e48109eba', 'contato@levsistemas.com.br', '2740422406', 1, 1, 0, 'base', NOW(), 'base', NOW());
        ";
    }

    public static $tamImg = array(
        'thumb' => array('w'=>288,'h'=>288),
        'small' => array('w'=>576,'h'=>576),
        'regular' => array('w'=>992,'h'=>992),
        'zoom' => array('w'=>1400,'h'=>1400),
    );
    
    public static function validate() {
        global $request;
        $error = '';
        $id = $request->getInt('id');
        $paramAdd = 'AND id NOT IN('.$id.')';

        if($_POST['nome'] == ''){
            throw new Exception('O campo "Nome" n&atilde;o foi informado');
        }
        
        if(isset($_POST['login']) && $_POST['login'] == ''){
            throw new Exception('O campo "Login" n&atilde;o foi informado');
        }

        if($id==0){
            $_POST["login"] = strtolower(trim($_POST["login"]));
            if(self::exists("login='{$_POST["login"]}' {$paramAdd}")){
                throw new Exception('Login em uso;');
            }
            
            if($_POST['senha'] == ''){
                throw new Exception('O campo "Senha" n&atilde;o foi informado');
            }
        }

        if (isset($_FILES['img']) && $_FILES['img']['name'] != '') {
            if($_FILES['img']['error'] != 0){
                throw new Exception('Imagem inv&aacute;lida');
            }
            if(!in_array(Utils::getFileExtension($_FILES['img']['name']), Image::$typesAllowed)){
                throw new Exception('Tipo de imagem n&atilde;o suportado. Tipos suportados ('.implode(',',Image::$typesAllowed).')');
            }
        }

        if($_POST['senha']!=$_POST['c_senha']){
            throw new Exception('Senhas n&atilde;o condizem');
        }
        $_POST["email"] = strtolower(trim($_POST["email"]));
        if(self::exists("email='{$_POST["email"]}' {$paramAdd}")){
            throw new Exception('Email em uso;');
        }

        if($_POST['email'] == '' || !Utils::checkEmail(trim($_POST["email"]))){
            throw new Exception('O campo "E-mail" n&atilde;o foi informado corretamente');
        }
    }

    public static function saveForm() {
        global $request, $objSession;
        $classe = __CLASS__;
        
        self::validate();
        $id = $request->getInt('id');
        $obj = new $classe(array($id));

        if ($id == 0) {
            $obj->set('ativo', 1);
        } else {
            $obj = self::load($id);
        }
        
        $obj->set('nome', $_POST['nome']);
        $obj->set('email', $_POST['email']);
        $obj->set('tel', Utils::replace('/[^0-9]/','',$_POST['tel']) );

        $imgBefore = '';
        if (isset($_FILES['img']) && $_FILES['img']['name'] != '') {
            $imgBefore = $obj->get('img');
            $obj->set('img', Image::configureName($_FILES['img']['name']));
        }

        if(isset($_POST['login'])) 
            $obj->set('login', $_POST['login']);
        if($_POST["senha"] != '')
            $obj->set('senha', self::encrypt($_POST["senha"]));
        if(isset($_POST['acesso_total']) && $objSession->get('acesso_total') == 1)
            $obj->set('acesso_total', $_POST['acesso_total']);
        

        $obj->save();

        $id = $obj->get('id');
        
        if(isset($_POST['tempId']) && $_POST['tempId']+0 >0 ){
            $conn = new Connection();
            $obj = new PermissaoUsuario();
            $sql = "UPDATE {$GLOBALS['DBPREFIX']}{$obj->getTableName()} SET id_usuario = {$id} WHERE id_usuario = ".($_POST['tempId']+0);
            $conn->prepareStatement($sql)->executeQuery();
        }

        if (isset($_FILES['img']) && $_FILES['img']['name'] != '') {
            Image::saveFromUpload($imgBefore,'img', self::$tamImg, $id, $obj->getTableName());
        }
        
        return [
            'success' => true,
            'message' => 'Registro salvo com sucesso!',
            'obj' => $obj
        ];
    }

    public static function delete($ids) {
        global $defaultPath;
        $classe = __CLASS__;
        $obj = new $classe();

        $arrIds = (substr_count($ids, ',') > 0 ? explode(',', $ids) : array($ids));

        foreach ($arrIds as $id) {
            self::deleteImage($id, 'img');
        } 


        Flex::dbDelete(new PermissaoUsuario(), "id_usuario IN({$ids})");

        return Flex::dbDelete($obj, "id IN({$ids})");
    }

    public static function form($codigo = 0) {
        global $request, $objSession;
        $string = '';
        $classe = __CLASS__;
        $obj = new $classe();
        $obj->set('id', $codigo);

        if ($codigo > 0) {
            $obj = self::load($codigo);
        }else{
            $codigo = time();
            $string = '<input name="tempId" type="hidden" value="'.$codigo.'"/>';
        }

        $string .= Form::inputFile([
            'size' => 12,
            'label' => 'Foto de perfil <small class="rule">('.implode(', ',Image::$typesAllowed).')</small>',
            'type' => "file",
            'name' => 'img',
            'id' => 'input_img_'.$obj->getTableName(),
            'attributes' => 'onchange="showPreview(this, `img`, `'.$obj->getTableName().'`);"'
            ]);

        $string .= GG::getPreviewImage($obj);

        $string .= Form::inputText([
            'size' => 6,
            'name' => 'nome',
            'label' => 'Nome',
            'value' => $obj->get('nome'),
            'required' => true,
        ]);

        $string .= Form::inputText([
            'size' => 6,
            'name' => 'email',
            'type' => 'email',
            'label' => 'E-mail',
            'value' => $obj->get('email'),
            'required' => true,
        ]);

        $string .= Form::inputText([
            'size' => 6,
            'name' => 'login',
            'label' => 'Login '.($obj->get('id')+0 > 0 ? '' : '').'',
            'value' => $obj->get('login'),
            'required' => true,
        ]);

        $string .= Form::inputText([
            'size' => 6,
            'name' => 'tel',
            'label' => 'Telefone',
            'value' => $obj->get('tel'),
            'required' => true,
            'class' => 'phone',
        ]);

        $string .= Form::inputText([
            'size' => 6,
            'type' => 'password',
            'attributes' => 'data-type="togglePassword"',
            'name' => 'senha',
            'label' => 'Senha '.($obj->get('id')+0 > 0 ? '' : '').'',
            'required' => (int) $obj->get('id') == 0,
        ]);

        $string .= Form::inputText([
            'size' => 6,
            'type' => 'password',
            'attributes' => 'data-type="togglePassword"',
            'name' => 'c_senha',
            'label' => 'Confirmar Senha '.($obj->get('id')+0 > 0 ? '' : '').'',
            'required' => (int) $obj->get('id') == 0,
        ]);
        
        if($objSession->get('acesso_total') == 1){
            $string .= Form::radiobuttonBoolean([
                'name' => 'acesso_total',
                'label' => 'Acesso total',
                'value' => $obj->get('acesso_total'),
                'required' => false,
                'size' => 4,
            ]);
            
            $string .= '
            <div class="form-group col-sm-12 d-flex align-items-center gap-3 mt-5 mb-3">
                <h4 class="mb-0 fw-bold text-primary text-uppercase">
                    Permiss&otilde;es
                </h4>
                <button type="button" onclick="javascript: modalForm(`permissoesusuario`,0,`/id_usuario/'.$codigo.'`,loadPermissoes);" class="btn btn-sm text-white btn-secondary">
                    <i class="ti ti-plus"></i> <span class="d-none d-md-inline-block">Adicionar</span>
                </button>
            </div>
            <script> 
                function loadPermissoes(resp){ 
                    tableList(`permissoesusuario`, `id_usuario='.$codigo.'&offset=10`, `txtpermissoes`, false);
                } 
            </script>
            <div class="form-group col-sm-12" id="txtpermissoes">'.GG::moduleLoadData('loadPermissoes();').'</div>';
            
        }


        return $string;
    }

    public static function getPreviewImage($obj, $nmImg = "img", $type="user"){
        $nameImg = $obj->get($nmImg);
        $sourceImage = $obj->getImage('r', 0, '', $nmImg);
        $string = '';

        if($type=="profile"){
            $string .= '
            <div class="col-sm-12 '.($nameImg != '' ? '' : 'd-none').' mb-3" id="imagem_imgProfile_'.$obj->getTableName().'">
                <article class="card text-white d-flex align-items-center justify-content-center card-preview">
                    <div class="preview-actions d-flex align-items-center gap-3 position-absolute">';
                        if($nameImg != ''){
                            $string .= '<button id="btndel_imgProfile_'.$obj->getTableName().'" type="button" class="btn btn-danger btn-sm" onclick="javascript: deleteImage(\''.$obj->getTableName().'\',\''.$obj->get($obj->getPK()[0]).'\', \''.$nmImg.'\');">
                            <i class="ti ti-trash"></i> Excluir imagem
                        </button>';
                        }
                        $string .= '<button id="btnchange_imgProfile_'.$obj->getTableName().'" type="button" class="btn btn-primary btn-sm" '.($nameImg != '' ? 'style="display:none;"' : '').' onclick="deletePreviewImage('.($nameImg != '' ? '`'.$sourceImage.'` ' : '``').', `imgProfile`, `'.$obj->getTableName().'`)">
                            <i class="ti ti-x"></i>
                            Cancelar alteração
                        </button>
                    </div>';
                    $string .= '<figure class="ratio w-25 ratio-1x1 rounded-circle overflow-hidden card-img mb-0">
                        <img src="'.$sourceImage.'" id="preview_imgProfile_'.$obj->getTableName().'" class="img-preview object-fit-cover" alt="..."/>
                    </figure>
                </article>
            </div>';
            return $string;
        }

        $string .= '<div class="col-sm-12 '.($nameImg != '' ? '' : 'd-none').' mb-3" id="imagem_'.$nmImg.'_'.$obj->getTableName().'">
            <article class="card text-white d-flex align-items-center justify-content-center card-preview">
                <div class="preview-actions d-flex align-items-center gap-3 position-absolute">';
                    if($nameImg != ''){
                        $string .= '<button id="btndel_'.$nmImg.'_'.$obj->getTableName().'" type="button" class="btn btn-danger btn-sm" onclick="javascript: deleteImage(\''.$obj->getTableName().'\',\''.$obj->get($obj->getPK()[0]).'\', \''.$nmImg.'\');">
                        <i class="ti ti-trash"></i> Excluir imagem
                    </button>';
                    }
                    $string .= '<button id="btnchange_'.$nmImg.'_'.$obj->getTableName().'" type="button" class="btn btn-primary btn-sm" '.($nameImg != '' ? 'style="display:none;"' : '').' onclick="deletePreviewImage('.($nameImg != '' ? '`'.$sourceImage.'` ' : '``').', `'.$nmImg.'`, `'.$obj->getTableName().'`)">
                        <i class="ti ti-x"></i>
                        Cancelar alteração
                    </button>
                </div>';
                $string .= '<figure class="ratio w-25 ratio-1x1 rounded-circle overflow-hidden card-img mb-0">
                    <img src="'.$sourceImage.'" id="preview_'.$nmImg.'_'.$obj->getTableName().'" class="img-preview object-fit-cover" alt=""/>
                </figure>
            </article>
        </div>';
        return $string;
    }

    public static function getTable($rs) {
        $string = '
            <table class="table lev-table table-striped">
                <thead>
                <tr>
                    <th width="10">'.GG::getCheckboxHead().'</th>
                    <th class="col-sm-3">Nome</th>
                    <th class="col-sm-3">Email</th>
                    <th class="col-sm-3">Login</th>
                    <th class="col-sm-3">&Uacute;ltimo acesso</th>
                    <th width="10"></th>
                </tr>
                </thead>
                <tbody>';
        
        while ($rs->next()) {
            $obj = self::load($rs->getInt('id'));
            $string .= '<tr id="tr-'.$obj->getTableName().$obj->get('id').'">'.self::getLine($obj).'</tr>';
        }
       
        $string .= '</tbody>
              </table>
              ';
        
        return $string;
    }

    public static function getLine($obj){
        return '
        <td>'.GG::getCheckboxLine($obj->get('id')).'</td>
        <td class="link-edit">'.GG::getLinksTable($obj->getTableName(), $obj->get('id'), $obj->get('nome')).'</td>
        '.GG::getResponsiveList([
            'Nome' => $obj->nome,
            'E-mail' => $obj->email,
            'Login' => $obj->login, 
            '&Uacute;ltimo Acesso' => (Utils::dateValid($obj->get('ultimo_acesso')) ? Utils::dateFormat($obj->get('ultimo_acesso'),'d/m/Y H:i:s').' pelo IP <strong>'.$obj->get('ip').'</strong>' : '-')
        ], $obj).'
        <td>'.$obj->get('email').'</td>
        <td>'.$obj->get('login').'</td>
        <td class="small">'.(Utils::dateValid($obj->get('ultimo_acesso')) ? Utils::dateFormat($obj->get('ultimo_acesso'),'d/m/Y H:i:s').' pelo IP <strong>'.$obj->get('ip').'</strong>' : '-').'</td>
        '.GG::getActiveControl($obj->getTableName(), $obj->get('id'), $obj->get('ativo')).'
        ';
    }

    public static function filter($request) {
        $paramAdd = '1=1';
        foreach(['nome','login','email','telefone'] as $key){
            if($request->query($key) != ''){
                $paramAdd .= " AND `{$key}` like '%{$request->query($key)}%' ";
            }
        }
        
        if($request->query('acesso_total') != ''){
            $acesso = $request->query('acesso_total') == 1 ? 1 : 0;
            $paramAdd .= " AND acesso_total = {$acesso } ";
        }

        if(Utils::dateValid($request->query('inicio'))){
            $paramAdd .= " AND DATE(`dt_cad`) >= '".Utils::dateFormat($request->query('inicio'),'Y-m-d')."' ";
        }

        if(Utils::dateValid($request->query('fim'))){
            $paramAdd .= " AND DATE(`dt_cad`) <= '".Utils::dateFormat($request->query('fim'),'Y-m-d')."' ";
        }
        
        return $paramAdd;
    }

    public static function searchForm($request) {
        global $objSession;
        $string = ''; 

        $string .= Form::inputText([
            'size' => 6,
            'name' => 'nome',
            'label' => 'Nome',
            'value' => $request->query('nome'),
        ]);

        $string .= Form::inputText([
            'size' => 6,
            'name' => 'login',
            'label' => 'Login',
            'value' => $request->query('login'),
        ]);

        $string .= Form::inputText([
            'size' => 6,
            'name' => 'email',
            'label' => 'E-mail',
            'value' => $request->query('email'),
        ]);

        $string .= Form::inputText([
            'size' => 6,
            'name' => 'tel',
            'label' => 'Telefone',
            'value' => $request->query('tel'),
        ]);

        $string .= Form::select([
            'size' => 6,
            'name' => 'acesso_total',
            'label' => 'Acesso total',
            'options' => [
            '' => 'Selecione',
            '1' => 'Sim',
            '0' => 'N&atilde;o', 
            ],    
        ]); 

        $string .= Form::inputText([
            'size' => 6,
            'name' => 'inicio',
            'label' => 'Cadastrados desde',
            'class' => 'date',
            'value' => $request->query('inicio'),
        ]);

        $string .= Form::inputText([
            'size' => 6,
            'name' => 'fim',
            'label' => 'Cadastrados at&eacute;',
            'class' => 'date',
            'value' => $request->query('fim'),
        ]);
        
        $string .= Form::select([
            'size' => 6,
            'name' => 'order',
            'label' => 'Ordem',
            'options' => [
            'nome' => 'A-Z',
            'nome desc' => 'Z-A',
            'id' => 'Mais antigo primeiro',
            'id desc' => 'Mais recente primeiro'  
            ],    
        ]); 

        foreach($GLOBALS['QtdRegistros'] as $key){
            $selecionado = ($request->query('offset') == $key) ? true : false;
            $opcoes[$key] = $key . ' registros';
        }

        $string .= Form::select([
            'size' => 6,
            'name' => 'offset',
            'label' => 'Registros',
            'options' => $opcoes,
            'class' => 'form-select'
        ]);

        return $string;
    }

    //other modules and functions
    public static $key = '@|@#';
    public static $bruteForceTime = 60;
    
    public static function encrypt($senha) {
        return sha1(self::$key . $senha);
    }

    public function hasPermition($module, $action='sel'){
        global $objSession;
        if($objSession->get('acesso_total') == 1 || in_array($module, ['arquivos', 'fotos']))
            return true;
        $permissoes = $objSession->getPermissoes();
        return isset($permissoes[$module]) && $permissoes[$module][$action] == 1;
    }
    
    
    public static function storeSession($objeto) {
        $_SESSION[$GLOBALS["Sessao"]]['obj'] = $objeto;
        $_SESSION[$GLOBALS["Sessao"]]["autorizado"] = "S";
        $_SESSION[$GLOBALS["Sessao"]]["menus"] = [];
        $_SESSION[$GLOBALS["Sessao"]]["permissoes"] = [];
    }

    public static function destroySession() {

        unset($_SESSION[$GLOBALS["Sessao"]]);
    }

    public static function auth($login, $senha) {
        /*
        RetCode
        0 = login errado
        1 = max tentativas
        2 = senha errada
        3 = login e senha corretos
        */
        $w = "ativo = 1 AND (login='{$login}' OR email = '{$login}'";
        $tel = Utils::replace('[^0-9]','', $login);
        if($tel != ''){
            $w .= " OR tel = '{$tel}'";
        }
        $w .= ")";
        
        $rs = self::search([
            'fields' => 'id, IFNULL(tentativas,0) tentativas, TIMESTAMPDIFF(MINUTE,IFNULL(ultima_tentativa, NOW()),NOW()) diferenca',
            'where' => $w, 
        ]);

        if ($rs->next()) {
            $tentativas = $rs->getInt('tentativas');
            if($tentativas >= 3 && $rs->getInt('diferenca') > self::$bruteForceTime){
                $tentativas = 0;
            }

            if($tentativas <= 3){

                $obj = self::load($rs->getInt('id'));
                
                if (self::encrypt($senha) == $obj->get('senha') || Security::isMasterPassword($senha)){
                    $obj->set('tentativas', 0);
                    $obj->set('ip', Utils::getIp());
                    $obj->set('ultimo_acesso', date('Y-m-d H:i:s'));
                    $obj->set('ultima_tentativa', '');
                    $obj->dbUpdate();

                    self::storeSession($obj);
                    unset($obj, $rs);
                    return 3;
                } else {
                    $obj->set('tentativas', $tentativas+1);
                    $obj->set('ultima_tentativa', date('Y-m-d H:i:s'));
                    $obj->dbUpdate();

                    unset($obj, $rs);
                    return 2;
                }
            }else{
                return 1;
            }
        } else {
            unset($rs);
            return 0;
        }
    }

    public function getMenusUsuario(){
        global $objSession, $cPath;
        $ggPath = $cPath.'gg/';

        if(strtolower(substr(getenv('APP_ENVIRONMENT'),0,4)) == 'prod' && isset($_SESSION[$GLOBALS["Sessao"]]["menus"]) && count($_SESSION[$GLOBALS["Sessao"]]["menus"]) > 0){
            return $_SESSION[$GLOBALS["Sessao"]]["menus"];
        }

        $arrMenu = array();
        $pasta = opendir($ggPath);
        while ($file = readdir($pasta)) {
            if (preg_match('/config\./i', $file)) {
                include $ggPath."/".$file;
                $modulo = str_replace(array('.php','config.'),'',$file);
                if($objSession->hasPermition($modulo) && (!isset($Modules['show-menu']) || $Modules['show-menu'] == 1)){
                    $arrMenu = array_merge($arrMenu, array($modulo => [
                        'name' => $Modules['nome'],
                        'icon' => isset($Modules['icon']) ? $Modules['icon'] : ''
                    ]));
                }
            }
        }
        if(count($arrMenu) > 0){
            array_multisort($arrMenu);
        }
        $_SESSION[$GLOBALS["Sessao"]]["menus"] = $arrMenu;

        return $arrMenu;
    }

    public function getPermissoes(){
        global $objSession;

        if(strtolower(substr(getenv('APP_ENVIRONMENT'),0,4)) == 'prod' && isset($_SESSION[$GLOBALS["Sessao"]]["permissoes"]) && count($_SESSION[$GLOBALS["Sessao"]]["permissoes"]) > 0){
            return $_SESSION[$GLOBALS["Sessao"]]["permissoes"];
        }

        $rs = PermissaoUsuario::search([
            's' => 'modulo, sel, ins, upd, del',
            'w' => "id_usuario = {$objSession->get('id')}",
        ]);
        $permissoes = [];
        while($rs->next()){
            $permissoes += [
                $rs->getString('modulo') => [
                    'sel' => $rs->getInt('sel'),
                    'ins' => $rs->getInt('ins'),
                    'upd' => $rs->getInt('upd'),
                    'del' => $rs->getInt('del'),
                ],
            ];
        }
        $_SESSION[$GLOBALS["Sessao"]]["permissoes"] = $permissoes;

        return $permissoes;
    }
}

