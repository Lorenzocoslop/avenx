<?php

class Servico extends Flex {

    protected $tableName = 'servicos';
    protected $mapper = array(
        'id' => 'int',
		'nome' => 'string',
		'img' => 'string',
		'resumo' => 'string',
		'descricao' => 'string',
		'meta_desc' => 'string',
        'meta_keys' => 'string',
        'meta_title' => 'string',
        'ativo' => 'int',
		'usr_cad' => 'string',
        'dt_cad' => 'sql',
        'usr_ualt' => 'string',
        'dt_ualt' => 'sql',

	);

    protected $primaryKey = array('id');

    public static $configGG = array(
        'nome' => 'Servi&ccedil;os',
        'class' => __CLASS__,
        'ordenacao' => 'nome ASC',
        'envia-arquivo' => true,
        'icon' => "ti ti-heart-handshake"
    );

    public static function createTable(){
        return '
        DROP TABLE IF EXISTS `servicos`;
        CREATE TABLE `servicos` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `nome` VARCHAR(100) NOT NULL,
            `img` VARCHAR(255) NULL,
            `resumo` VARCHAR(255) NULL,
            `descricao` TEXT NULL,
            `meta_desc` TEXT NULL,
            `meta_keys` VARCHAR(255) NULL,
            `meta_title` VARCHAR(255) NULL,
            `ativo` int(1) DEFAULT 1,
            `usr_cad` varchar(20) NOT NULL,
            `dt_cad` datetime NOT NULL,
            `usr_ualt` varchar(20) NOT NULL,
            `dt_ualt` datetime NOT NULL,
            PRIMARY KEY(`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;';
    }
    
	public static $tamImg = array(
        'thumb' => array('w'=>56,'h'=>56),
        'small' => array('w'=>120,'h'=>120),
        'regular' => array('w'=>480,'h'=>360),
        'zoom' => array('w'=>800,'h'=>600),
    );
    
    public static function validate() {
    	global $request;
        $error = '';
        $id = $request->getInt('id');
        $paramAdd = 'AND id NOT IN('.$id.')';
        
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
        $obj->set('resumo', $_POST['resumo']);
        $obj->set('descricao', $_POST['descricao']);
        $obj->set('meta_desc', $_POST['meta_desc']);
        $obj->set('meta_keys', $_POST['meta_keys']);
        $obj->set('meta_title', $_POST['meta_title']);
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
        
        Utils::generateSitemap();

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

        $ret = $obj->dbDelete($obj, 'id IN('.$ids.')');
        Utils::generateSitemap();
        return $ret;
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
        
    	$string .= '
    	<ul class="nav nav-tabs">

            <li class="active"><button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#dados'.$obj->getTableName().'" role="tab" aria-controls="#dados'.$obj->getTableName().'" aria-selected="true">Dados</button></li>

            <li class="active"><button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#seo'.$obj->getTableName().'" role="tab" aria-controls="#seo'.$obj->getTableName().'" aria-selected="true">SEO</button></li>
        </ul>
        
        <div class="tab-content p-4"> 
            <div class="tab-pane fade show active" id="dados'.$obj->getTableName().'">
                <div class="row">';

                    $string .= Form::inputText([
                        'size' => 12,
                        'name' => 'nome',
                        'label' => 'Nome',
                        'value' => $obj->get('nome'),
                        'required' => true,
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

                    $string .= Form::inputText([
                        'size' => 12,
                        'name' => 'resumo',
                        'label' => 'Resumo',
                        'value' => $obj->get('resumo'),
                    ]);

                    $string .= Form::ckeditor([
                        'size' => 12,
                        'name' => 'descricao',
                        'label' => 'Descri&ccedil;&atilde;o',
                        'value' => $obj->get('descricao'),
                    ]);
                    
                    $string .= '
                </div>
            </div>

            <div class="tab-pane fade" id="seo'.$obj->getTableName().'">
                <div class="row">';

                    
                $string .= Form::inputText([
                    'size' => 6,
                    'name' => 'meta_title',
                    'label' => 'Title',
                    'value' => $obj->get('meta_title'),
                ]);

                $string .= Form::inputText([
                    'size' => 6,
                    'name' => 'meta_keys',
                    'label' => 'Keywords',
                    'value' => $obj->get('meta_keys'),
                ]);

                $string .= Form::textarea([
                    'size' => 12,
                    'name' => 'meta_desc',
                    'label' => 'Description',
                    'value' => $obj->get('meta_desc'),
                    'attributes' => 'style="height:200px"',
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
        foreach(['nome','resumo','decricao'] as $key){
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
            'name' => 'resumo',
            'label' => 'Resumo',
            'value' => $request->query('resumo'),
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
}
