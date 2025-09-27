<?php

$view['title'] = "Depoimentos - ".$view["title"];

$rs = Depoimento::search([
    's' => '*',
    'w' => 'ativo=1',
    'o' => 'RAND()',
]);
$view['depoimentos'] = array();
while($rs->next()){
    $view['depoimentos'][] = Depoimento::load($rs->getInt('id'));
}