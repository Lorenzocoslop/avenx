<?php

if($request->get('module') !=''){
	$view['title'] = "Sobre - ".$view["title"];

	$rs = Estatico::search([
		's' => 'id, nome, descricao',
		'w' => "url='sobre'"
	]);
	if($rs->next()){
	  	$obj = Estatico::load($rs->getInt('id'));
	}else{
		$obj = new Estatico();
	}

	$view['sobre'] = $obj;
	$view['estatico'] = $obj;
    $view['keywords'] = $view['estatico']->get('meta_keys');

    $view['og'] = array(
	    'url' => 'https://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'],
	    'title' => Utils::replace('#<[^>]+>#', ' ', $obj->get('nome')),
	    'image' => $obj->getImage('r'),
	    'description' => $obj->get('subtitulo'), 
	    'type' => 'website',
	);

	$view['json-ld']["@context"] = "http://schema.org";
	$view['json-ld']["@type"] = "NewsArticle";
	$view['json-ld']["mainEntityOfPage"] = array(
	    '@type' => 'WebPage',
	    '@id' => 'https://'.$_SERVER['SERVER_NAME'].__PATH__,
	);
	$view['json-ld']["headline"] = $view['og']['title'];
	$view['json-ld']["description"] = $view['og']['description'];
	$view['json-ld']["datePublished"] = $obj->get('dt_cad');
	$view['json-ld']["dateModified"] = $obj->get('dt_ualt');
	$view['json-ld']["image"] = array();

	if($view['og']['image']!=''){
	    $view['json-ld']["image"][] = $obj->getImage('r');
	}

	$view['description'] = $view['og']['description'];
	if($view['og']['description'] == '') {
	    $view['og']['description'] = $view['description'] = Utils::subText($obj->get('descricao'),500);
	}

}else if($request->get('module') !='sobre'){
	$rs2 = Estatico::search([
		's' => 'id, nome, descricao',
		'w' => "url='home-sobre'",
	]);
	if($rs2->next()){
	$view['home-sobre'] = Estatico::load($rs2->getInt('id'));
	}else{
	$view['home-sobre'] = [];
	}
}



$view['empresa'] = Estatico::getEstatico('empresas-home');
$view['isps'] = Estatico::getEstatico('isps-home');
$view['operadoras'] = Estatico::getEstatico('operadoras-home');