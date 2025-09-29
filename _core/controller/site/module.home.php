<?php
$view['title'] .= ($Config->get('slogan') != "" ? " - ".$Config->get('slogan') : "");

$view['banners'] = array();
$rs = Banner::search([
    's' => 'id',
    'w' => '(expira = 0 OR NOW() BETWEEN inicio AND fim) AND ativo=1',
    'o' => 'ordem'
]);
while($rs->next()){
    $obj = Banner::load($rs->getInt('id'));
    if($obj->getImage() != ''){
        $view['banners'][] = $obj;
    }
    unset($obj);
}
unset($rs);


$view['home-sobre'] = Estatico::getEstatico('home-sobre');

$rs2 = Estatico::search([
    's' => 'id, nome, descricao',
    'w' => "url='home-contrate'",
]);
if($rs2->next()){
$view['home-contrate'] = Estatico::load($rs2->getInt('id'));
}



