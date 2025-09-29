<?php

if($request->get('module') !=''){
    $view['title'] = "Contato - ".$view["title"];

    if($request->get('action') == 'form'){
        $view['end_scripts'] .= 'toForm();';
    }
}

