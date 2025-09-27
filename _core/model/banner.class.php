<?php

use Dotenv\Loader\Value;

class Banner extends Flex {

    protected $tableName = 'banners';
    protected $mapper = array(
        'id' => 'int',
        'nome' => 'string',
        'descricao' => 'string',
        'url' => 'string',
        'botao' => 'string',
        'janela' => 'string',
        'img' => 'string',
        'imgmob' => 'string',
        'expira' => 'int',
        'inicio' => 'date',
        'fim' => 'date',
        'ordem' => 'int',
        'ativo' => 'int',
        'usr_cad' => 'string',
        'dt_cad' => 'sql',
        'usr_ualt' => 'string',
        'dt_ualt' => 'sql',
    );

    protected $primaryKey = array('id');
    

    public static $configGG = array(
        'nome' => 'Banners',
        'class' => __CLASS__,
        'ordenacao' => 'nome ASC',
        'envia-arquivo' => true,
        'icon' => "ti ti-picture-in-picture"
    );

    public static function createTable(){
        return '
        DROP TABLE IF EXISTS `banners`;
        CREATE TABLE `banners` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `nome` VARCHAR(100) NOT NULL,
            `url` VARCHAR(255) NULL,
            `botao` VARCHAR(255) NULL,
            `janela` VARCHAR(10) NULL,
            `img` VARCHAR(255) NULL,
            `imgmob` VARCHAR(255) NULL,
            `descricao` TEXT NULL,
            `expira` INT NULL,
            `inicio` DATETIME NULL,
            `fim` DATETIME NULL,
            `ativo` int(1) DEFAULT 1,
            `ordem` int(2) DEFAULT 1,
            `usr_cad` varchar(20) NOT NULL,
            `dt_cad` datetime NOT NULL,
            `usr_ualt` varchar(20) NOT NULL,
            `dt_ualt` datetime NOT NULL,
            PRIMARY KEY(`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;';
    }
    
    public static $tamImg = array(
        'thumb' => array('w' => 95, 'h' => 39),
        'small' => array('w' => 140, 'h' => 100),
        'regular' => array('w' => 1920, 'h' => 800),
    );
    
    public static $tamImgmob = array(
        'regular' => array('w' => 425, 'h' => 756),
    );
    
    public static function validate() {
        if($_POST['nome'] == ''){
            throw new Exception('O campo "Nome" n&atilde;o foi informado');
        }
        
        if (isset($_FILES['img']) && $_FILES['img']['name'] != '') {
            if($_FILES['img']['error'] != 0){
                throw new Exception('Imagem inv&aacute;lida');
            }
            if(!in_array(Utils::getFileExtension($_FILES['img']['name']), Image::$typesAllowed)){
                throw new Exception('Tipo de imagem n&atilde;o suportado. Tipos suportados ('.implode(',',Image::$typesAllowed).')');
            }
        }
        
        if (isset($_FILES['imgmob']) && $_FILES['imgmob']['name'] != '') {
            if($_FILES['imgmob']['error'] != 0){
                throw new Exception('Imagem inv&aacute;lida');
            }
            if(!in_array(Utils::getFileExtension($_FILES['imgmob']['name']), Image::$typesAllowed)){
                throw new Exception('Tipo de imagem n&atilde;o suportado. Tipos suportados ('.implode(',',Image::$typesAllowed).')');
            }
        }

        if($_POST['expira']==1){
            if(!Utils::dateValid($_POST["inicio"].':00')){
                throw new Exception('Data/hora de in&iacute;cio inv&aacute;lida');
            }
            if(!Utils::dateValid($_POST["fim"].':00')){
                throw new Exception('Data/hora de fim inv&aacute;lida');
            }
        }
    }

    public static function saveForm() {
        global $request;
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
        $obj->set('url', $_POST['url']);
        $obj->set('botao', $_POST['botao']);
        $obj->set('janela', $_POST['janela']);
        $obj->set('descricao', $_POST['descricao']);
        $obj->set('expira', $_POST['expira']+0);
        $obj->set('inicio', ($obj->get('expira') == 1 && Utils::dateValid($_POST['inicio'].':00') ? Utils::dateFormat($_POST['inicio'].':00','Y-m-d H:i:s') : ''));
        $obj->set('fim', ($obj->get('expira') == 1 && Utils::dateValid($_POST['fim'].':00') ? Utils::dateFormat($_POST['fim'].':00','Y-m-d H:i:s') : ''));
        $imgBefore = '';
        if (isset($_FILES['img']) && $_FILES['img']['name'] != '') {
            $imgBefore = $obj->get('img');
            $obj->set('img', Image::configureName('img_'.$_FILES['img']['name']));
        }
        
        $imgmobBefore = '';
        if (isset($_FILES['imgmob']) && $_FILES['imgmob']['name'] != '') {
            $imgmobBefore = $obj->get('imgmob');
            $obj->set('imgmob', Image::configureName($_FILES['imgmob']['name'], 'imgmob'));
            $obj->set('imgmob', Image::configureName($_FILES['imgmob']['name'], 'imgmob'));
        }
        
        $obj->save();

        $id = $obj->get('id');
        
        if (isset($_FILES['img']) && $_FILES['img']['name'] != '') {
            Image::saveFromUpload($imgBefore,'img', self::$tamImg, $id, $obj->getTableName());
        }
        
        if (isset($_FILES['imgmob']) && $_FILES['imgmob']['name'] != '') {
            Image::saveFromUpload($imgmobBefore,'imgmob', self::$tamImgmob, $id, $obj->getTableName());
        }
        
        return [
            'success' => true,
            'message' => 'Registro salvo com sucesso!',
            'obj' => $obj
        ];
    }

    public static function delete($ids) {
        $classe = __CLASS__;
        $obj = new $classe();

        $arrIds = (substr_count($ids, ',') > 0 ? explode(',', $ids) : array($ids));

        foreach ($arrIds as $id) { 
            self::deleteImage($id, 'img');
            self::deleteImage($id, 'imgmob');
        } 

        return Flex::dbDelete($obj, "id IN({$ids})");
    }

    public static function form($codigo = 0) {
        global $request;
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
        
        $string .= Form::inputText([
            'size' => 6,
            'name' => 'nome',
            'label' => 'Nome',
            'value' => $obj->get('nome'),
            'required' => true,
        ]);

        $string .= Form::inputText([
            'size' => 6,
            'name' => 'url',
            'label' => 'Link',
            'value' => $obj->get('url'),
            'required' => false,
        ]);

        $string .= Form::inputText([
            'size' => 6,
            'name' => 'botao',
            'label' => 'Texto Bot&atilde;o',
            'value' => $obj->get('botao'),
            'required' => false,
        ]);

        $string .= Form::select([
            'size' => 6,
            'name' => 'janela',
            'label' => 'Abertura do link',
            'options' => [
                '_self' => 'Mesma janela',
                '_blank' => 'Nova janela',
            ],
        ]);
        
        $string .= Form::radiobuttonBoolean([
            'size' => 12,
            'name' => 'expira',
            'label' => 'Expira?',
            'value' => $obj->get('expira'),
            'attributes' => 'onclick="if($(`[name=expira]:checked`).val() == 1){ $(`.expiration`).fadeIn(); }else{ $(`.expiration`).fadeOut();}"'
        ]);

        $string .= '
        <div class="expiration col-sm-12" style="display:'.($obj->get('expira')!=1?'none':'block').'">
            <div class="row flex-wrap">';

            $string .= Form::inputText([
                'size' => 6,
                'name' => 'inicio',
                'label' => 'In&iacute;cio',
                'class' => 'date',
                'value' => Utils::dateValid($obj->get('inicio')) ? Utils::dateFormat($obj->get('inicio'),'d/m/Y H:i') : '',
               
            ]);    
           
            $string .= Form::inputText([
                'size' => 6,
                'name' => 'fim',
                'label' => 'Fim',
                'class' => 'date',
                'value' => Utils::dateValid($obj->get('fim')) ? Utils::dateFormat($obj->get('fim'),'d/m/Y H:i') : '',
            ]); 
            

        $string .= '
            </div>
        </div>';

        $string .= Form::inputFile([
            'size' => 12,
            'label' => 'Imagem PC <small class="rule">('.implode(', ',Image::$typesAllowed).')</small>',     
            'type' => "file",
            'name' => 'img',
            'id' => 'input_img_'.$obj->getTableName(),
            'attributes' => 'onchange="showPreview(this, `img`, `'.$obj->getTableName().'`);"'
        ]);
        
        $string .= GG::getPreviewImage($obj);

        $string .= Form::inputFile([
            'size' => 12,
            'label' => 'Imagem Mobile<small class="rule">('.implode(', ',Image::$typesAllowed).')</small>',
            'type' => "file",
            'name' => 'imgmob',
            'id' => 'input_imgmob_'.$obj->getTableName(),
            'attributes' => 'onchange="showPreview(this, `imgmob`, `'.$obj->getTableName().'`);"'
        ]);

        $string .= GG::getPreviewImage($obj, "imgmob");

        $string .= Form::ckeditor([
            'size' => 12,
            'name' => 'descricao',
            'label' => 'Descri&ccedil;&atilde;o',
            'value' => $obj->get('descricao'),
        ]);

        return $string;
    }

    public static function getTable($rs) {
        $string = '
            <table class="table lev-table table-striped">
            <thead>
              <tr>
                <th colspan="2" class="col-2 col-md-1 responsive-item">'.GG::getCheckboxHead().'</th>
                <th class="col-9 col-sm-12">Nome</th>
                <th class="col-1 responsive-item"></th>
              </tr>
            </thead>
            '.GG::getOrderTbody(__CLASS__);
        
        while ($rs->next()) {
            $obj = self::load($rs->getInt('id'));
            $string .= '<tr id="tr-'.$obj->getTableName().$obj->get('id').'">'.self::getLine($obj).'</tr>';
        }
       
        $string .= '</tbody>
              </table>';
        
        return $string;
    }

    public static function getLine($obj){
        return '
        <td class="align-middle responsive-item">'.GG::getCheckboxLine($obj->get('id')).'</td>
        '.GG::getOrderControl($obj->get('id')).'
        <td class="link-edit">'.
            GG::getLinksTable($obj->getTableName(), $obj->get('id'), $obj->get('nome'))
        .'</td>'.GG::getResponsiveList(['Nome' => $obj->nome], $obj).'
        '.GG::getActiveControl($obj->getTableName(), $obj->get('id'), $obj->get('ativo')).'
        ';
    }

    public static function filter($request) {
        $paramAdd = '1=1';
        foreach(['nome','url','descricao'] as $key){
            if($request->query($key) != ''){
                $paramAdd .= " AND `{$key}` like '%{$request->query($key)}%' ";
            }
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
            'name' => 'url',
            'label' => 'URL',
            'value' => $request->query('url'),
        ]);
        
        $string .= Form::inputText([
            'size' => 6,
            'name' => 'descricao',
            'label' => 'Descri&ccedil;&atilde;o',
            'value' => $request->query('descricao'),
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
            'label' => 'Cadastrados at&eacute',
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
}

