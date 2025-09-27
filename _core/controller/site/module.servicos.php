<?php
$view['title'] = "Servi&ccedil;os - ".$view["title"];
$action = $request->get('action');
$cidade = $request->getIndex(2);
$view['servicos'] = array();
$view['servico'] =  null;

$rs = Servico::search([
    's' => 'id',
    'w' => "ativo=1",
    'o' => "nome",
]);

while($rs->next()){
    $view['servicos'][] = Servico::load($rs->getInt('id'));
}

if($action != ''){
    $parts = explode('-', $action);
    $id = (int) end($parts);

    if(Servico::exists("id='{$id}' and ativo = 1")){
        $obj = Servico::load($id);

        $view['servico'] = $obj;
        if($view['cidade'] != ''){
            $view['title'] = $obj->get('nome').' em '.$view['cidade'].' - '.$view['title'];
            $obj->set('descricao', $obj->get('descricao').'<p>Nosso consult&oacute;rio realiza atendimentos de <strong>'.$obj->get('nome').'</strong> na cidade de <strong>'.$view['cidade'].'</strong>.</p>');
        }else{
            $view['title'] = $obj->get('nome').' - '.$view['title'];
        }

        $view['og'] = array(
            'url' => 'https://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'],
            'title' => Utils::replace('#<[^>]+>#', ' ', $view['title']),
            'description' => Utils::subText($obj->get('descricao'), 200), 
            'type' => 'website',
        );
    }

    $view['servicos'] = [];
        $rs = Servico::search([
            's' => 'id',
            'w' => "ativo = 1 AND id <> ". (isset($obj->id) ? $obj->id : 0) ,
            'o' => 'nome',
        ]);
        while($rs->next()){
            $view['servicos'][] = Servico::load($rs->getInt('id'));
        }

}