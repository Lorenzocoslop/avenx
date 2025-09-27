<?php
if($request->get('module') == 'clientes'){
	$view['title'] = "Clientes - ".$view["title"];
}

$rs = Cliente::search([
    's' => 'id,nome,img',
    'w' => 'ativo=1',
    'o' => 'nome'
]);
$view['clientes'] = array();
while($rs->next()){
    $view['clientes'][] = Cliente::load($rs->getInt('id'));
   
}

$rs = Cliente::search([
    's' => 'COUNT(id) qtd',
    'w' => 'ativo=1',
]);
$rs->next();
$qtdClientes = $rs->getInt('qtd');
$qtdPP=12;
$paginas = ceil($qtdClientes / $qtdPP);

