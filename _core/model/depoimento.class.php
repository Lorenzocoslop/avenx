<?php

class Depoimento extends Flex {

    protected $tableName = 'depoimentos';
    protected $mapper = array(
        'id' => 'int',
        'nome' => 'string',
        'img' => 'string',
        'empresa' => 'string',
        'descricao' => 'string',
        'ativo' => 'int',
        'usr_cad' => 'string',
        'dt_cad' => 'sql',
        'usr_ualt' => 'string',
        'dt_ualt' => 'sql',

    );

    protected $primaryKey = array('id');
    

    public static $configGG = array(
        'nome' => 'Depoimentos',
        'class' => __CLASS__,
        'ordenacao' => 'nome ASC',
        'envia-arquivo' => true,
        'icon' => "ti ti-message-circle"
    );

    public static function createTable(){
        return '
        DROP TABLE IF EXISTS `depoimentos`;

        CREATE TABLE `depoimentos` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `nome` VARCHAR(100) NOT NULL,
            `img` VARCHAR(255) NULL,
            `empresa` VARCHAR(100) NULL,
            `descricao` TEXT NULL,
            `ativo` int(1) DEFAULT 1,
            `usr_cad` varchar(20) NOT NULL,
            `dt_cad` datetime NOT NULL,
            `usr_ualt` varchar(20) NOT NULL,
            `dt_ualt` datetime NOT NULL,
            PRIMARY KEY(`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;';
    }
    
    public static $tamImg = array(
        'thumb' => array('w'=>288,'h'=>288),
        'small' => array('w'=>576,'h'=>576),
        'regular' => array('w'=>992,'h'=>992),
        'zoom' => array('w'=>1400,'h'=>1400),
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
        $obj->set('empresa', $_POST['empresa']);
        $obj->set('descricao', $_POST['descricao']);
        $imgBefore = '';
        if (isset($_FILES['img']) && $_FILES['img']['name'] != '') {
            $imgBefore = $obj->get('img');
            $obj->set('img', Image::configureName($_FILES['img']['name']));
        }
        
        $obj->save();

        $id = $obj->get('id');
        
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
            'name' => 'empresa',
            'label' => 'Empresa',
            'value' => $obj->get('empresa'),
        ]);

        $string .= Form::inputFile([
            'size' => 12,
            'label' => 'Imagem <small class="rule">('.implode(', ',Image::$typesAllowed).')</small>',
            'type' => "file",
            'name' => 'img',
            'id' => 'input_img_'.$obj->getTableName(),
            'attributes' => 'onchange="showPreview(this, `img`, `'.$obj->getTableName().'`);"'
            ]);

            $string .= GG::getPreviewImage($obj);
        
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
                <th width="10">'.GG::getCheckboxHead().'</th>
                <th class="col-sm-12">Nome</th>
                <th width="10"></th>
              </tr>
            </thead>
            <tbody>';
        
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
        <td>'.GG::getCheckboxLine($obj->get('id')).'</td>
                <td class="link-edit">'.GG::getLinksTable($obj->getTableName(), $obj->get('id'), $obj->get('nome')).'</td>'.GG::getResponsiveList(['Nome' => $obj->nome], $obj).'
        '.GG::getActiveControl($obj->getTableName(), $obj->get('id'), $obj->get('ativo')).'
        ';
    }

    public static function filter($request) {
        $paramAdd = '1=1';
        foreach(['nome','empresa','descricao'] as $key){
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
            'name' => 'empresa',
            'label' => 'Empresa',
            'value' => $request->query('empresa'),
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
