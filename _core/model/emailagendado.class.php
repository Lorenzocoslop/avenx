<?php

class EmailAgendado extends Flex {

    protected $tableName = 'emailsagendados';
    protected $mapper = array(
        'id' => 'int',
        'assunto' => 'string',
        'para' => 'string',
        'dados' => 'string',
        'erro' => 'string',
        'dt_cad' => 'sql',
        'dt_env' => 'date',
    );

    protected $primaryKey = array('id');
            
    public static $configGG = array(
        'nome' => 'E-mails Agendados',
        'class' => __CLASS__,
        'ordenacao' => 'id DESC',
        'envia-arquivo' => false,
        'icon' => "ti ti-mail-check"
    );

    public static function createTable(){
        return '
        DROP TABLE IF EXISTS `emailsagendados`;
        CREATE TABLE `emailsagendados` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `assunto` varchar(255) DEFAULT NULL,
            `para` varchar(255) DEFAULT NULL,
            `dados` longtext,
            `erro` varchar(255) DEFAULT NULL,
            `dt_cad` datetime NOT NULL,
            `dt_env` datetime DEFAULT NULL,
            PRIMARY KEY(`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;';
    }
    
    public static function validate() {
        global $request;
        $error = '';
        $id = $request->getInt('id');
        $paramAdd = 'AND id NOT IN('.$id.')';
        
    }

    public static function saveForm() {
        global $request;
        $classe = __CLASS__;
        
        self::validate();
        $id = $request->getInt('id');
        $obj = new $classe(array($id));

        if ($id > 0) {
            $obj = self::load($id);
        }
        
        $obj->set('assunto', $_POST['assunto']);
        $obj->set('para', $_POST['para']);
        $obj->set('dados', $_POST['dados']);
        $obj->set('erro', $_POST['erro']);
        $obj->set('dt_env', Utils::dateValid($_POST['dt_env']) ? Utils::dateFormat($_POST['dt_env'],'Y-m-d H:i:s') : '');
        
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

        return Flex::dbDelete($obj, "id IN({$ids})");
    }

    public static function form($codigo = 0) {

        $string = "";
        $classe = __CLASS__;
        $obj = new $classe();
        $obj->set('id', $codigo);

        if ($codigo + 0 > 0) {
            $obj = self::load($codigo);
        }
        
        $string .= Form::inputText([
            'size' => 6,
            'name' => 'assunto',
            'label' => 'Assunto',
            'value' => $obj->get('assunto'),
            'required' => true,
        ]);

        $string .= Form::inputText([
            'size' => 6,
            'name' => 'para',
            'type' => 'email',
            'label' => 'Para',
            'value' => $obj->get('para'),
            'required' => true,
        ]);

        $string .= Form::inputText([
            'size' => 8,
            'name' => 'erro',
            'label' => 'Erro',
            'value' => $obj->get('erro'),
        ]);
        $string .= Form::inputText([
            'size' => 4,
            'name' => 'dt_env',
            'label' => 'Data envio',
            'value' => Utils::dateFormat($obj->get('dt_env'),'d/m/Y'),
            'class' => 'date'
        ]);
        $string .= Form::textarea([
            'size' => 12,
            'name' => 'dados',
            'label' => 'Dados Serializados',
            'value' => $obj->get('dados'),
            'attributes' => 'style="height: 200px"',
        ]);
        
        
        return $string;
    }

    public static function getTable($rs) {
        $string = '
            <table class="table lev-table table-striped">
            <thead>
              <tr>
                <th width="10">'.GG::getCheckboxHead().'</th>
                <th>Assunto</th>
                <th>Para</th>
                <th>Criado</th>
                <th>Enviado</th>
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
        <td class="link-edit">'.GG::getLinksTable($obj->getTableName(), $obj->get('id'), $obj->get('assunto')).'</td>'.GG::getResponsiveList([
            'Assunto' => $obj->get('assunto'),
            'Para' => $obj->get('para'),
            'Criado' => Utils::dateFormat($obj->get('dt_cad'),'d/m/Y H:i:s'),
            'Enviado' => Utils::dateValid($obj->get('dt_env')) ? Utils::dateFormat($obj->get('dt_cad'),'d/m/Y H:i:s') : '-',
        ], $obj).'
        <td>'.$obj->get('para').'</td>
        <td>'.Utils::dateFormat($obj->get('dt_cad'),'d/m/Y H:i:s').'</td>
        <td>'.(Utils::dateValid($obj->get('dt_env')) ? Utils::dateFormat($obj->get('dt_env'),'d/m/Y H:i:s') : '-').'</td>
        ';
    }

    public static function filter($request) {
        $paramAdd = '1=1';
        foreach(['assunto','para','erro','dados'] as $key){
            if($request->query($key) != ''){
                $paramAdd .= " AND `{$key}` like '%{$request->query($key)}%' ";
            }
        }

        if(Utils::dateValid($request->query('envio_ini'))){
            $paramAdd .= " AND DATE(`dt_env`) >= '".Utils::dateFormat($request->query('envio_ini'),'Y-m-d')."' ";
        }

        if(Utils::dateValid($request->query('envio_fim'))){
            $paramAdd .= " AND DATE(`dt_env`) >= '".Utils::dateFormat($request->query('envio_fim'),'Y-m-d')."' ";
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
            'name' => 'para',
            'label' => 'Para',
            'value' => $request->query('para'),
        ]);
        
        $string .= Form::inputText([
            'size' => 6,
            'name' => 'assunto',
            'label' => 'Assunto',
            'value' => $request->query('assunto'),
        ]);
        
        $string .= Form::inputText([
            'size' => 6,
            'name' => 'dados',
            'label' => 'Dados',
            'value' => $request->query('dados'),
        ]);

        $string .= Form::inputText([
            'size' => 6,
            'name' => 'erro',
            'label' => 'Erro',
            'value' => $request->query('erro'),
        ]);

        $string .= Form::inputText([
            'size' => 6,
            'name' => 'envio_ini',
            'label' => 'Enviados desde',
            'class' => 'date',
            'value' => $request->query('envio_ini'),
        ]);

        $string .= Form::inputText([
            'size' => 6,
            'name' => 'envio_fim',
            'label' => 'Enviados at&eacute;',
            'class' => 'date',
            'value' => $request->query('envio_fim'),
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
            'para' => 'A-Z',
            'para desc' => 'Z-A',
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
