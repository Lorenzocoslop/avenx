<?php

class Lead extends Flex {

    protected $tableName = 'leads';
    protected $mapper = array(
        'id' => 'int',
		'nome' => 'string',
		'email' => 'string',
		'tel' => 'string',
		'ativo' => 'int',
		'usr_cad' => 'string',
        'dt_cad' => 'sql',
        'usr_ualt' => 'string',
        'dt_ualt' => 'sql',

	);

    protected $primaryKey = array('id');
    

    public static $configGG = array(
        'nome' => 'Leads',
        'class' => __CLASS__,
        'ordenacao' => 'nome ASC',
        'envia-arquivo' => false,
        'icon' => "ti ti-user-search"
    );

    public static function createTable(){
        return '
        DROP TABLE IF EXISTS `leads`;
        CREATE TABLE `leads` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `nome` VARCHAR(100) NOT NULL,
            `email` VARCHAR(255) NOT NULL,
            `tel` VARCHAR(20) NULL,
            `ativo` int(1) DEFAULT 1,
            `usr_cad` varchar(20) NOT NULL,
            `dt_cad` datetime NOT NULL,
            `usr_ualt` varchar(20) NOT NULL,
            `dt_ualt` datetime NOT NULL,
            PRIMARY KEY(`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;';
    }
    
    public static function validate() {
    	global $request;
        $error = '';
        $id = $request->getInt('id');
        $paramAdd = 'AND id NOT IN('.$id.')';
        
    	if($_POST['nome'] == ''){
    		throw new Exception('O campo "Nome" n&atilde;o foi informado');
    	}
    	
    	if($_POST['email'] == ''){
    		throw new Exception('O campo "E-mail" n&atilde;o foi informado');
    	}

        if(!Utils::checkEmail($_POST['email'])){
            throw new Exception('O campo "E-mail" est&aacute; inv&aacute;lido');
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
        $obj->set('email', $_POST['email']);
        $obj->set('tel', Utils::replace('/[^0-9]/','',$_POST['tel']) );
        $obj->save();

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
            'size' => 4,
            'name' => 'nome',
            'label' => 'Nome',
            'value' => $obj->get('nome'),
        ]);

		$string .= Form::inputText([
            'size' => 4,
            'name' => 'email',
            'type' => 'email',
            'label' => 'E-mail',
            'value' => $obj->get('email'),
        ]);

        $string .= Form::inputText([
            'size' => 4,
            'name' => 'tel',
            'label' => 'Telefone',
            'value' => $obj->get('tel'),
            'class' => 'phone'
        ]);
        
        return $string;
    }

    public static function getTable($rs) {
        $string = '
            <table class="table lev-table table-striped">
            <thead>
              <tr>
                <th width="10">'.GG::getCheckboxHead().'</th>
                <th class="col-sm-4">Nome</th>
                <th class="col-sm-4">Email</th>
                <th class="col-sm-4">Tel</th>
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
        <td class="link-edit">'.GG::getLinksTable($obj->getTableName(), $obj->get('id'), $obj->get('nome')).'</td>'.GG::getResponsiveList(['E-mail' => $obj->email, 'Telefone' => $obj->tel], $obj).'
        <td>'.$obj->get('email').'</td>
        <td>'.$obj->get('tel').'</td>
        '.GG::getActiveControl($obj->getTableName(), $obj->get('id'), $obj->get('ativo')).'
        ';
    }

    public function getExtraTab() {
        return '<button class="nav-link" data-bs-toggle="tab" data-bs-target="#exportNews" type="button" role="tab" aria-controls="exportNews" aria-selected="true">Exporta&ccedil;&atilde;o</button>';
    }

    public function getExtraTabContent() {
        $string = '';
        $string .= '
        <div class="tab-pane fade p-4" id="exportNews">
            <div class="row">
                <form id="formExport" onsubmit="return exportEmails(this);">
                    <div class="form-group col-sm-4 mb-3">
                        <label for="">Separador</label>
                        <select class="form-select" name="separador">
                            <option>;</option>
                            <option>,</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <button class="btn btn-primary btn-block">Exportar</button>
                    </div>
                    <div class="form-group col-sm-12 bg-gray border border-gray p-3" id="retornoEmails"></div>

                </form>
                <script>
                    function exportEmails(form){
                        $(form).ajaxSubmit({
                            url: __PATH__+`ajax/exporta-emails`,
                            type: `POST`,
                            dataType: `json`,
                            beforeSend: function() {
                                blockUi();
                            },
                            success: function(data)
                            {
                                unblockUi();
                                $(`#retornoEmails`).html(data[`data`]);

                            },
                            error: function(erro)
                            {
                                unblockUi();
                                $(`#retornoEmails`).html(erro.responseText);
                            }
                        });
                        return false;
                    }
                </script>
            </div>
        </div>';

        return $string;
    }

    public static function filter($request) {
        $paramAdd = '1=1';
        foreach(['nome','email','tel'] as $key){
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
        $string = '';
        

        $string .= Form::inputText([
            'size' => 6,
            'name' => 'nome',
            'label' => 'Nome',
            'value' => $request->query('nome'),
        ]);

        $string .= Form::inputText([
            'size' => 6,
            'name' => 'email',
            'label' => 'E-mail',
            'value' => $request->query('email'),
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
        
        return $string;
    }
}
