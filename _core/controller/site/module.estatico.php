<?php
$rs = Estatico::search([
	's' => 'id',
	'w' => "url='{$request->get('module')}'",
]);


if($rs->numRows()>0){
    $rs->next();
    
    $obj = Estatico::load($rs->getInt('id'));
    $view['estatico'] = $obj;
    $view['keywords'] = $view['estatico']->get('meta_keys');
    $view['title'] = $view['estatico']->get('nome')." - ".$view['title'];

    $view['og'] = array(
	    'url' => 'https://'.$_SERVER['SERVER_NAME'].__PATH__.$request->get('module').'/'.$request->get('action'),
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

}else{
	header('HTTP/1.0 404 Not Found');
    $view['title'] = "P&aacute;gina n&atilde;o encontrada - ".$view['title'];
    $obj = new Estatico();
    $obj->set('nome','P&aacute;gina n&atilde;o encontrada');
    $obj->set('descricao','Navegue pelo menu dispon&iacutelvel no site!');
    $view['estatico'] = $obj;
}
