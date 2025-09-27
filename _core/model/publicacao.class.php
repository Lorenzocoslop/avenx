<?php

class Publicacao extends Flex {

    protected $tableName = 'publicacoes';
    protected $mapper = array(
        'id' => 'int',
        'titulo' => 'string',
        'data' => 'date',
        'subtitulo' => 'string',
        'descricao' => 'string',
        'autor' => 'string',
        'fonte' => 'string',
        'img' => 'string',
        'legenda' => 'string',
        'video' => 'string',
        'meta_desc' => 'string',
        'meta_keys' => 'string',
        'meta_title' => 'string',
        'url' => 'string',
        'ativo' => 'int',
        'usr_cad' => 'string',
        'dt_cad' => 'sql',
        'usr_ualt' => 'string',
        'dt_ualt' => 'sql',

    );

    protected $primaryKey = array('id');

    public static $configGG = array(
        'nome' => 'Publica&ccedil;&otilde;es',
        'class' => __CLASS__,
        'ordenacao' => 'titulo ASC',
        'envia-arquivo' => true,
        'icon' => "ti ti-brand-blogger"
    );

    public static function createTable(){
        return '
        DROP TABLE IF EXISTS `publicacoes`;
        CREATE TABLE `publicacoes` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `titulo` VARCHAR(255) NOT NULL,
            `data` DATETIME NULL,
            `subtitulo` VARCHAR(255) NULL,
            `descricao` TEXT NULL,
            `autor` VARCHAR(100) NULL,
            `fonte` VARCHAR(255) NULL,
            `img` VARCHAR(255) NULL,
            `legenda` VARCHAR(255) NULL,
            `video` VARCHAR(255) NULL,
            `meta_desc` TEXT NULL,
            `meta_keys` VARCHAR(255) NULL,
            `meta_title` VARCHAR(255) NULL,
            `url` VARCHAR(255) NULL,
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
        global $request;
        $error = '';
        $id = $request->getInt('id');
        $paramAdd = 'AND id NOT IN('.$id.')';
        
        if($_POST['titulo'] == ''){
            throw new Exception('O campo "T&iacute;tulo" n&atilde;o foi informado');
        }

        $url = strtolower(Utils::replace('/[ ]/i','-',Utils::replace('/[^0-9A-Za-z\- ]/i','',Utils::removeDiatrics(strip_tags($_POST['url'])))));
        $url = preg_replace('/-+/', '-', trim($url));

        if($_POST['url'] != '' && self::exists('url = "'.$url.'" AND id <> '.$id.'')){
            $error .= '<li>Este URL personalizado já foi utilizado</li>';
        }

        if(!Utils::dateValid($_POST['data'].' '.$_POST['hora'].":00")){
            throw new Exception('Os campos de "Data" e "Hora" n&atilde;o foram informados corretamente');
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

    public static function getUrl($id) {
        $obj = self::load($id);

        if ($obj->get('url') != '') {
            $url = Utils::replace('/[ ]/i','-', Utils::replace('/[^0-9A-Za-z\- ]/i','', Utils::removeDiatrics(strip_tags($obj->get('url')))));
            return preg_replace('/-+/', '-', trim(strtolower($url)));
        }

        return Utils::hotUrl($id, $obj->get('titulo'));
    }

    public static function saveForm() {
        global $request, $defaultPath;
        $classe = __CLASS__;
        

        self::validate();
        $id = $request->getInt('id');
        $obj = new $classe(array($id));

        if ($id == 0) {
            $obj->set('ativo', 1);
        } else {
            $obj = self::load($id);
        }
        
        $obj->set('titulo', $_POST['titulo']);
        $obj->set('data', (Utils::dateValid($_POST['data'].' '.$_POST['hora'].":00") ? Utils::dateFormat($_POST['data'].' '.$_POST['hora'].":00",'Y-m-d H:i:s') : ''));
        $obj->set('subtitulo', $_POST['subtitulo']);
        $obj->set('descricao', $_POST['descricao']);
        $obj->set('autor', $_POST['autor']);
        $obj->set('fonte', $_POST['fonte']);
        $obj->set('legenda', $_POST['legenda']);
        $obj->set('video', $_POST['video']);
        $obj->set('meta_desc', $_POST['meta_desc']);
        $obj->set('meta_keys', $_POST['meta_keys']);
        $obj->set('meta_title', $_POST['meta_title']);

        $url = strtolower(Utils::replace('/[ ]/i','-',Utils::replace('/[^0-9A-Za-z\- ]/i','',Utils::removeDiatrics(strip_tags($_POST['url'])))));
        $url = preg_replace('/-+/', '-', trim($url));
        $obj->set('url', $url);

        $imgBefore = '';
        if (isset($_FILES['img']) && $_FILES['img']['name'] != '') {
            $imgBefore = $obj->get('img');
            $obj->set('img', Image::configureName($_FILES['img']['name']));
        }
        
        $obj->save();

        $id = $obj->get('id');
        
        if(isset($_POST["tempId"]) && (int) $_POST["tempId"] >0 ){
        
            $objUp = new Foto();
            $sql = "UPDATE {$objUp->getTableName()} SET id_tipo = {$id} WHERE tipo = '{$obj->getTableName()}' AND id_tipo = ".($_POST['tempId']+0);
            $conn = new Connection();
            $conn->prepareStatement($sql)->executeQuery();
            if(is_dir($defaultPath."uploads/".$obj->getTableName()."/".$_POST["tempId"]."/")){
                rename($defaultPath."uploads/".$obj->getTableName()."/".$_POST["tempId"]."/",  $defaultPath."uploads/".$obj->getTableName()."/".$id."/");
            }
        
        }
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
            Foto::deleteByTipo($id, $obj->getTableName());
            
            $caminho = $defaultPath."uploads/".$obj->getTableName()."/{$id}/";
            if(is_dir($caminho)){
                $ponteiro  = opendir($caminho);
                while ($nome_itens = readdir($ponteiro)) {
                    if($nome_itens != "." && $nome_itens != ".."){
                        @unlink($caminho.$nome_itens);
                    }
                }
               @rmdir($caminho);
            }
            
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
                        'name' => 'titulo',
                        'label' => 'T&iacute;tulo',
                        'value' => $obj->get('titulo'),
                        'required' => true,
                    ]);

                    $string .= Form::inputText([
                        'size' => 12,
                        'name' => 'subtitulo',
                        'label' => 'Subt&iacute;tulo',
                        'value' => $obj->get('subtitulo'),
                        'required' => true,
                    ]);

                    $string .= Form::inputText([
                        'size' => 6,
                        'name' => 'data',
                        'label' => 'Data',
                        'class' => 'date',
                        'value' => Utils::dateFormat($obj->get('data'),'d/m/Y'),
                        'required' => true,
                    ]);
                    
                    $string .= Form::inputText([
                        'size' => 6,
                        'name' => 'hora',
                        'label' => 'Hora',
                        'class' => 'shorthour',
                        'value' => Utils::dateFormat($obj->get('data'),'H:i'),
                        'required' => true,
                    ]);

                    $string .= Form::ckeditor([
                        'size' => 12,
                        'name' => 'descricao',
                        'label' => 'Descri&ccedil;&atilde;o',
                        'value' => $obj->get('descricao'),
                    ]);

                    $string .= Form::inputText([
                        'size' => 6,
                        'name' => 'autor',
                        'label' => 'Autor',
                        'value' => $obj->get('autor'),
                        'class' => 'mt-3',
                    ]);

                    $string .= Form::inputText([
                        'size' => 6,
                        'name' => 'fonte',
                        'label' => 'Fonte',
                        'value' => $obj->get('fonte'),
                        'class' => 'mt-3',
                    ]);

                    $string .= Form::inputFile([
                        'size' => 12,
                        'label' => 'Imagem',
                        'type' => "file",
                        'name' => 'img',
                        'attributes' => 'onchange="showPreview(this, `img`, `'.$obj->getTableName().'`);"'
                    ]);

                    $string .= GG::getPreviewImage($obj);

                    $string .= Form::inputText([
                        'size' => 12,
                        'name' => 'legenda',
                        'label' => 'Legenda da Imagem',
                        'value' => $obj->get('legenda'),
                    ]);

                    $string .= Form::inputText([
                        'size' => 12,
                        'name' => 'video',
                        'label' => 'V&iacute;deo (Youtube/Vimeo/Facebook)',
                        'value' => $obj->get('video'),
                    ]);
                    
                    $string .= '
                    <div 
                        class="form-group col-sm-12 uploader" 
                        data-table="'.$obj->getTableName().'" 
                        data-id="'.($obj->get('id')+0).'" 
                        data-tid="'.$codigo.'" 
                        data-type="fotos" 
                        data-retorno="uploads'.$obj->getTableName().'" 
                    >
                        <p>Seu navegador n&atilde;o suporta Flash, Silverlight ou HTML5.</p>
                    </div>
                    <div id="uploads'.$obj->getTableName().'" class="col-sm-12 mt-3"></div>
                </div>
            </div>

            <div class="tab-pane fade" id="seo'.$obj->getTableName().'">
                <div class="row">';


                    $string .= Form::inputText([
                        'size' => 4,
                        'name' => 'meta_title',
                        'label' => 'Title',
                        'value' => $obj->get('meta_title'),
                    ]);

                    $string .= Form::inputText([
                        'size' => 4,
                        'name' => 'meta_keys',
                        'label' => 'Keywords',
                        'value' => $obj->get('meta_keys'),
                    ]);

                    $string .= Form::inputText([
                        'size' => 4,
                        'name' => 'url',
                        'label' => 'URL Personalizado',
                        'value' => $obj->get('url'),
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
                <th class="col-sm-12">Titulo</th>
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
        <td class="link-edit">'.GG::getLinksTable($obj->getTableName(), $obj->get('id'), $obj->get('id').' - '.$obj->get('titulo')).'</td>'.GG::getResponsiveList(['Titulo' => $obj->titulo], $obj).'
        '.GG::getActiveControl($obj->getTableName(), $obj->get('id'), $obj->get('ativo')).'
        ';
    }

    public static function filter($request) {
        $paramAdd = '1=1';
        foreach(['titulo','subtitulo','decricao'] as $key){
            if($request->query($key) != ''){
                $paramAdd .= " AND `{$key}` like '%{$request->query($key)}%' ";
            }
        }

        if(Utils::dateValid($request->query('inicio'))){
            $paramAdd .= " AND DATE(`data`) >= '".Utils::dateFormat($request->query('inicio'),'Y-m-d')."' ";
        }

        if(Utils::dateValid($request->query('fim'))){
            $paramAdd .= " AND DATE(`fim`) <= '".Utils::dateFormat($request->query('fim'),'Y-m-d')."' ";
        }

        return $paramAdd;
    }

    public static function searchForm($request) {
        global $objSession;
        $string = '';

        $string .= Form::inputText([
            'size' => 6,
            'name' => 'titulo',
            'label' => 'T&iacute;tulo',
            'value' => $request->query('titulo'),
        ]);

        $string .= Form::inputText([
            'size' => 6,
            'name' => 'subtitulo',
            'label' => 'Subt&iacute;tulo',
            'value' => $request->query('subtitulo'),
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
