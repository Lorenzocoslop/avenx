<?php
$view['cidade'] = '';
if(!isset($view['tipo'])){
    $view['title'] = 'Publica&ccedil;&otilde;es - '.$view['title'];
    $view['titulo-pagina'] = 'Publica&ccedil;&otilde;es';
}
$view['publicacao'] = $view['publicacoes'] = [];


$id = 0;
$per_page = 15;
$qtd = 0;

$module = $request->get('module');
$action = $request->get('action');

if($action == 'list'){
    Utils::ajaxHeader();

    $page = (int)($request->get('page') != '' ? $request->get('page') : 1);
    $first = ($page-1)*$per_page;

    $rs = Publicacao::search([
        's' => 'COUNT(id) qtd',
        'w' => "ativo = 1 and id <> {$id}",
    ]);
    
    $rs->next();
    $qtd = (int) $rs->getInt('qtd');
    $pages = $qtd > 0 ? ceil($qtd/$per_page) : 0;

    $html = '';
    if($qtd == 0) $html .= '<div class="container px-5 d-flex align-items-center"> <h2 class="text-secondary fw-bold">Nenhum registro encontrado!</h2></div>';

    $rs = Publicacao::search([
        's' => 'id',
        'w' => "ativo = 1 and id <> {$id}",
        'o' => 'dt_cad DESC',
        'l' => "{$first}, {$per_page}"
    ]);

    if($qtd){

        $objPublicacao = null;
        if($id) $objPublicacao = Publicacao::load($id);

        $html .= '<div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-5">';
        while($rs->next()){
            $publicacao = Publicacao::load($rs->getInt('id'));
            $img = $publicacao->img != '' ? $publicacao->getImage('s') : '';
            $html .='    
                <div class="col">
                <article class="position-relative rounded-3 h-100" data-aos="fade-up" data-aos-delay="100">';
                if($img != ''){
                    $html .='<div class="rounded-3 ratio ratio-4x3 mb-3"><img src="'. $img .'" class="rounded-4"></div>';
                } else {
                    $html .= '<div class="d-flex align-items-center ratio ratio-4x3 mb-3 rounded-4" style="background-image: linear-gradient(to bottom, rgba(255,0,0,0), #0c3972);"><i class="icon text-white icon-32 d-flex justify-content-center" style="top: 50%; left: 50%; transform: translate(-50%, -50%);">'.file_get_contents($defaultPath."img/svg/no-image.svg").'</i></div>';
                }
                
                
                $html .='<small class="text-black-50 text-uppercase">'. Utils::dateFormat($publicacao->data, 'd/m/Y') .'</small>
                        <h3 class="fw-bold mt-2">
                            <a href="'. __PATH__.'blog/' . Publicacao::getUrl($publicacao->id) . '" class="stretched-link text-reset text-decoration-none">' . $publicacao->titulo. '</a>
                        </h3>
                    </article>
                </div>';
        }

        $html .= '</div>';

        if($pages > 1){

            $maxPages = 5;
            $inicio = $page - $maxPages;
            $fim = $page + $maxPages;

            if($inicio < 1) $inicio = 1;
            if($fim > $pages) $fim = $pages;

            
            $html .= ' <nav aria-label="Page navigation example" class="justify-content-center d-flex mt-5" >
            <ul class="pagination justify-content-end">';

                if($pages > ($maxPages*2)+1){
                    $html .= '<a class="btn btn-default btn-sm px-3 me-1 py-2 btn-outline-primary" onclick="listPublicacoes('.$id.', 1); toUp(); return false;" href="#"> << </a>';
                } 
                if($page > 1){
                    $html .= '<a class="btn btn-default btn-sm px-3 me-1 py-2 btn-outline-primary" onclick="listPublicacoes('.$id.', '.($page-1).'); toUp(); return false;" href="#"> < </a>';
                } 
                if($page - $maxPages > 1){
                    $html .= '<a class="btn btn-default btn-sm px-3 me-1 py-2 btn-outline-primary" onclick="return false;" href="#"> ... </a>';
                }

                for($i=$inicio; $i<=$fim; $i++){
                    $html .= '<a class="btn btn-default mx-1 px-3 py-2 btn-sm '.($i == $page ? ' btn-primary': ' btn-outline-primary').'" onclick="listPublicacoes('.$id.', '.$i.'); toUp(); return false;" href="#">'.($i).'</a></li>';
                }

                if($page + $maxPages < $pages ){
                    $html .= '<a class="btn btn-default btn-sm px-3 me-1 py-2 btn-outline-primary" onclick="return false;" href="#"> ... </a>';
                } 
                if($page < $pages){
                    $html .= '<a class="btn btn-default btn-sm px-3 me-1 py-2 btn-outline-primary" onclick="listPublicacoes('.$id.', '.($page+1).'); toUp(); return false;" href="#"> > </a>';
                } 
                if($pages > ($maxPages*2)+1){
                    $html .= '<a class="btn btn-default btn-sm px-3 me-1 py-2 btn-outline-primary" onclick="listPublicacoes('.$id.', '.($pages).'); toUp(); return false;" href="#"> >> </a>';
                }
            
            $html .= '
            </ul>
            </nav>
                ';

        }

    }
    // echo $html;
    // exit;
    Utils::jsonResponse('Registros obtidos com sucesso', true, ['html' => $html]);

}elseif ($action != '') {
    
    $arr = explode('-', $action);
    $id = (int) end($arr);

    if($id == 0){
        $nome = $action;
        $rs = Publicacao::search([
            's' => 'id',
            'w' => "url = '{$nome}'"
        ]);
        if($rs->next()){
            $id = $rs->getInt('id');
        }
    }
    

    if (Publicacao::exists("id={$id} AND ativo=1")) {
        $obj = Publicacao::load($id);
        $view['publicacao'] = $obj;

        if($request->get('em') != ''){
            $conn = new Connection();
            $sql = "SELECT nome, estado FROM cidades WHERE url = '{$request->get('em')}'";
            $rs = $conn->prepareStatement($sql)->executeReader();
            if($rs->next()){
                $view['cidade'] = $rs->getString('nome').'/'.$rs->getString('estado');
            }
        }

        $rs = Foto::search([
            's' => 'id',
            'w' => "id_tipo = {$id} AND tipo = '{$obj->getTableName()}'",
            'o' => 'id',
        ]);
        
        while ($rs->next()) {
            $foto = Foto::load($rs->getInt('id'));
            $imgs[] = array(
                'title' => $foto->get('descricao'),
                'href' => $foto->getImage('z',0,$foto->get('tipo').'/'.$foto->get('id_tipo')),
            ); 
    
    
            $view['thumbs'][] = [
                't' => $foto->getImage('t', 0, $foto->get('tipo').'/'.$foto->get('id_tipo').'/'),
                's' => $foto->getImage('s', 0, $foto->get('tipo').'/'.$foto->get('id_tipo').'/'),
                'r' => $foto->getImage('r', 0, $foto->get('tipo').'/'.$foto->get('id_tipo').'/'), 
                'z' => $foto->getImage('z', 0, $foto->get('tipo').'/'.$foto->get('id_tipo').'/'),
                'desc' => $foto->get('descricao'),
            ];
        }
        if (count($view['thumbs']) > 0) {
            $view['gallery'] = json_encode($imgs);
        }

        $title = $obj->get('meta_title') != '' ? $obj->get('meta_title') : $obj->get('titulo');

        $view['title'] = $title.($view['cidade']!=''? ' em '.$view['cidade'] : '').' - '.$view['title'];

        $view['keywords'] = $obj->get('meta_keys');

        $view['og'] = array(
            'url' => 'https://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'],
            'title' => Utils::replace('#<[^>]+>#', ' ', $title),
            'image' => 'https://'.$_SERVER['SERVER_NAME'].$obj->getImage('r'),
            'description' => $obj->get('meta_desc'), 
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
            $view['og']['description'] = $view['description'] = Utils::subText($obj->get('resumo'),500);
        }

        
    }else {
        $view['publicacao'] = new Publicacao();
        $view['publicacao'] -> set('titulo','Registro não encontrado');
        $view['publicacao'] -> set('resumo','<h3>Navegue abaixo para mais informações.</h3>');
    }  

}

$view['end_scripts'] .= "listPublicacoes({$id}, 1)";